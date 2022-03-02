$(document).ready(function () {



    $('.tr_phy').each(function (i, element) {

        irmaos = $(element).nextUntil(".tr_phy")
        td_collapse = $(this).find('.td_collapse');


        if (irmaos.length > 0) {

            td_collapse.addClass('active')
            $('<i>', {
                class: 'fas fa-minus-square coll_icon'
            }).prependTo(td_collapse)
        }
    });

    $('.acc_category').each(function (i, element) {

        irmaos = $(element).nextUntil(".acc_category")
        td_collapse = $(this).find('.td_collapse');


        if (irmaos.length > 0) {

            $('<i>', {
                class: 'fas fa-minus-square coll_icon'
            }).prependTo(td_collapse)
        }
    });


    $('.acc_category').on('click', function () {

        irmaos = $(this).nextUntil(".acc_category")
        icone = $(this).children().find('.coll_icon')

        if (irmaos.is(':visible')) {
            icone.removeClass('fa-minus-square').addClass('fa-plus-square')
            irmaos.hide()
        } else {
            icone.removeClass('fa-plus-square').addClass('fa-minus-square')
            irmaos.show()
        }
    })


    $('tr:not(.acc_category) .td_collapse.active').on('click', function () {

        irmaos = $(this).parent().nextUntil(".tr_phy")
        icone = $(this).find('.coll_icon')

        if (irmaos.is(':visible')) {
            icone.removeClass('fa-minus-square').addClass('fa-plus-square')
            irmaos.hide()
        } else {
            icone.removeClass('fa-plus-square').addClass('fa-minus-square')
            irmaos.show()
        }
    })


    $('tr:not(.acc_category) td:not(.td_collapse)').on('click', function () {

        el = $(this).parent()

        if (el.hasClass('tr_enabled')) {
            $(el).removeClass('tr_enabled').addClass('tr_disabled')
            $('.btn_edit').addClass('disabled')
            $('.btn_delete').addClass('disabled')
        } else {
            $('tr:not(.acc_category)').removeClass('tr_enabled')
            $(el).removeClass('tr_disabled').addClass('tr_enabled')
            $('.btn_edit').removeClass('disabled')
            $('.btn_delete').removeClass('disabled')
        }


    })
});