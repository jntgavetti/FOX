$(document).ready(function () {
    // Pagina: listagem_grupos.php, Lista os grupos do squid.

    var topico_padrao = $('.div_topico_padrao');
    var div_linha_padrao = $('.div_topico_padrao .div_info_linha').clone();
    topico_padrao.removeClass('div_topico_padrao')
    var sys_op = true;

    $('#g_default a').on('click', function () {
        $('.body-groups .btn_add_group').trigger('click');
    })


    // Recebe dados sobre o grupo selecionado
    function recebe_SIP(arq, categoria, canvas) { // Recebe SITES, IPS OU PALAVRAS = recebe_SIP :D
        var linha = "";

        req = envia_padrao('arquivo=' + arq, 'lista_categoria', '_controler/grupos_squid.php')
        req.done(function (msg) {
            msg = JSON.parse(msg)
            conteudo = msg[categoria];

            if (conteudo.length != 0) {
                for (indice in conteudo) {
                    linha += conteudo[indice]
                }

                $(`.body-groups ${canvas} .div_controle_topicos`).html(linha)
                orfao = $('.body-groups .orfao');


                orfao.each(function () {
                    temPai = $(this).parent().parent().hasClass('div_topico');
                    if (temPai) { $(this).removeClass('orfao') }
                })


                let tit_top = $('.titulo_topico')

                for (let i = 0; i < tit_top.length; i++) {
                    tit_top[i].value = tit_top[i].value.replace('#', '');
                }
            } else {
                topico_padrao_clone = topico_padrao.clone();
                topico_padrao_clone.find('.titulo_topico').val("PADRAO")

                $(`.body-groups ${canvas} .div_controle_topicos`).html(topico_padrao_clone);
            }

        })
    }

    function lista_grupos() {

        let req = envia_padrao(null, "lista_grupos", "_controler/grupos_squid.php");

        req.done(function (msg) {
           
            if (msg.length != 0) {
                let msg_decode = JSON.parse(msg);
                
                if(msg_decode.length != 0){
                    $('#g_default').hide();

                    for (grupo in msg_decode) {
                        grupo = msg_decode[grupo];
                        div = $('<div>', { class: 'div_grupo' })
                        grupo = grupo.replace('-', ' ')
                        i_edit = $('<i>', { class: 'fa fa-edit edit' }).appendTo(div)
                        p = $('<p>', { class: 'tit_grupo d-inline', id: grupo.toLowerCase(), text: grupo.toUpperCase() }).appendTo(div)
                        $('.body-groups .grupos_perso').append(div);
                        sys_op = true
                    }
                }else{
                    $('#g_default').show()
                    $('.btn_save, .btn_apply, .btn_delete').addClass('disabled')
                    sys_op = false
                }
            } else {
                $('#g_default').show()
                $('.btn_save, .btn_apply, .btn_delete').addClass('disabled')
                sys_op = false
            }


            p_grupo = $('.body-groups .grupos_perso :nth-child(2) .tit_grupo').prop('id');
            $('.body-groups .grupos_perso :nth-child(2)').addClass('active');
            recebe_SIP(p_grupo, 'sites', ' .div_lista_sites');
            recebe_SIP(p_grupo, 'palavras', ' .div_lista_palavras');
            recebe_SIP(p_grupo, 'ips', ' .div_lista_ips');

        })
    }
    lista_grupos()




    /* Cria um novo input */
    function cria_linha(pai) {
        let clone_div_info = div_linha_padrao.clone();
        clone_div_info.find('input').val()
        clone_div_info.find('input').addClass('orfao')
        site_count++;
        return clone_div_info;
    }

    // Elemento arrastável
    $(document).on('mousedown', '.body-groups .div_info_linha .btn_move', function (e) {
        let divs = $(this).closest('.div_topico').find('.div_info_linha').length;
        if (divs > 1) {
            $(this).parent().attr('id', 'drag')
            dragElement(e, 'drag')

            setInterval(function () {
                $('.draggable svg').animate({ marginTop: '+=5px' })
                $('.draggable svg').animate({ marginTop: '-=5px' })
            }, 0)
        }
    })

    $(document).on('click', '.body-groups .ul_alterna_div li:not(.disabled) a', function () {
        if (this.id == "lista_ext") {
            $('.btn_add_topico, .btn_add_tp').hide();
        } else {
            $('.btn_add_topico, .btn_add_tp').show();
        }

        $(this).siblings().hide();
        $('.body-groups .div_alterna').hide();
        $('.body-groups .div_alterna').removeClass('active');
        $('.body-groups .ul_alterna_div .active').removeClass('active');

        if (this.id == "lista_sites") {
            $(this).addClass('active');
            $('.body-groups .div_lista_sites').show();
            $('.body-groups .div_lista_sites').addClass('active');
        }
        if (this.id == "lista_ips") {
            $(this).addClass('active');
            $('.body-groups .div_lista_ips').show();
            $('.body-groups .div_lista_ips').addClass('active');
        }

        if (this.id == "lista_palavras") {
            $(this).addClass('active');
            $('.body-groups .div_lista_palavras').show();
            $('.body-groups .div_lista_palavras').addClass('active');
        }

        if (this.id == "lista_ext") {
            $(this).addClass('active');
            $('.body-groups .div_lista_ext').show();
            $('.body-groups .div_lista_ext').addClass('active');
        }
    })
    // ---------------------------------------------------------- //


    var novo_grupo_status = true;
    $('.body-groups .btn_add_group').on('click', function () {
        $('#g_default').hide()
        $('.btn_confirm').trigger('click')
        if (novo_grupo_status) {
            let div_grupo = $('<div>', { class: 'div_grupo div_grupo_novo active bg-transparent' })
            let srow = $('<div>', { class: 'row ml-1' }).appendTo(div_grupo)
            let input = $('<input>', { placeholder: "Novo grupo", class: 'form-control form-control-sm col-4' }).appendTo(srow)
            let confirm = $('<i>', { id: "btn_confirm", class: "btn_confirm fa fa-check-circle ml-2 mt-2" }).appendTo(srow)
            let edit = $('<i>', { class: 'fa fa-edit edit disabled' }).appendTo(div_grupo)


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

    $(document).on('click', '.body-groups .btn_confirm', function () {
        let grupo = $('.div_grupo.active input').val();

        if (grupo.length != 0) {
            var igual;
            $('.tit_grupo').each(function () {
                if (grupo == this.id) { igual = true }
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


    // Recebe as informações de acordo com o grupo selecionado

    $(document).on('click', '.body-groups .div_grupo:not(.div_grupo_novo)', function () {

        if (novo_grupo_status == true) {
            $('.div_alterna').hide();
            $('.div_alterna').removeClass('active');
            $('.ul_alterna_div .active').removeClass('active');


            $('#lista_sites').addClass('active');
            $('.div_lista_sites').show();
            $('.div_lista_sites').addClass('active');

            if (!novo_grupo_status) {
                novo_grupo_status = true;
                $('.div_grupo_novo.active').remove();
            }

            $('.div_grupo').removeClass('active');
            $(this).addClass('active');
            grupo = $(this).find('.tit_grupo').prop('id');
            recebe_SIP(grupo, 'sites', ' .div_lista_sites');
            recebe_SIP(grupo, 'palavras', ' .div_lista_palavras');
            recebe_SIP(grupo, 'ips', ' .div_lista_ips');
        }
    })
    // -----------------------------------------------------//

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
                let resp = confirm("Tem certeza que deseja remover esse grupo e suas políticas?");
                if (resp) {
                    let req = envia_padrao('grupo=' + grupo, 'excluir', '_controler/grupos_squid.php');
                    req.done(function (msg) {
                        if (msg.length != 0) {
                            msg_decode = JSON.parse(msg)
                            status = msg_decode["status"];
                            detalhe = msg_decode["detalhe"];

                            if (status == 0) {
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
    // -----------------------------------------------------//


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
        let div_crud = this_inp.siblings().not('.btn_move')


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

    // Adiciona Topicos
    $(document).on('click', '.body-groups .btn_add_topico', function () {
        let div_alterna = $('.div_alterna.active');

        if (!div_alterna.hasClass('div_lista_ext')) {
            topico_padrao_clone = topico_padrao.clone();
            let orfao = $('.div_alterna.active .orfao');
            let div_info_orfao = orfao.parent();
            let g_sel = $('.div_grupo.active p').prop('id');
            let acl_icon = topico_padrao.find('.acl_icon')

            $(topico_padrao_clone).append(div_info_orfao)
            $('.div_alterna.active .div_controle_topicos').prepend(topico_padrao_clone)

            orfao.removeClass('orfao')

            acl_icon.removeClass('d-none')

            if (g_sel == "nivel-a" || g_sel == "nivel-b" || g_sel == "nivel-c" || g_sel == "nivel-d" || g_sel == "todos-niveis") {
                $('.allow').addClass('d-none')
                $('.deny').removeClass('d-none')
            } else {
                $('.allow').removeClass('d-none')
                $('.deny').addClass('d-none')
            }
            $('.div_alterna.active p#aviso').hide()
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

    // Remove topicos
    $(document).on('click', '.body-groups .div_info_topico .btn_del', function () {

        let qtd_inputs = $(this).parent().parent().find('input.linha_topico').length;
        let qtd_inputs_topico = $(this).parent().parent().siblings().length;
        let div_info = $(this).parent().parent();

        if (qtd_inputs_topico != 0) {
            if (qtd_inputs > 1) {
                let resp = confirm('Deseja remover o tópico e seu conteúdo?');
                if (resp) {

                    div_info.remove();
                }
            } else {
                div_info.remove();
            }
        }

    })
    // ---------------------------------------------------------- //
    var oldGroup;
    $(document).on('click', '.body-groups .div_grupo.active .edit:not(.disabled)', function () {
        novo_grupo_status = false
        $(this).addClass('disabled')
        let div = $('.div_grupo.active').addClass('bg-transparent');
        p = $('.div_grupo.active .tit_grupo');
        let srow = $('<div>', { class: 'row ml-1' }).appendTo(div)
        let input = $('<input>', { class: 'form-control form-control-sm col-4 d-inline', value: p.text() }).appendTo(srow);
        let confirm = $('<i>', { id: "btn_confirm", class: "btn_confirm fa fa-check-circle ml-2 mt-2" }).appendTo(srow)
        oldGroup = p.text();
        p.remove();
    })

    // Salva alterações
    $(document).on('click', '.body-groups .btn_save:not(.disabled)', function () {
        if (sys_op) {
            $(".btn_confirm").trigger("click");
            if (novo_grupo_status) {
                let input_sites = $('.div_lista_sites .linha_topico');
                let input_palavras = $('.div_lista_palavras .linha_topico');
                let input_ips = $('.div_lista_ips .linha_topico');

                if (input_sites.length == 0 || input_sites.val().length == 0) {
                    let div_padrao_sites = cria_linha();
                    let input_sites = div_padrao_sites.find('input');
                    input_sites.val("sitepadraoh2")
                    $('.div_lista_sites .div_topico').append(div_padrao_sites)
                    div_padrao_sites.hide();
                }

                if (input_palavras.length == 0 || input_palavras.val().length == 0) {
                    let div_padrao_palavras = cria_linha();
                    let input_palavras = div_padrao_palavras.find('input');
                    input_palavras.val("palavrapadraoh2")
                    $('.div_lista_palavras .div_topico').append(div_padrao_palavras)
                    div_padrao_palavras.hide();
                }

                if (input_ips.length == 0 || input_ips.val().length == 0) {
                    let div_padrao_ips = cria_linha();
                    let input_ips = div_padrao_ips.find('input');
                    input_ips.val("169.254.254.255")
                    $('.div_lista_ips .div_topico').append(div_padrao_ips)
                    div_padrao_ips.hide();
                }

                let valida_sites = 0, valida_palavras = 0, valida_ips = 0, valida_ext = 0;
                let sites = $('.div_lista_sites input');
                let palavras = $('.div_lista_palavras input');
                let ips = $('.div_lista_ips input');
                let downloads = $('.div_lista_ext input').is(":checked");


                let categorias = {};
                let array_sites = [];
                let array_palavras = [];
                let array_ips = [];
                let grupo = $('.div_grupo.active p').prop('id').replace(' ', '-');
                var old_grupo = $('.div_grupo.active p').attr('data-old');

                for (let i = 0; i < sites.length; i++) {
                    linha = sites[i];
                    topico = $(linha);
                    valor = linha.value;

                    if (topico.hasClass('titulo_topico')) {
                        valor = "#" + valor;
                    }
                    valida_sites += 0

                    array_sites[i] = valor;
                }

                for (let i = 0; i < palavras.length; i++) {
                    linha = palavras[i];
                    topico = $(linha).hasClass('titulo_topico')
                    valor = linha.value;
                    let regex = new RegExp('^[a-zA-Z0-9]+$', 'g');
                    test = regex.test(valor)

                    if (topico) {
                        valor = "#" + valor;
                    } else {
                        if (valor.length != 0) {
                            if (!test) {
                                valida_sites += 1
                                validaInput(0, linha);
                            } else {
                                valida_sites += 0
                            }
                        }
                    }

                    array_palavras[i] = valor;
                }

                for (let i = 0; i < ips.length; i++) {

                    linha = ips[i];
                    topico = $(linha).hasClass('titulo_topico')
                    valor = linha.value;
                    let regex = new RegExp('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(\/[0-9]{1,3}$|$)', 'g');
                    test = regex.test(valor)

                    if (topico) {
                        valor = "#" + valor;
                    } else {
                        if (valor.length != 0) {
                            if (!test) {
                                valida_sites += 1
                                validaInput(0, linha);
                            } else {
                                valida_sites += 0
                            }
                        }
                        valida_sites += 0
                    }

                    array_ips[i] = valor;
                }

                if (valida_sites == 0 && valida_palavras == 0 && valida_ips == 0) {

                    categorias = JSON.stringify({ "sites": array_sites, "palavras": array_palavras, "ips": array_ips, "downloads": downloads });

                    let req = envia_padrao('old_grupo=' + old_grupo + '&grupo=' + grupo + '&dados=' + categorias, 'salvar', '_controler/grupos_squid.php')

                    req.done(function (msg) {
                        msg_decode = JSON.parse(msg)
                        status = msg_decode["status"];
                        detalhe = msg_decode["detalhe"];

                        if (status == 0) {
                            exibe_mudancas(1, 'Êxito ao salvar alterações.', detalhe)
                        } else {
                            exibe_mudancas(0, 'Houve falhas ao tentar salvar alterações', detalhe)
                        }
                    })
                } else {
                    exibe_mudancas(0, 'Falha ao salvar alterações.',
                        "<span>Verifique se os campos não estão vazios e se seguem o padrão correto.</span>")


                    let div_alterna = $('.div_alterna:not(.active) input');
                    let nome_nav, nome_div;
                    div_alterna.each(function (el, i, arr) {
                        nome_div = $(this).parents('.div_alterna').attr('id');
                        nome_nav = nome_div.replace('div_', '#')

                        if ($(this).hasClass('wrong')) {
                            $(nome_nav).siblings().show();
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
                let req = envia_padrao(null, 'aplicar', '_controler/grupos_squid.php')
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

})
/* ============================================================================= */
