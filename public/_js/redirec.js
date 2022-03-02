$(document).ready(function () {

    // Pagina: redireciona_portas.php, lista todos os redirecionamentos de portas.
    // "checkbox universal": é o checkbox que apaga todos os campos
    var tableCheckBoxMsg = ""; // Mensagem de erro
    var botaoSalvar = $('#save'); // Botao salvar do popup
    var formAcao = $('#action'); // Acao ao enviar o formulario
    var old_redirec = [];
    $('input').on('focus', function () {
        $('.aviso').hide();
        $(this).removeClass('border-red');
    })

    // Funcao: Lista os dados
    function list_redirec() {

        redirec = [];
        let req = envia_padrao(null, "getRedirec", "_controler/redirec.php");

        req.done(function (msg) {
           
            msg_decode = JSON.parse(msg);
            
            for(let i in msg_decode){
                $('tbody').append(msg_decode[i])
            }
            
        })
        
    } list_redirec();
    //FIM


    // Funcao: Abre o poupop para adicao
    function add_redirec() {
        $(".body-redirec #save").off().on("click", function () {
            regexRedirec();
            requiredFields = $('input[required=required]');

            requiredFields.each(function () {
                if (this.value.length == 0) {
                    $('.aviso').show();
                    $(this).addClass('border-red');
                    vazio = true;
                } else {
                    vazio = false;
                    return;
                }
            })


            if (!vazio) {
                redirec = {}

                $('.inp_redirec').each(function (i, b, c) {
                    if (i == 1) {
                        if (this.value == "ativo") {
                            redirec[this.name] = 'a'
                        } else if (this.value == "inativo") {
                            redirec[this.name] = 'i'
                        } else {
                            redirec[this.name] = this.value
                        }
                    } else {
                        redirec[this.name] = this.value
                    }
                })

                redirec_json = JSON.stringify(redirec)
                let req = envia_padrao("redirec=" + redirec_json, "add", "_controler/redirec.php");
                page_loading(true)
                req.done(function (msg) {
                    msg_decode = JSON.parse(msg)

                    status = msg_decode["status"];
                    detalhe = msg_decode["detalhe"];

                    if (status == 1) {
                        let tr = $("<tr>", { class: 'tr_new' }).prependTo($('tbody'))
                        let td_check = $("<td>", { "class": "tdCheckbox" }).appendTo(tr)
                        $("<input>", { "type": "checkbox" }).appendTo(td_check)

                        for (let i in redirec) {
                            let td = $("<td>", { "text": redirec[i] }).appendTo(tr);

                            if (i == 'status') {
                                if (redirec[i] == "a") {
                                    $(td).text("ativo")
                                    td.addClass('text-success')
                                } else {
                                    $(td).text("inativo")
                                    td.addClass('text-danger')
                                }
                            }
                        }

                        $('.modal').modal('hide');
                        page_loading(false)
                    }
                })
            }
        })

    }// FIM


    // Funcao: Abre o popup para edicao 
    function edit_redirec(tr) {

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
            $(this).attr('data-content', tableCheckBoxMsg)
            $(this).popover('show')
        }

        $(this).on('focusout', function () {
            $(this).popover('dispose');
        })

        let checkBoxMarked = $('input:checked'); // Pega os checkbox marcados no momento

        let checkBoxTR = checkBoxMarked.parent().parent(); // Pega a TR dos checkbox selecionados

        cBoxTdContent = checkBoxTR.children().not('.tdCheckbox'); // Pega os dados da TR menos o checkbox e o id do banco

        $(cBoxTdContent).each(function (i, j, k) { // Itera sobre a linha da tabela e coloca o conteudo nos inputs de popup

            $('#formPopUp .inp_redirec').eq(i).val(this.innerHTML);
            if (i == 1) {
                if (this.innerHTML == "ativo") {
                    old_redirec[i] = "a";
                } else if (this.innerHTML == "inativo") {
                    old_redirec[i] = "i";
                } else {
                    old_redirec[i] = this.innerHTML;
                }
            } else {
                old_redirec[i] = this.innerHTML;
            }

        })


        $('.body-redirec #save').off().on('click', function () { // Ao salvar envia os dados do formulario para edição
            page_loading(true)
            redirec = {}
            $('.inp_redirec').each(function (i, b, c) {
                if (i == 1) {
                    if (this.value == "ativo") {
                        redirec[this.name] = 'a'
                    } else if (this.value == "inativo") {
                        redirec[this.name] = 'i'
                    } else {
                        redirec[this.name] = this.value
                    }
                } else {
                    redirec[this.name] = this.value
                }
            })

            redirec_json = JSON.stringify(redirec)
            old_redirec_json = JSON.stringify(old_redirec)


            let req = envia_padrao("redirec=" + redirec_json + "&old_redirec=" + old_redirec_json + "&nivelTR=" + checkBoxTR.attr('class'), "edit", "_controler/redirec.php");
            
            req.done(function (msg) {
                msg_decode = JSON.parse(msg)

                status = msg_decode["status"];
                detalhe = msg_decode["detalhe"];

                if (status == 1) {
                    redirec = [];
                    $('.inp_redirec').each(function (indice, b, c) { redirec[indice] = this.value })
                    if (redirec[1] == "ativo") {
                        $(cBoxTdContent[1]).removeClass('text-danger');
                        $(cBoxTdContent[1]).addClass('text-success');
                    } else {
                        $(cBoxTdContent[1]).removeClass('text-success');
                        $(cBoxTdContent[1]).addClass('text-danger');
                    }
                    $(cBoxTdContent).each(function (i, j, k) { // Itera sobre a linha da tabela e coloca o conteudo nos inputs de popup	
                        $(this).text(redirec[i])
                    })

                    $(checkBoxTR).addClass('tr_new')
                    $('.modal').modal('hide');
                    page_loading(false)
                }
            })
        });
    }// FIM


    // Funcao: Abre o popup para remoção 
    function del_redirec() {
        page_loading(true);
        redirec = {}
        $('.inp_redirec').each(function (indice, b, c) { redirec[this.name] = this.value })

        redirec_json = JSON.stringify(redirec)
        let checkboxCheckedCount = $('input:checked:not(#checkMain)');
        var checkBoxMarked = $('input:checked:not(#checkMain)'); // Pega os checkbox marcados no momento
        var checkBoxMainMarked = $('#checkMain').is(":checked"); // Pega os checkbox marcados no momento
        let tableItem = $(checkBoxMarked).not("#checkMain").parent().parent();
        let nivel_tabela = $('.body-redirec tbody tr').attr('class');
        let checkBoxTR = checkBoxMarked.parent().parent(); // Pega a TR dos checkbox selecionados

        $(this).on('focusout', function () {
            $(this).popover('dispose');
        });

        if (checkBoxMarked.length == 0) { // Tratamento de erros

            var tableCheckBoxMsg = "Selecione pelo menos um item para exclusão !";
            if (tableCheckBoxMsg != "") {
                $(this).attr('data-content', tableCheckBoxMsg);
                $(this).popover('show');
            }

        } else if (checkboxCheckedCount.length > 1) {
            tableCheckBoxMsg = "Selecione apenas um item para remoção !";

            if (tableCheckBoxMsg != "") {
                $(this).attr('data-content', tableCheckBoxMsg);
                $(this).popover('show');
            }
        } else {

            let resposta = confirm('Deseja realmente excluir o item selecionado? ');

            if (resposta == true) {
                
                redirec = {}
                $(tableItem).children().not('.tdCheckbox').each(function (i, b, c) {
                    let texto = this.innerText.toLowerCase()
                    if (i == 1) {
                        if (texto == "ativo") {
                            redirec[i] = 'a'
                        } else if (texto == "inativo") {
                            redirec[i] = 'i'
                        } else {
                            redirec[i] = texto
                        }
                    } else {
                        redirec[i] = texto
                    }
                })

                
                redirec_json = JSON.stringify(redirec)
                let req = envia_padrao("redirec=" + redirec_json + "&nivelTR=" + checkBoxTR.attr('class'), "del", "_controler/redirec.php");

                req.done(function (msg) {
                    console.log(msg)
                    $(tableItem).remove();
                    page_loading(false)
                })
            }
        }

    } // FIM


    // Funcao: Filtra dados do formulario de redirecionamento
    function regexRedirec() {
        let status = false;
        let inputOrigem = document.getElementById("popupInp2");
        let inputDestino = document.getElementById("popupInp4");

        let regExpCIDR = /\/[0-9]/;
        let regOrigem = regExpCIDR.test(inputOrigem.value);
        let regDestino = regExpCIDR.test(inputDestino.value);

        if (regOrigem == false && inputOrigem.value != '' && inputOrigem.value != ' ') {
            inputOrigem.value += "/32"
        }
        if (regDestino == false && inputDestino.value != '' && inputDestino.value != ' ') {
            inputDestino.value += '/32'
        }
    }



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

    $('.body-redirec .add').on('click', function () { add_redirec(); })
    $('.body-redirec .edit').on('click', function () { edit_redirec(); })
    $('.body-redirec .delete').on('click', function (e) { del_redirec(); })
})