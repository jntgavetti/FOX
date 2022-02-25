$(document).ready(function () {
$.easing.def = "easeOutBounce";
    $('.toast').toast({ delay: 5000 });

    function hideSubMenu() { // Função que oculta todos os submenus abertos
        $(".menu-item").addClass('liDisabled');
        $(".menu-item").removeClass('liEnabled');
        $(".menu-item svg").addClass('icon-disabled');
        $(".menu-item svg").removeClass('icon-enabled');
        $(".menu-item .arrow-enabled").addClass('arrow-disabled');
        $(".menu-item .arrow-enabled").removeClass('arrow-enabled');
        $('.menu-item').find('.dropdown-ul').slideUp("fast");
    }

    /* Submenus */
    $(".menu-item").on('click', function () { // Quando clicar no menu principal

        
        liMain = $(this).parent(); // LI pai do link
        liStat = $(this).hasClass('liDisabled') // Status do submenu
        dropdownMenu = $(this).find('.dropdown-ul'); // Submenu
        dropdownArrow = $(this).find('.arrow-icon'); // Icone de seta
        dropdownDesc = $(this).find('.desc-icon'); // Icone de descrição

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