// Pagina: listagen_lan.php
    // Funcao: exibe as interfaces internas


    function carrega_pagina_com_transicao(elemento_some, link) {
        $(elemento_some).hide('drop', function () {
            window.location.href = link;
        })
    }


    $('.body-lan .aside-back').on('click', function (e) { carrega_pagina_com_transicao('.body-lan .container, .aside-back', 'listagem_lan.php'); });
    $('.body-lista-lan .aside-add').on('click', function (e) { carrega_pagina_com_transicao('.body-lista-lan #lista, .aside-add', 'add_lan.php'); });
    $('.body-add-lan .aside-back').on('click', function (e) { carrega_pagina_com_transicao('.body-add-lan .container, .aside-back', 'listagem_lan.php'); });
    $('.body-wan .aside-back').on('click', function (e) { carrega_pagina_com_transicao('.body-wan .container, .aside-back', 'listagem_wan.php'); });
    $('.body-lista-wan .aside-add').on('click', function (e) { carrega_pagina_com_transicao('.body-lista-wan #lista, .aside-add', 'add_wan.php'); });
    $('.body-add-wan .aside-back').on('click', function (e) { carrega_pagina_com_transicao('.body-add-wan .container, .aside-back', 'listagem_wan.php'); });
    $('.body-lista-lan #lista tbody tr').on('click', function (e) {
        let int = "interface_lan?interface=" + $(this).children().eq(0).text();
        carrega_pagina_com_transicao(".body-lista-lan #lista", int);
    });
    $('.body-lista-wan #lista tbody tr').on('click', function (e) {
        let int = "interface_wan?interface=" + $(this).children().eq(0).text();
        carrega_pagina_com_transicao(".body-lista-wan #lista", int);
    });
    $('.body-lan #delete').on('click', function (e) {
        e.preventDefault();
        let interface = $('#formInterfaceLan input[name=interface]').val();
        let int_exibe = interface.toUpperCase();

        let resp = confirm("Tem certeza que deseja remover a interface " + int_exibe + "?");
        if (resp == true) {
            deletar("interface=" + interface, '_controler/interface_lan.php');
            carrega_pagina_com_transicao(".body-lan .container, .aside-back", 'listagem_lan.php');
        } else {
            return false;
        }
    })
    $('.body-wan #delete').on('click', function (e) {
        e.preventDefault();
        let interface = $('input[name=interface]').val();
        let int_exibe = interface.toUpperCase();

        let resp = confirm("Tem certeza que deseja remover a interface " + int_exibe + "?");
        if (resp == true) {
            deletar("interface=" + interface, '_controler/interface_wan.php');
            carrega_pagina_com_transicao(".body-lan .container, .aside-back", 'listagem_lan.php');
        } else {
            return false;
        }
    })

    inputs_interface = $('.div-info-valor input[type=text]:not([disabled=true])');
    div_form_interface = $('.body-interface .div-info-valor');
    form_data = $('.div-info-valor:not(.selectsPlaca, .div_eth)');
    modo_placa = $('.div-info-valor #selectModoPlaca');
    classificacao_placa_lan = $('.body-lan #formInterfaceLan #selectClassPlaca');
    classificacao_placa_wan = $('.body-wan #selectClassPlaca');



    if (modo_placa.val() == 'dinamica') {
        inputs_interface.attr('disabled', true);

        $(modo_placa).on('change', function () {
            if (modo_placa.val() == 'dinamica') {
                inputs_interface.attr('disabled', true);
            } else {
                inputs_interface.attr('disabled', false);
            }

        })
    } else {
        $(modo_placa).on('change', function () {
            if (modo_placa.val() == 'dinamica') {
                div_form_interface.not('.div_eth, .div_status, .div_modo').css('display', 'none');
                p_aviso_interface.text("Atenção: Ao clicar em aplicar a interface irá fazer uma requisição DHCP.")
                p_aviso_interface.css('display', 'block');
            } else {
                div_form_interface.css('display', 'block');
                p_aviso_interface.css('display', 'none');
            }
        })
    }

    $(classificacao_placa_lan).on('change', function () {
        if (classificacao_placa_lan.val() == 'externa') {
            div_form_interface.not('#divClassPlaca').css('display', 'none');
            p_aviso_interface.html("Atenção: Ao clicar em aplicar esta interface será considerada uma rota de saída para internet. <br> Se quiser edita-la vá para o menu Internet");
            p_aviso_interface.css('display', 'block');
        } else {
            div_form_interface.css('display', 'block')
            $('.body-lan .container .interface p#aviso').css('display', 'none');
        }

    })

    $(classificacao_placa_wan).on('change', function () {
        if (classificacao_placa_wan.val() == 'interna') {
            div_form_interface.not('#divClassPlaca').css('display', 'none')
            p_aviso_interface.html("Atenção: Ao clicar em aplicar esta interface deixará de ser considerada uma rota de saída para internet. <br> Se quiser edita-la vá para o menu 'interfaces internas'");
            p_aviso_interface.css('display', 'block');
        } else {
            div_form_interface.css('display', 'block')
            $('.body-wan .interface p#aviso').css('display', 'none');
        }

    })


    $('.body-lan #save').on('click', function (e) {
        e.preventDefault();

        requiredFields = $('input[required=true]');


        $('#formInterfaceLan input').on('click', function () {
            p_aviso.css('display', 'none');
            $(requiredFields).css('border-color', 'inherit');
        })

        requiredFields.each(function () {
            if (this.value == "") {
                p_aviso.text("Preencha os campos obrigatórios !");
                p_aviso.css('display', 'block');
                $(this).css('border-color', 'red');
                vazio = true;
            } else {
                p_aviso.text("");
                vazio = false;
                return;
            }
        })

        if (!vazio) {
            let tipo_placa = $('.body-lan .container .interface form input[name=tipo_placa]').val();
            let interface = $('.body-lan .container .interface form input[name=interface]').val();
            let dadosFormulario = $('#formInterfaceLan').serialize() + "&interface=" + interface + "&tipo_placa=" + tipo_placa;
            let texto_erro;
            editar(dadosFormulario, '_controler/interface_lan.php');


            $(document).ajaxComplete(function () {

                p_aviso.css('display', 'block');
                if (status_req_ajax === false) {
                    let exception = erro_req_ajax.tipo.search("UNIQUE constraint failed");
                    let campo = erro_req_ajax.campo;
                    if (exception != -1) {
                        if (campo == "ip") {
                            texto_erro = "Erro ao tentar editar interface: Endereço IPv4 já existe!";
                        }
                    }
                    p_aviso.css("color", "red");
                    p_aviso.text(texto_erro);
                } else {
                    p_aviso.css("color", "green");
                    p_aviso.text("Interface editada com sucesso!");
                    setTimeout(function () { carrega_pagina_com_transicao('.body-lan .container', 'listagem_lan.php') }, 2000);

                }
            })
        }


    })

    $('.body-wan #save').on('click', function (e) {
        e.preventDefault();
        let interface = $('.body-wan .interface form input[name=interface]').val();
        let dadosFormulario = $('.body-wan .interface #formInterfaceWan').serialize() + '&interface=' + interface;
        editar(dadosFormulario, "_controler/interface_wan.php");
        carrega_pagina_com_transicao(".body-wan .container", "listagem_wan.php");
    })



    // ==================================================================================
    // Pagina: add_lan.php
    // Funcao: adiciona interfaces internas



    $('.body-add-lan #formInterfaceLan #save').on('click', function (element) {
        element.preventDefault();
        let interface = $('.body-add-lan .container .interface form input[name=interface]').val();

        requiredFields = $('input[required=true]');



        $('#formInterfaceLan input').on('click', function () {
            p_aviso.css('display', 'none');
            $(requiredFields).css('border-color', 'inherit');
        })

        requiredFields.each(function () {
            if (this.value == "") {
                p_aviso.text("Preencha os campos obrigatórios !");
                p_aviso.css('display', 'block');
                $(this).css('border-color', 'red');
                vazio = true;
            } else {
                p_aviso.text("");
                vazio = false;
                return;
            }
        })


        if (!vazio) {

            let dadosFormulario = $('#formInterfaceLan').serialize().toLowerCase();
            adicionar(dadosFormulario, '_controler/interface_lan.php');


            $(document).ajaxComplete(function () {
                p_aviso.css('display', 'block');
                if (status_req_ajax === false) {
                    let exception = erro_req_ajax.tipo.search("UNIQUE constraint failed");
                    let campo = erro_req_ajax.campo;
                    let tipo_erro = erro_req_ajax.tipo;
                    if (exception != -1) {
                        if (campo == "ip") {
                            texto_erro = "Erro ao tentar adicionar interface: Endereço IPv4 já existe!";
                        } else if (campo == "ethernet") {
                            texto_erro = "Erro ao tentar adicionar interface: A interface <a href='interface_lan.php?interface=" + interface + "'>" + interface.toUpperCase() + "</a> já existe!";
                        } else {
                            texto_erro = "Erro desconhecido: " + tipo_erro;
                        }
                    }
                    p_aviso.css("color", "red");
                    p_aviso.html(texto_erro);
                } else {
                    p_aviso.css("color", "green");
                    p_aviso.text("Interface adicionada com sucesso!");
                    setTimeout(function () { carrega_pagina_com_transicao('.body-add-lan .container', 'listagem_lan.php'); }, 2000);
                }
            })


        }


    })


    // ==================================================================================
    // Pagina: add_wan.php
    // Funcao: adiciona interfaces externas


    $('.body-add-wan #formInterfaceWan #save').on('click', function (element) {
        element.preventDefault();

        requiredFields = $('input[required=true]');
        p_aviso = $('p.aviso');
        let statusForm = $('.body-add-wan .div_ip').css('display');
        let interface = $('.body-add-wan input[name=interface]');
        let interface_exibe = $('.body-add-wan .container .interface form input[name=interface]').val().toUpperCase();
        let texto_erro = "";

        $('#formInterfaceWan input').on('click', function () {
            p_aviso.css('display', 'none');
            $(requiredFields).css('border-color', 'inherit');
        })

        if (statusForm == 'block') {
            requiredFields.each(function () {

                if (this.value == "") {
                    p_aviso.css('display', 'block');
                    $(this).css('border-color', 'red');
                    vazio = true;
                } else {
                    vazio = false;
                    return;
                }
            })
        } else {

            if (interface.val() == "") {
                p_aviso.css('display', 'block');
                $(interface).css('border-color', 'red');
                vazio = true;
            } else {
                vazio = false;
            }
        }
        if (!vazio) {
            let dadosFormulario = $('#formInterfaceWan').serialize().toLowerCase();
            adicionar(dadosFormulario, '_controler/interface_wan.php');

            $(document).ajaxComplete(function () {
                p_aviso.css('display', 'block');
                if (status_req_ajax === false) {
                    let exception = erro_req_ajax.tipo.search("UNIQUE constraint failed");
                    let campo = erro_req_ajax.campo;
                    let tipo_erro = erro_req_ajax.tipo;
                    if (exception != -1) {
                        if (campo == "ip") {
                            texto_erro = "Erro ao tentar adicionar interface: Endereço IPv4 já existe!";
                        } else if (campo == "ethernet") {
                            texto_erro = "Erro ao tentar adicionar interface: A interface <a href='interface_wan.php?interface=" + interface_exibe + "'>" + interface_exibe + "</a> já existe!";
                        } else {
                            texto_erro = "Erro desconhecido: " + tipo_erro;
                        }
                    }
                    p_aviso.css("color", "red");
                    p_aviso.html(texto_erro);
                } else {
                    p_aviso.css("color", "green");
                    p_aviso.text("Interface adicionada com sucesso!");
                    setTimeout(function () { carrega_pagina_com_transicao('.body-add-wan .container', 'listagem_wan.php'); }, 2000);
                }
            })
        }


    })


    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++

    // Pagina: listagem_provedores.php


    $('.body-lista-provedor .aside-add').on('click', function (e) { carrega_pagina_com_transicao('.body-lista-provedor #lista, .aside-add', 'add_provedor.php'); });


    $('.body-lista-provedor #lista tbody tr').on('click', function (e) {
        let id = $(this).children().eq(0).text();
        let nome = $(this).children().eq(1).text();
        let interface = $(this).children().eq(4).text();
        let provedor = "provedor.php?id=" + id + "&provedor=" + nome + "&interface=" + interface;
        carrega_pagina_com_transicao(".body-lista-provedor #lista", provedor);
    });



    // Pagina: provedor.php

    $('.body-provedor .aside-back').on('click', function (e) { carrega_pagina_com_transicao('.body-provedor .container, .aside-back', 'listagem_provedores.php'); });

    modo_operacao = $('.body-provedor #selectModoPlaca');
    div_d_pppoe = $('.body-provedor .section-main .container .div_d_pppoe');
    div_user_pppoe = $('.body-provedor .section-main .container .div_u_pppoe');
    div_pass_pppoe = $('.body-provedor .section-main .container .div_s_pppoe');
    input_pppoe = $('.body-provedor .section-main .container .div_d_pppoe input, .body-provedor .section-main .container .div_u_pppoe input, .body-provedor .section-main .container .div_s_pppoe input')

    $('.body-provedor #delete').on('click', function (e) {

        e.preventDefault();
        let id = $('#formProvedor input[name=id]').val();
        let provedor = $('#formProvedor input[name=provedor]').val();


        let resp = confirm("Tem certeza que deseja remover o link de internet da " + provedor + "?");
        if (resp == true) {
            deletar("id=" + id, '_controler/provedores.php');
            $(document).ajaxComplete(function () {
                if (status_req_ajax == true) {
                    p_aviso.css('display', 'block');
                    p_aviso.css('color', 'green');
                    p_aviso.text("Provedor excluído!");
                    setTimeout(function () {
                        carrega_pagina_com_transicao(".body-provedor .container, .aside-back", 'listagem_provedores.php');
                    }, 2000)
                } else {
                    p_aviso.css('display', 'block');
                    p_aviso.css('color', 'red');
                    p_aviso.text("Erro ao excluir provedor.");
                }

            })


        } else {
            return false;
        }
    })


    $('.body-provedor #save').on('click', function (e) {
        e.preventDefault();

        requiredFields = $('input[required=true], input[required=required]');


        $('#formProvedor input').on('click', function () {
            p_aviso.css('display', 'none');
            $(requiredFields).css('border-color', 'inherit');
        })

        requiredFields.each(function () {
            if (this.value == "") {
                p_aviso.text("Preencha os campos obrigatórios !");
                p_aviso.css('display', 'block');
                $(this).css('border-color', 'red');
                vazio = true;
            } else {
                p_aviso.text("");
                vazio = false;
                return;
            }
        })

        if (!vazio) {
            let dadosFormulario = $('#formProvedor').serialize();
            let prioridade_enviada = $('#formProvedor [name=prioridade]').val();
            let id_provedor_enviado = $('#formProvedor [name=id]').val();
            let filtro = "action=consulta_existencia&db_id=id_provedor&db_tabela=provedores&db_coluna=prioridade&db_dado=" + prioridade_enviada;

            let texto;

            function envia() {
                editar(dadosFormulario, '_controler/provedores.php');
                $(document).ajaxComplete(function () {
                    p_aviso.css('display', 'block');
                    if (status_req_ajax === false) {
                        p_aviso.css("color", "red");
                        texto = "Erro ao editar provedor."
                    } else {
                        p_aviso.css("color", "green");
                        texto = "Provedor editado com sucesso!";
                        setTimeout(function () { carrega_pagina_com_transicao('.body-provedor .container', 'listagem_provedores.php') }, 2000);
                    }
                    p_aviso.text(texto);
                })
            }
            if (prioridade_enviada == "backup") {
                envia();
            }
            else {
                $.ajax({
                    type: 'POST',
                    async: true,
                    data: filtro,
                    url: '_controler/operacoes.php',
                    complete: function (msg) {
                        if (msg.responseText.length != 0 && msg.responseText != 0) {
                            let dataJson = JSON.parse(msg.responseText);
                            let prioridade_retornada = dataJson[0].prioridade;
                            let id_retornado = dataJson[0].id_provedor;


                            if (prioridade_retornada == "principal" && id_retornado != id_provedor_enviado) {
                                let resp = confirm("Tem certeza que deseja alterar o link principal?");
                                if (resp == true) {
                                    let filtro = "action=altera&db_id=id_provedor&db_tabela=provedores&db_coluna=prioridade&db_dado=backup&db_dado_comparacao=principal";
                                    $.ajax({
                                        type: 'POST',
                                        async: true,
                                        data: filtro,
                                        url: '_controler/operacoes.php',
                                        complete: function (msg) {
                                            envia();
                                        }
                                    })
                                } else {
                                    return false;
                                }


                            } else {
                                envia();
                            }
                        } else {
                            envia();
                        }

                    }
                })
            }


        }
    })


    if (modo_operacao.val() == "pppoe") {
        $(div_d_pppoe).css('display', 'block')
        $(div_user_pppoe).css('display', 'block');
        $(div_pass_pppoe).css('display', 'block');
    } else {
        $(div_d_pppoe).css('display', 'none')
        $(div_user_pppoe).css('display', 'none');
        $(div_pass_pppoe).css('display', 'none');
        $(input_pppoe).attr('required', false)
    }


    modo_operacao.on('change', function () {
        if (this.value == "pppoe") {
            $(div_d_pppoe).css('display', 'block')
            $(div_user_pppoe).css('display', 'block');
            $(div_pass_pppoe).css('display', 'block');
            input_pppoe.attr('required', true);
        } else {
            $(div_d_pppoe).css('display', 'none')
            $(div_user_pppoe).css('display', 'none');
            $(div_pass_pppoe).css('display', 'none');

            $(input_pppoe).attr('required', false);
            $(input_pppoe).val('');

        }
    })


    // Pagina: add_provedor.php

    $('.body-add-provedor .aside-back').on('click', function (e) { carrega_pagina_com_transicao('.body-add-provedor .container, .aside-back', 'listagem_provedores.php'); });


    modo_operacao = $('.body-add-provedor #selectModoPlaca');
    div_d_pppoe = $('.body-add-provedor .section-main .container .div_d_pppoe');
    div_user_pppoe = $('.body-add-provedor .section-main .container .div_u_pppoe');
    div_pass_pppoe = $('.body-add-provedor .section-main .container .div_s_pppoe');
    input_pppoe = $('.body-add-provedor .section-main .container .div_d_pppoe input, .body-add-provedor .section-main .container .div_u_pppoe input, .body-add-provedor .section-main .container .div_s_pppoe input')


    $('.body-add-provedor #save').on('click', function (e) {
        e.preventDefault();

        requiredFields = $('input[required=true], input[required=required]');


        $('#formProvedor input').on('click', function () {
            p_aviso.css('display', 'none');
            $(requiredFields).css('border-color', 'inherit');
        })

        requiredFields.each(function () {
            if (this.value == "") {
                p_aviso.text("Preencha os campos obrigatórios !");
                p_aviso.css('display', 'block');
                $(this).css('border-color', 'red');
                vazio = true;
            } else {
                p_aviso.text("");
                vazio = false;
                return;
            }
        })

        if (!vazio) {
            let dadosFormulario = $('#formProvedor').serialize();
            let prioridade_enviada = $('#formProvedor [name=prioridade]').val();
            let id_provedor_enviado = $('#formProvedor [name=id]').val();
            let filtro = "action=consulta_existencia&db_id=id_provedor&db_tabela=provedores&db_coluna=prioridade&db_dado=" + prioridade_enviada;

            let texto;

            function envia() {
                adicionar(dadosFormulario, '_controler/provedores.php');

                $(document).ajaxComplete(function () {
                    p_aviso.css('display', 'block');
                    if (status_req_ajax === false) {
                        p_aviso.css("color", "red");
                        texto = "Erro ao adicionar provedor."
                    } else {
                        p_aviso.css("color", "green");
                        texto = "Provedor adicionado com sucesso!";
                        setTimeout(function () { carrega_pagina_com_transicao('.body-add-provedor .container', 'listagem_provedores.php') }, 2000);
                    }
                    p_aviso.text(texto);
                })
            }


            if (prioridade_enviada == "backup") {
                envia();
            }
            else {
                $.ajax({
                    type: 'POST',
                    async: true,
                    data: filtro,
                    url: '_controler/operacoes.php',
                    complete: function (msg) {
                        if (msg.responseText.length != 0 && msg.responseText != 0) {
                            let dataJson = JSON.parse(msg.responseText);
                            let prioridade_retornada = dataJson[0].prioridade;
                            let id_retornado = dataJson[0].id_provedor;


                            if (prioridade_retornada == "principal") {

                                let resp = confirm("Já existe um provedor principal, deseja altera-lo?");

                                if (resp == true) {
                                    let filtro = "action=altera&db_id=id_provedor&db_tabela=provedores&db_coluna=prioridade&db_dado=backup&db_dado_comparacao=principal";

                                    $.ajax({
                                        type: 'POST',
                                        async: true,
                                        data: filtro,
                                        url: '_controler/operacoes.php',
                                        complete: function (msg) {
                                            envia();
                                        }
                                    })
                                } else {
                                    return false;
                                }


                            } else {
                                envia();
                            }
                        } else {
                            envia();
                        }

                    }
                })
            }


        }
    })


    if (modo_operacao.val() == "pppoe") {
        $(div_d_pppoe).css('display', 'block')
        $(div_user_pppoe).css('display', 'block');
        $(div_pass_pppoe).css('display', 'block');
    } else {
        $(div_d_pppoe).css('display', 'none')
        $(div_user_pppoe).css('display', 'none');
        $(div_pass_pppoe).css('display', 'none');
        $(input_pppoe).attr('required', false)
    }


    modo_operacao.on('change', function () {
        if (this.value == "pppoe") {

            $(div_d_pppoe).css('display', 'block')
            $(div_user_pppoe).css('display', 'block');
            $(div_pass_pppoe).css('display', 'block');
            input_pppoe.attr('required', true);
        } else {
            $(div_d_pppoe).css('display', 'none')
            $(div_user_pppoe).css('display', 'none');
            $(div_pass_pppoe).css('display', 'none');

            $(input_pppoe).attr('required', false);
            $(input_pppoe).val('');

        }
    })
