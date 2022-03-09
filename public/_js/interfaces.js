$(document).ready(function () {


    // Funcionalidades da tabela

    function collapseTR() {

        $('.acc_category').each(function (i, element) {

            irmaos = $(element).nextUntil(".acc_category")
            td_collapse = $(this).find('.td_collapse');



            if (irmaos.length > 0) {
                
                if (!td_collapse.hasClass('active')) {

                    td_collapse.addClass('active')
                    $('<i>', {
                        class: 'fas fa-square-minus coll_icon'
                    }).prependTo(td_collapse)
                    $(this).find('.td_collapse').children().show();
                } 

            }else{
                if (td_collapse.hasClass('active')) {
                    
                    td_collapse.removeClass('active')
                    
                    $(this).find('.td_collapse').children().hide();   
                }
            }
        });

        $('.tr_phy:not(.acc_category)').each(function (i, element) {

            irmaos = $(element).nextUntil(".tr_phy")
            td_collapse = $(this).find('.td_collapse');



            if (irmaos.length > 0) {
                
                if (!td_collapse.hasClass('active')) {

                    td_collapse.addClass('active')
                    $('<i>', {
                        class: 'fas fa-square-minus coll_icon'
                    }).prependTo(td_collapse)
                    $(this).find('.td_collapse').children().show();
                } 

            }else{
                if (td_collapse.hasClass('active')) {
                    
                    td_collapse.removeClass('active')
                    
                    $(this).find('.td_collapse').children().hide();   
                }
            }
        });
    } collapseTR()


    $('.acc_category .td_collapse.active').on('click', function () {

        irmaos = $(this).parent().nextUntil(".acc_category")
        icone = $(this).find('.coll_icon')

        if (irmaos.is(':visible')) {
            icone.removeClass('fa-solid fa-square-minus').addClass('fa-square-plus')
            irmaos.hide()
        } else {
            icone.removeClass('fa-square-plus').addClass('fa-square-minus')
            irmaos.show()
        }
    })


    $('tr:not(.acc_category) .td_collapse.active').on('click', function () {

        irmaos = $(this).parent().nextUntil(".tr_phy")
        icone = $(this).find('.coll_icon')

        if (irmaos.is(':visible')) {
            icone.removeClass('fa-square-minus').addClass('fa-square-plus')
            irmaos.hide()
        } else {
            icone.removeClass('fa-square-plus').addClass('fa-square-minus')
            irmaos.show()
        }
    })


    $('tr:not(.acc_category)').on('click', function () {

        el = $(this)



        if (el.hasClass('tr_enabled')) {

            $(el).removeClass('tr_enabled').addClass('tr_disabled')

            $('.btn_edit').addClass('disabled')
            $('.btn_delete').addClass('disabled')
        } else {

            $('tr:not(.acc_category)').removeClass('tr_enabled')
            $(el).removeClass('tr_disabled').addClass('tr_enabled')
            $('.btn_edit').addClass('disabled')
            $('.btn_delete').addClass('disabled')


            if (el.hasClass('tr_phy')) {
                $('.btn_edit').removeClass('disabled')
            } else {

                $('.btn_edit').removeClass('disabled')
                $('.btn_delete').removeClass('disabled')

            }

        }


    })

    // FIM Funcionalidades da tabela

    // CRUD

    $('.btn_delete').on('click', function () {

        let resp = confirm("Tem certeza que deseja remover essa interface?");
        if (resp) {
            let coluna_ativa = $('.tr_vir.tr_enabled');
            let iface = $.trim(coluna_ativa.find('.table-iface').text());

            let req = envia_padrao('iface=' + iface, 'delete', 'interfaces.require.php');
            req.done(function (msg) {
                if (msg == '1') {
                    exibe_mudancas(1, "Sucesso ao remover interface");
                    $('div#conteudo *').remove();
                    $('div#conteudo').load('interfaces.php')
                } else {
                    exibe_mudancas(0, "Falha ao remover interface");
                }

            })
        }


    })

    // FIM CRUD
});