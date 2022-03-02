$(document).ready(function () {
    page_loading(false);
    // Pagina: listagem_dispositivos.php
    // Funções de exibição para o usuario

    function newEl(nome, setor, ipv4) {
        var div_usuario = $('<div>', { class: 'div_usuario' });
        $('<p>', { class: 'f_disp dispositivo', text: nome }).appendTo(div_usuario);
        $('<span>', { class: 'f_setor setor', text: setor }).appendTo(div_usuario);
        $('<span>', { class: 'f_ipv4 ipv4', text: ipv4 }).appendTo(div_usuario);
        return div_usuario;
    }

    function getInfoDevice(ipSearch) {
        let req = envia_padrao("ip_procura=" + ipSearch, "lista_dispositivo", "_controler/dispositivos.php");
        req.done(function (msg) {

            if (msg.length != 0) {

                let msg_decode = JSON.parse(msg);
                let dispositivo = msg_decode["nome"]
                let setor = msg_decode["setor"]
                let ipv4 = msg_decode["ipv4"]
                let mac = msg_decode["mac"]

                $('.body-users #nome').val(dispositivo);
                $('.body-users #setor').val(setor);
                $('.body-users #ipv4').val(ipv4);
                $('.body-users #mac').val(mac);
            }
        })
    }

    (function recebe_dispositivos() {

        page_loading(true)

        let req = envia_padrao("ip_procura=all", "lista_dispositivo", "_controler/dispositivos.php");
        req.done(function (msg) {

            if (msg.length != 0 && msg != "null") {
                let msg_decode = JSON.parse(msg);


                for (obj in msg_decode) {
                    
                    nome = msg_decode[obj].nome;
                    setor = msg_decode[obj].setor;
                    ipv4 = msg_decode[obj].ipv4;
                    mac = msg_decode[obj].mac;
                    el = newEl(nome, setor, ipv4);
                    $('.div_usuarios').append(el)
                    $('.div_usuario').eq(0).addClass('active');
                }
                div_primeiro_usuario = $('.body-users .div_usuarios .div_usuario:first-of-type()');
                nome_primeiro_usuario = $('.body-users .div_usuarios .div_usuario:first-of-type() .f_disp').text();
                setor_primeiro_usuario = $('.body-users .div_usuarios .div_usuario:first-of-type() .f_setor').text();
                ip_primeiro_usuario = $('.body-users .div_usuarios .div_usuario:nth-child(2) .f_ipv4').text();
                mac_primeiro_usuario = $('.body-users .div_usuarios .div_usuario:first-of-type() .f_mac').text();

            } else {
                $('.div_usuario_padrao').show();
                $('.body-users .btn_save, .btn_delete, .btn_apply, .edit').remove();
                $('.body-users #div_info_usuario form input').prop('disabled', 'disabled')
            }

            let ip = $('.div_usuario.active .ipv4').text();
            
            getInfoDevice(ip);

            page_loading(false)

        })

    })()


    $('.body-users .aside-back').on('click', function (e) {
        $('.col_info_usuarios').hide('drop');
        $('.col_usuarios').show(500)
        $('.col_info_usuarios').css('border', 'inherit');
        $('footer').hide('500')
        $('.body-users aside').hide()
    });


    tablet_celular = window.matchMedia("(max-width: 768px)");
    $(document).on('click', '.body-users .div_usuario', function () {

        if (tablet_celular.matches) {
            $('footer').css('display', 'flex');
            $('.body-users aside').show();
            $('.col_usuarios').hide('drop');
            $('.col_info_usuarios').css('border', 'none');
            $('.col_info_usuarios').show();

        } else {
            $('footer').hide();
            $('.col_usuarios').show(500);
            $('.col_info_usuarios').css('border', 'default');
            $('.col_info_usuarios').show();
        }

        $('.body-users .div_usuario').removeClass('active');
        $(this).addClass('active');


        $('#ipv4').prop('disabled', true)
        $('.div_mac span').html('');
        $('.body-users .edit').removeClass('disabled');
        let ip = $(this).find('.ipv4').text();
        getInfoDevice(ip);
    })

    // Caixa de pesquisa
    $('#search_user').on('keyup', function () {

        let texto_procurado = $(this).val();
        let divs_usuario = $('.div_usuario');
        let filtro = new RegExp(texto_procurado, 'ig');


        $('.div_usuario').each(function () {
            let texto_div = $(this).text();
            let procura = filtro.test(texto_div);

            if (procura === true) {
                $(this).css('display', 'block');
            } else {
                $(this).css('display', 'none');
            }
        })
    })

    $('#search_user').on('click', function () {

        let texto_procurado = $(this).val();
        let divs_usuario = $('.div_usuario');
        let filtro = new RegExp(texto_procurado, 'm');


        divs_usuario.each(function () {
            let texto_div = $(this).text();
            let procura = filtro.test(texto_div);

            if (procura === true) {
                $(this).css('display', 'block');
            } else {
                $(this).css('display', 'none');
            }
        })
    })
    // Fim caixa de pesquisa


    // Funções de CRUD do usuario

    $('.body-users .edit').on('click', function (e) {
        $(this).addClass('disabled');
        ipv4nilla = $('#ipv4').val();
        $('#ipv4').attr('disabled', false);
    })

    $('.body-users .btn_save').off().on('click', function (e) {

        ipv4nilla = $('.div_usuario.active .f_ipv4').text()
        let nome = $('#nome');
        let setor = $('#setor');
        let ipv4 = $('#ipv4');
        let mac = $('#mac');
        regex_ipv4 = /\d+\.\d+\.\d+\.\d+/g;
        found = ipv4.val().match(regex_ipv4);


        $('form input').on('click', function () {
            ipv4.css('border', '1px solid #ced4da');
            mac.css('border', '1px solid #ced4da');
        })

        if (ipv4.val().length == 0 || !found) {
            ipv4.tooltip('show')
            ipv4.css('border-color', 'red');
            vazio_ip = true;
        } else {
            vazio_ip = false;
        }

        if (mac.val().length == 0) {
            mac.tooltip('show')
            mac.css('border-color', 'red');
            vazio_mac = true;
        } else {
            vazio_mac = false;
        }

        if (!vazio_ip && !vazio_mac) {
            page_loading(true);

            obj_dados = {
                "nome": nome.val(),
                "setor": setor.val(),
                "ipv4": ipv4.val(),
                "mac": mac.val(),
                "ipv4nilla": ipv4nilla
            }

            dados = JSON.stringify(obj_dados);


            let request_ajax = envia_padrao("dispositivo=" + dados, 'edit', '_controler/dispositivos.php');

            request_ajax.done(function (msg) {
                page_loading(false);

                if (msg != "null" && msg.length > 0) {

                    msg_decode = JSON.parse(msg);
                    status = msg_decode["status"]
                    detalhe = msg_decode["detalhe"]

                    if (status == 0) {

                        $('.div_usuario.active');
                        $('.div_usuario.active .f_disp').text($('#nome').val());
                        $('.div_usuario.active .f_setor').text($('#setor').val());
                        $('.div_usuario.active .f_ipv4').text($('#ipv4').val());

                        exibe_mudancas(1, "Êxito ao editar usuário");


                    } else {
                        exibe_mudancas(0, "Falha ao editar usuário", detalhe);
                        $('#ipv4').val(ipv4nilla)
                    }

                    $('#ipv4').attr('disabled', true);
                    $('.edit').removeClass('disabled');
                }
            })
        }

    })

    $('.body-users .btn_delete').not('.disabled').off().on('click', function (e) {

        e.preventDefault();

        let div_usuario_ativo = $('.div_usuarios .div_usuario.active');
        let user = $('.div_usuario.active .f_disp').text();
        let ipv4 = $('#ipv4').val();

        let resposta = confirm("Deseja realmente excluir o usuário " + user + "?");

        if (resposta == true) {
            let request_ajax = envia_padrao('ipv4=' + ipv4, "del", "_controler/dispositivos.php");

            page_loading(true);


            request_ajax.done(function (msg) {
                page_loading(false);

                if (msg != "null" && msg.length > 0) {

                    msg_decode = JSON.parse(msg);
                    status = msg_decode["status"]
                    detalhe = msg_decode["detalhe"]

                    if (status == 0) {
                        exibe_mudancas(1, "Êxito ao remover dispositivo: " + user);
                        div_usuario_ativo.remove();
                        $('form')[0].reset();
                        $('.div_usuario:nth-child(2)').addClass('active');
                        ipv4 = $('.div_usuario.active .f_ipv4').text();
                        if (ipv4.length == 0) {
                            $('.div_usuario_padrao').show();
                            $('form input').prop('disabled', 'disabled')
                            $('.btn_close').on('click', function () {
                                $('.btn_save, .btn_delete, .btn_apply, .edit').remove();
                            })
                        } else { getInfoDevice(ipv4); }

                    } else {
                        exibe_mudancas(0, "Falha ao tentar remover disposivo" + user, detalhe);
                    }
                }

            })

        } else {
            return false;
        }

    })

    $('#mac').on('focusout', function () {$('.div_mac span').hide();})

    $('#mac').off().on('click', function (e) {

        let input_ip = $('#ipv4').val();
        let input_mac = $('.div_mac input');
        let img_loading = $('.div_mac img');
        let span_status = $('.div_mac span');
        let reg_mac = /[0-9a-zA-Z]:[0-9a-zA-Z]/ig
        let req = envia_padrao('ip=' + input_ip, 'find_mac', '../_controler/dispositivos.php');

        span_status.hide();
        input_mac.attr('disabled', true);
        img_loading.show();

        req.done(function (msg) {
            input_mac.attr('disabled', false);
            reg_test = reg_mac.test(msg);

            if (typeof msg == "null" || msg == "" || reg_test === false) {
                span_status.html('Mac não encontrado.');
                span_status.show();
                input_mac.focus();
            } else {
                span_status.hide();
                input_mac.val(msg);
            }

            img_loading.hide();
        })
    })

    $(document).on('click', '.body-users .btn_apply:not(.disabled)', function () {
        modal_loading(true)
        setTimeout(() => {

            ip = $('#ipv4').val();
            if ($(this).hasClass('disabled')) {
                return false;
            } else {
                let req = envia_padrao(null, 'apply', '_controler/dispositivos.php')
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

            }
        }, 3000);


    })

    // ------------------
    // Pagina: add_dispositivos.php

    tablet_celular = window.matchMedia("(max-width: 768px)");

    if (tablet_celular.matches) {$('.body-add-user footer').css('display', 'flex');} 
    else {$('.body-add-user footer').hide();}

    $('.body-add-user .btn_add').off().on('click', function (e) {

        let nome = $('#nome');
        let setor = $('#setor');
        let ipv4 = $('#ipv4');
        let mac = $('#mac');
        regex_ipv4 = /\d+\.\d+\.\d+\.\d+/g;
        found = ipv4.val().match(regex_ipv4);


        $('form input').on('click', function () {
            ipv4.css('border', '1px solid #ced4da');
            mac.css('border', '1px solid #ced4da');
        })

        if (ipv4.val().length == 0 || !found) {
            ipv4.css('border-color', 'red');
            vazio_ip = true;
        } else {
            vazio_ip = false;
        }

        if (mac.val().length == 0) {
            mac.css('border-color', 'red');
            vazio_mac = true;
        } else {
            vazio_mac = false;
        }


        if (!vazio_ip && !vazio_mac) {
            page_loading(true);


            obj_dados = {
                "nome": nome.val(),
                "setor": setor.val(),
                "ipv4": ipv4.val(),
                "mac": mac.val(),
            }

            dados = JSON.stringify(obj_dados);

            let request_ajax = envia_padrao("dispositivo=" + dados, 'add', '_controler/dispositivos.php');

            request_ajax.done(function (msg) {
                page_loading(false);
                msg_decode = JSON.parse(msg);

                status = msg_decode["status"]
                detalhe = msg_decode["detalhe"]

                if (status == 0) {
                    exibe_mudancas(1, "Êxito ao adicionar usuário");
                    $('form')[0].reset();
                } else {
                    exibe_mudancas(0, "Falha ao adicionar usuário", detalhe);
                }



            })


        }

    })


    /* ============================================================================= */
})