$(document).ready(function () {
$.easing.def = "easeOutBounce";
    $('.toast').toast({ delay: 5000 });

    function hideSubMenu() { // Função que oculta todos os submenus abertos
        $(".link-item").addClass('liDisabled');
        $(".link-item").removeClass('liEnabled');
        $(".link-item svg").addClass('icon-disabled');
        $(".link-item svg").removeClass('icon-enabled');
        $(".link-item .arrow-enabled").addClass('arrow-disabled');
        $(".link-item .arrow-enabled").removeClass('arrow-enabled');
        $('.link-item').siblings().slideUp("fast");
    }

    /* Submenus */
    $(".link-item").on('click', function () { // Quando clicar no menu principal
        liMain = $(this).parent(); // LI pai do link
        liStat = $(this).hasClass('liDisabled') // Status do submenu
        dropdownMenu = $(this).siblings(); // Submenu
        dropdownArrow = $(this).children().eq(2); // Icone de seta
        dropdownDesc = $(this).children().eq(0); // Icone de descrição

        if (liStat) { // Verifica se se o submenu esta fechado
            // Se estiver fechado, oculta qualquer outro submenu e abre o que foi clicado

            hideSubMenu();

            $(this).addClass('liEnabled');
            $(this).removeClass('liDisabled');
            $(dropdownArrow).removeClass('icon-disabled arrow-disabled');
            $(dropdownArrow).addClass('icon-enabled arrow-enabled');
            $(dropdownDesc).removeClass('icon-disabled');
            $(dropdownDesc).addClass('icon-enabled');
            dropdownMenu.slideDown('fast');

        } else {
            // Se nao fecha o submenu
            $(dropdownDesc).addClass('icon-disabled');
            $(dropdownDesc).removeClass('icon-enabled');
            $(dropdownArrow).addClass('icon-disabled arrow-disabled');
            $(dropdownArrow).removeClass('icon-enabled arrow-enabled');
            $(this).addClass('liDisabled');
            $(this).removeClass('liEnabled');
            dropdownMenu.slideUp('fast');
        }


    })

    /* Fim submenu */

    /* Menu Responsivo */
    $('.btn-show-menu').on('click', function () {
        $('body, html').css('overflow-y', 'hidden')
        $('nav.nav-main').css('display', 'block');
        $('nav.nav-main').css('position', 'absolute')
        $('#divMenuEscuro').css('display', 'block')
        $('#divMenuEscuro').css('z-index', '1')
    })

    $('#divMenuEscuro').on('click', function () {
        $('body, html').css('overflow-y', 'auto')
        $('nav.nav-main').css('display', 'none');
        $('#divMenuEscuro').css('display', 'none')
        $('#divMenuEscuro').css('z-index', '0')
    })


})
    /* Fim Menu Responsivo */