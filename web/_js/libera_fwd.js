$(document).ready(function () {

    // "checkbox universal": é o checkbox que apaga todos os campos
    var tableCheckBoxMsg = ""; // Mensagem de erro
    var botaoSalvar = $('#save'); // Botao salvar do popup
    var formAcao = $('#action'); // Acao ao enviar o formulario


    $('input').on('focus', function () {
        $('.aviso').hide();
        $(this).removeClass('border-red');
    })

    function troca_texto(i, texto) {
        switch (i) {
            case 1:
                if (texto == "unidirecional") {
                    texto = 'u'
                }

                if (texto == "bidirecional") {
                    texto = 'b'
                }
                break;
            case 2:
                if (texto == "todos") { texto = "tcp/udp/icmp" }
                break;
            case 3:
            case 5:
                if (texto == "qualquer") {
                    texto = '+'
                }
                break;

            case 4:
                if (texto == "qualquer") {
                    texto = "0.0.0.0/0"
                }
                break;
        }

        return texto;
    }

    // Funcao: Lista os dados
    function list(data) {
        let req = envia_padrao("req=" + data, "get", "_controler/libera_fwd.php");


        req.done(function (msg) {
            if (msg.length != 0) {
                msg_decode = JSON.parse(msg);

                if (msg_decode.length != 0) {

                    if (data == null) {
                        for (let i in msg_decode) {
                            $('tbody').append(msg_decode[i])
                        }
                    } else {
                        select_placa = $('#data-4, #data-6');
                        select_redes = $('#data-5, #data-7');

                        placas = msg_decode.placas;
                        redes = msg_decode.redes;

                        for (let placa in placas) {
                            $('<option>', { value: placas[placa], text: placas[placa] }).prependTo(select_placa)
                        }

                        for (let rede in redes) {
                            $('<option>', { value: redes[rede], text: redes[rede] }).prependTo(select_redes)
                        }
                    }
                }

            }



        })


    }
    list(null); list("net");

    //FIM


    // Funcao: Abre o poupop para adicao
    function add() {
        vazio = 0;
        status_reg = 0;
        $('#formPopUp')[0].reset()
        $('.aviso').hide();
        $('input').removeClass('border-red');


        $("#save").off().on("click", function () {

            requiredFields = $('input[required=required]');

            requiredFields.each(function () {
                if (this.value.length == 0) {
                    $('p.aviso').text('Campos obrigatórios em branco.');
                    $('.aviso').show();
                    $(this).addClass('border-red');
                    vazio++;
                }
            })

            let ip_destino = $('#popupInp7');
            let reg = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}($|\/\d{1,2})/;

            if (reg.test(ip_destino.val()) == true) {
                status_reg = true;
            } else {
                status_reg = false;
                ip_destino.addClass('border-red')
            }

            if (vazio == 0 && status_reg == true) {
                obj_form = {}

                $('.inp').each(function (i, b, c) {

                    let prop = this.name.toLowerCase();
                    let texto = this.value.toLowerCase();

                    texto = troca_texto(i, texto);

                    obj_form[prop] = texto;
                })


                form_json = encodeURIComponent(JSON.stringify(obj_form))


                let req = envia_padrao("form_json=" + form_json, "add", "_controler/libera_fwd.php");

                page_loading(true)
                req.done(function (msg) {

                    msg_decode = JSON.parse(msg)

                    status = msg_decode["status"];
                    detalhe = msg_decode["detalhe"];

                    if (status == 1) {
                        let tr = $("<tr>", { class: 'tr_new' }).prependTo($('tbody'))
                        let td_check = $("<td>", { "class": "tdCheckbox" }).appendTo(tr)
                        $("<input>", { "type": "checkbox" }).appendTo(td_check)

                        for (let i in obj_form) {
                            let td = $("<td>").appendTo(tr);
                            let texto = obj_form[i].toLowerCase();


                            switch (i) {
                                case 'fluxo':
                                    if (texto == "u") {
                                        texto = 'unidirecional'
                                    }

                                    if (texto == "b") {
                                        texto = 'bidirecional'
                                    }
                                    break;
                                case 'protocolo':
                                    if (texto == "tcp/udp/icmp") { texto = "todos" }
                                    break;
                                case 'int-origem':
                                case 'int-destino':
                                    if (texto == "+") {
                                        texto = 'qualquer'
                                    }
                                    break;

                                case 'ip-origem':
                                    if (texto == "0.0.0.0/0" || texto == "0/0") {
                                        texto = "qualquer"
                                    }
                                    break;
                            }

                            $(td).text(texto)

                        }

                        $('.modal').modal('hide');
                        page_loading(false)
                    }

                })

            }
        })

    }// FIM


    // Funcao: Abre o popup para edicao 
    function edit() {

        vazio = 0;
        status_reg = false;
        botaoSalvar.removeClass('disabled');
        formAcao.attr('value', 'edit');
        $('tr').removeClass('tr_new')



        // Tratamento de erros

        let checkboxCheckedCount = $('input:checked:not(#checkMain)');
        if (checkboxCheckedCount.length == 0) { tableCheckBoxMsg = "Selecione pelo menos um item para edição !"; } else {
            if (checkboxCheckedCount.length > 1) {
                tableCheckBoxMsg = "Selecione apenas um item para edição !";
            } else {
                tableCheckBoxMsg = "";
            }
        }


        if (tableCheckBoxMsg != "") {
            alert(tableCheckBoxMsg)
        } else {
            let checkBoxMarked = $('input:checked'); // Pega os checkbox marcados no momento

            let checkBoxTR = checkBoxMarked.parent().parent(); // Pega a TR dos checkbox selecionados

            cBoxTdContent = checkBoxTR.children().not('.tdCheckbox'); // Pega os dados da TR menos o checkbox e o id do banco

            old_form = [];
            $(cBoxTdContent).each(function (i, j, k) { // Itera sobre a linha da tabela e coloca o conteudo nos inputs de popup
                $('#formPopUp .inp').eq(i).val(this.innerHTML);

                let texto = this.innerHTML.toLowerCase();

                texto = troca_texto(i, texto);
                old_form[i] = texto;

            })


            $('#save').off().on('click', function () { // Ao salvar envia os dados do formulario para edição

                requiredFields = $('input[required=required]');

                requiredFields.each(function () {
                    if (this.value.length == 0) {
                        $('p.aviso').text('Campos obrigatórios em branco.');
                        $('.aviso').show();
                        $(this).addClass('border-red');
                        vazio++;
                    }
                })


                let ip_destino = $('#popupInp7');
                let reg = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}($|\/\d{1,2})/;

                if (reg.test(ip_destino.val()) == true) {
                    status_reg = true;
                } else {
                    status_reg = false;
                    ip_destino.addClass('border-red');
                }



                if (vazio == 0 && status_reg == true) {
                    page_loading(true)
                    form = {}
                    $('.inp').each(function (i, b, c) {

                        let prop = this.name.toLowerCase();
                        let texto = this.value.toLowerCase();

                        texto = troca_texto(i, texto);

                        form[prop] = texto;
                    })

                    form_json = encodeURIComponent(JSON.stringify(form))
                    old_form_json = encodeURIComponent(JSON.stringify(old_form))


                    let req = envia_padrao("form_json=" + form_json + "&old_form_json=" + old_form_json, "edit", "_controler/libera_fwd.php");

                    req.done(function (msg) {
                        msg_decode = JSON.parse(msg)

                        status = msg_decode["status"];
                        detalhe = msg_decode["detalhe"];

                        if (status == 1) {
                            form = [];
                            $('.inp').each(function (indice, b, c) { form[indice] = this.value })

                            $(cBoxTdContent).each(function (i, j, k) { // Itera sobre a linha da tabela e coloca o conteudo nos inputs de popup	
                                $(this).text(form[i])
                            })

                            $(checkBoxTR).addClass('tr_new')
                            $('.modal').modal('hide');
                            $(":checkbox").prop('checked', false)
                            page_loading(false)
                        }
                    })
                }
            });

        }
    }// FIM


    // Funcao: Abre o popup para remoção 
    function del() {
        let checkboxCheckedCount = $('input:checked:not(#checkMain)');
        var checkBoxMarked = $('input:checked:not(#checkMain)'); // Pega os checkbox marcados no momento
        let tableItem = $(checkBoxMarked).not("#checkMain").parent().parent();

        if (checkBoxMarked.length == 0) { // Tratamento de erros

            var tableCheckBoxMsg = "Selecione pelo menos um item para exclusão !";
            if (tableCheckBoxMsg != "") {
                alert(tableCheckBoxMsg);
            }

        } else if (checkboxCheckedCount.length > 1) {
            tableCheckBoxMsg = "Selecione apenas um item para remoção !";

            if (tableCheckBoxMsg != "") {
                alert(tableCheckBoxMsg);
            }
        } else {

            let resposta = confirm('Deseja realmente excluir o item selecionado? ');

            if (resposta == true) {
                page_loading(true);
                arr_form = [];
                $(tableItem).children().not('.tdCheckbox').each(function (i, b, c) {

                    let texto = this.innerHTML.toLowerCase();

                    texto = troca_texto(i, texto);

                    arr_form[i] = texto;

                })


                form_json = encodeURIComponent(JSON.stringify(arr_form))
                let req = envia_padrao("form_json=" + form_json, "del", "_controler/libera_fwd.php");

                req.done(function (msg) {
                    $(tableItem).remove();
                    page_loading(false)
                })
            }
        }

    } // FIM


    // Função: Seleciona todos os campos da tabela
    $('th:nth-child(1), th:nth-child(1) input').on('click', function () {
        let checkboxsMarcados = $(':checkbox').is(":checked");
        if (checkboxsMarcados == true) { $(':checkbox:not(.disabled)').prop("checked", false); } else { $(':checkbox:not(.disabled)').prop("checked", true); }
    })
    // Fim


    // Funcao: Marca e desmarca o checkbox ao clicar no item da tabela
    $(document).on('click', 'tr:not(.disabled) td:nth-child(1) input', function () { $(this).each(function () { this.checked = !this.checked; }); })
    $(document).on('click', 'tr:not(.disabled) td', function () { $(this).parent().children().eq(0).children().each(function () { this.checked = !this.checked; }) })
    // Fim


    // Funcao: Verifica se o checkbox está marcado para permitir edição
    $(document).on('click', 'tr.disabled', function (e) { e.preventDefault(); })
    $(document).on('click', 'tr:not(.disabled) td,th:nth-child(1)', function () {

        var checkboxMarcados = $(':checkbox:not(#checkMain)').is(":checked")
        var QTDcheckboxMarcados = $('input:checked:not(#checkMain)')


        if (!checkboxMarcados || QTDcheckboxMarcados.length > 1) { // Se nao houver checkbox marcado ou se houver mais de 1 desativa o botao
            $('.edit').attr('data-toggle', '')
            $('.edit').addClass('disabled')
            $('.edit').attr('data-toggle', '')
        } else { // Se nao ativa o botao
            $('.edit, .delete').removeClass('disabled')
            $('.edit').attr('data-toggle', 'modal')
        }
        if (!checkboxMarcados) {
            $('.delete').addClass('disabled')
        } else {
            $('.delete').removeClass('disabled')

        }

    })
    // Fim



    $('.add').on('click', function () { add(); })
    $('.edit').on('click', function () { edit(); })
    $('.delete').on('click', function (e) { del(); })
})