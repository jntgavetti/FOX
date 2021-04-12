$('.body-users .div_info_fw #todos').on('click', function () {
    let marcado = $(this).is(':checked');
    if (marcado) {
        $('.div_info_fw input').not(this).prop('checked', false);
        $('.div_info_fw input').not(this).prop('disabled', true);
    } else {
        $('.div_info_fw input').not(this).prop('disabled', false);
    }
})

$('.div_info_fw input').on('click', function () {
    let todas_portas = $('.body-users .div_info_fw #todos');
    let marcado = $('.div_info_fw input').not(todas_portas).is(':checked');
    if (marcado) {
        todas_portas.prop('disabled', true);
        todas_portas.prop('checked', false);
    } else {
        todas_portas.prop('disabled', false);
    }
})

$(document).ready(function () {
    // Pagina: listagem_grupos.php, Lista os grupos do squid.


    var div_linha_padrao = $('.div_topico_padrao .div_info_linha').clone();

    var sys_op = true;

    $('#g_default a').on('click', function () {
        $('.body-groups .btn_add_group').trigger('click');
    })


    function getDoor(prot, canvas) {

        var linha = "";

        req = envia_padrao('protocol=' + prot, 'getDoors', '_controler/servicos.php')
        req.done(function (msg) {
            
            if (msg.length != 0) {
                msg = JSON.parse(msg)

                for (prot in msg) {
                    linha += msg[prot]
                }

                $(`.body-groups ${canvas} .div_controle_topicos`).html(linha)

            }

        })
    }

    function getProtocol() {

        let req = envia_padrao(null, "getProtocol", "_controler/servicos.php");

        req.done(function (msg) {

            if (msg.length != 0) {
                let msg_decode = JSON.parse(msg);

                if (msg_decode.length != 0) {
                    $('#g_default').hide();

                    for (prot in msg_decode) {
                        protocolo = msg_decode[prot];
                        div = $('<div>', { class: 'div_grupo' })
                        //i_edit = $('<i>', { class: 'fa fa-edit edit' }).appendTo(div)
                        p = $('<p>', { class: 'tit_grupo d-inline', id: protocolo.toLowerCase(), text: protocolo.toUpperCase() }).appendTo(div)
                        $('.body-groups .grupos_perso').append(div);
                        sys_op = true
                    }
                } else {
                    $('#g_default').show()
                    $('.btn_save, .btn_apply, .btn_delete').addClass('disabled')
                    sys_op = false
                }
            } else {
                $('#g_default').show()
                $('.btn_save, .btn_apply, .btn_delete').addClass('disabled')
                sys_op = false
            }


            protocolo = $('.body-groups .grupos_perso :nth-child(2) .tit_grupo').prop('id');
            $('.body-groups .grupos_perso :nth-child(2)').addClass('active');
            getDoor(protocolo, ' .div_lista_sites');

        })
    }
    getProtocol()

    /* Cria um novo input */
    function cria_linha(pai) {
        let clone_div_info = div_linha_padrao.clone();
        clone_div_info.find('input').val()
        clone_div_info.find('input').addClass('orfao')
        site_count++;
        return clone_div_info;
    }

    // Recebe as informações de acordo com o grupo selecionado
    $(document).on('click', '.body-groups .div_grupo:not(.div_grupo_novo)', function () {

        if (novo_grupo_status == true) {
            if (!novo_grupo_status) {
                novo_grupo_status = true;
                $('.div_grupo_novo.active').remove();
            }

            $('.div_grupo').removeClass('active');
            $(this).addClass('active');
            protocol = $(this).find('.tit_grupo').prop('id');
            getDoor(protocol, '.div_lista_sites');
        }
    })

    // Adiciona grupos
    var novo_grupo_status = true;
    $('.body-groups .btn_add_group').on('click', function () {
        $('#g_default').hide()
        $('.btn_confirm').trigger('click')
        if (novo_grupo_status) {
            let div_grupo = $('<div>', { class: 'div_grupo div_grupo_novo active bg-transparent' })
            let srow = $('<div>', { class: 'row ml-1' }).appendTo(div_grupo)
            let input = $('<input>', { placeholder: "Novo grupo", class: 'form-control form-control-sm col-4' }).appendTo(srow)
            let confirm = $('<i>', { id: "btn_confirm", class: "btn_confirm fa fa-check-circle ml-2 mt-2" }).appendTo(srow)
            //let edit = $('<i>', { class: 'fa fa-edit edit disabled' }).appendTo(div_grupo)


            $('.grupos_niveis').removeClass('visible')
            $('.grupos_perso').addClass('visible')
            $('.div_grupo').removeClass('active')
            $('.grupos_perso').append(div_grupo);
            $(input).focus();
            novo_grupo_status = false
        } else {
            validaInput(0, '.div_grupo.active input')
        }
    })
    // ---------------------------------------------------------- //


    // Edita grupos
    var oldGroup;
    $(document).on('click', '.body-groups .div_grupo.active .edit:not(.disabled)', function () {
        novo_grupo_status = false
        $(this).addClass('disabled')
        let div = $('.div_grupo.active').addClass('bg-transparent');
        p = $('.div_grupo.active .tit_grupo');
        let srow = $('<div>', { class: 'row ml-1' }).appendTo(div)
        let input = $('<input>', { class: 'form-control form-control-sm col-4 d-inline', value: p.text() }).appendTo(srow);
        let confirm = $('<i>', { id: "btn_confirm", class: "btn_confirm fa fa-check-circle ml-2 mt-2" }).appendTo(srow)
        oldGroup = p.text().toLowerCase();
        p.remove();
    })
    // ---------------------------------------------------------- //


    // Confirma alterações
    $(document).on('click', '.body-groups .btn_confirm', function () {
        let grupo = $('.div_grupo.active input').val();

        if (grupo.length != 0) {
            var igual;

            $('.tit_grupo').each(function () {
                if (grupo.toLowerCase() == this.id) { igual = true }
            })
            if (!igual) {
                $('<p>', { class: 'tit_grupo d-inline', 'data-old': oldGroup, id: grupo.toLowerCase(), text: grupo.toUpperCase() }).appendTo($('.div_grupo.active'));
                $('.div_grupo.active').removeClass('bg-transparent').removeClass('div_grupo_novo');
                $('.btn_save, .btn_apply, .btn_delete').removeClass('disabled')
                $(this).remove();
                $('.div_grupo.active input').remove();
                $('.div_grupo.active .row').remove();
                $('.div_grupo.active .edit').removeClass('disabled');
                novo_grupo_status = true;
                sys_op = true;
                $('.div_grupo.active').trigger('click')
            } else {
                validaInput(0, '.div_grupo.active input')
            }

        } else {
            validaInput(0, '.div_grupo.active input')
        }
    })

    // Excluir grupos
    var perso, nivel = false;
    $(document).on('click', '.body-groups .btn_delete:not(.disabled)', function () {

        if (sys_op) {
            div_grupos = $('.grupos_perso .div_grupo');
            div_grupo = $('.div_grupo.active');
            grupo = $('.div_grupo.active p').prop('id').replace(' ', '-');


            perso = true;

            if (!novo_grupo_status) {
                div_grupo.remove()
                novo_grupo_status = true;
            } else {
                let resp = confirm("Tem certeza que deseja remover esse serviço e suas portas?");

                if (resp) {
                    let req = envia_padrao('grupo=' + grupo, 'excluir', '_controler/servicos.php');
                    req.done(function (msg) {

                        if (msg.length != 0) {
                            msg_decode = JSON.parse(msg)
                            status = msg_decode["status"];
                            detalhe = msg_decode["detalhe"];

                            if (status == 1) {
                                exibe_mudancas(1, 'Êxito ao remover grupo.')
                                div_grupo.remove()


                                $('.grupos_perso :nth-child(2)').addClass('active');
                                $('.div_grupo.active').trigger('click');

                                if ($('.grupos_perso .div_grupo').length == 0) {
                                    $('#g_default').show();
                                    $('.btn_save, .btn_apply, .btn_delete').addClass('disabled')
                                    sys_op = false
                                }
                            } else {
                                exibe_mudancas(0, 'Houve falhas ao tentar remover grupo', detalhe)
                            }
                        }
                    })
                }

            }

        }
    })
    // ---------------------------------------------------------- //


    // Salva alterações
    $(document).on('click', '.body-groups .btn_save:not(.disabled)', function () {
        if (sys_op) {
            $(".btn_confirm").trigger("click");
            if (novo_grupo_status) {
                let inputs = $('.div_lista_sites .linha_topico');
                let valida = 0;
                let doors = [];
                let prot = $('.div_grupo.active p').prop('id');
                var old_prot = $('.div_grupo.active p').attr('data-old');


                for (let i = 0; i < inputs.length; i++) {
                   
                    let linha = inputs[i].value;
                    let test = /^[0-9]{1,5}$|^[0-9]{1,5}:[0-9]{1,5}$/.exec(linha)
                    if (!test) {
                        valida += 1
                        validaInput(0, inputs[i]);
                    } else {
                        valida += 0
                    }
                    doors[i] = inputs[i].value;
                }

                if (valida == 0) {

                    servico = JSON.stringify({ old_prot, prot, doors });

                    let req = envia_padrao("servico=" + servico, 'salvar', '_controler/servicos.php')

                    req.done(function (msg) {

                        msg_decode = JSON.parse(msg)
                        console.log(msg_decode)
                        status = msg_decode["status"];
                        detalhe = msg_decode["detalhe"];
    
                        if (status == 1) {
                            exibe_mudancas(1, 'Êxito ao salvar alterações.', detalhe)
                        } else {
                            exibe_mudancas(0, 'Houve falhas ao tentar salvar alterações', detalhe)
                        }
                    })
                }

            }
        }
    })
    // ---------------------------------------------------------- //


    // Aplica alterações
    $(document).on('click', '.body-groups .btn_apply:not(.disabled)', function () {
        if (sys_op) {
            modal_loading(true)

            setTimeout(() => {
                let req = envia_padrao(null, 'aplicar', '_controler/servicos.php')
                req.done(function (msg) {
                    modal_loading(false)
                    if (msg.length != 0) {

                        msg_decode = JSON.parse(msg)
                        status = msg_decode["status"];
                        detalhe = msg_decode["detalhe"];

                        if (status == 1) {
                            exibe_mudancas(1, "Êxito ao aplicar alterações.");
                        } else {
                            exibe_mudancas(0, "Falha ao aplicar alterações.", detalhe);
                        }
                    }
                })
            }, 3000);

        }

    })
    // ---------------------------------------------------------- //



    // Controla o comportamento dos inputs
    $(document).on('focus', '.body-groups .div_alterna input[type=text]', function () {
        let inp = $(this);
        let div_crud = inp.siblings()
        div_crud.css('display', 'inline');
        $('.div_alterna input').removeClass('wrong')
    })
    $(document).on('blur', '.body-groups .div_alterna input[type=text]', function () {
        let this_inp = $(this);
        let inp = $('.div_alterna input');
        let div_crud = this_inp.siblings().not('span')


        if (this_inp.val().length == 0) {
            this_inp.removeClass('defou').addClass('wrong')
        } else {
            this_inp.removeClass('wrong').addClass('defou')
        }

        div_crud.css('display', 'none');
    })
    // ---------------------------------------------------------- //


    // Adiciona inputs novos ao topico existente
    var site_count = 0;
    $(document).on('click', '.body-groups .div_info_linha .btn_add', function () {
        let nova_div = cria_linha();
        let div_info = $(this).parent();
        let avo = $(this).parent().parent();
        let novo_input = nova_div.find('input');

        if (avo.hasClass('div_topico')) {
            avo.append(nova_div);
            novo_input.removeClass('orfao')
        } else {
            div_info.after(nova_div)
        }

    })

    $(document).on('click', '.body-groups .btn_add_tp', function () {

        let div_alterna = $('.div_alterna.active');

        if (!div_alterna.hasClass('div_lista_ext')) {
            let nova_div = cria_linha();
            let g_sel = $('.div_grupo.active p').prop('id');
            let novo_input = nova_div.find('input');
            let acl_icon = nova_div.find('.acl_icon')
            let acl_deny = nova_div.find('.deny')
            let acl_allow = nova_div.find('.allow')
            acl_icon.removeClass('d-none')

            if (g_sel == "nivel-a" || g_sel == "nivel-b" || g_sel == "nivel-c" || g_sel == "nivel-d" || g_sel == "todos-niveis") {
                acl_allow.addClass('d-none');
            } else {
                acl_deny.addClass('d-none');
            }


            novo_input.removeClass('orfao')
            $(this).parent().parent().append(nova_div);
        }

    })
    // ---------------------------------------------------------- //

    // Remove inputs do topico selecionado
    $(document).on('click', '.body-groups .div_info_linha .btn_del', function () {
        let div_info = $(this).parent();
        let qtd_inputs = div_info.parent().find('input.linha_topico').length;


        if (qtd_inputs > 1) {
            div_info.remove();
        } else {
            div_info.find('input').val('').focus()
        }

    })
    // ---------------------------------------------------------- //


})
/* ============================================================================= */
