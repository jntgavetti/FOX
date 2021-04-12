// Função de loading
function page_loading(status) {
    if (status == true) {
        $('.div_loading_main').css('display', 'block');
    } else {
        $('.div_loading_main').css('display', 'none');
    }
}page_loading(true);
 // Funcao padrao de requisicao ajax
 function envia_padrao(dados, action, urlPost) {
    let dados_form = dados + "&action=" + action;


    let req = $.ajax({
        type: 'POST',
        cache: false,
        url: urlPost,
        async: true,
        data: dados_form
    })

    return req;

}

function modal_loading(status) {
    $('.modal-body .status').html('')
    $('.modal .modal-body .div_detalhe').hide();
    $('.modal .modal-body .detalhe_desc').hide();

    if (status == true) {
        $('.modal').modal('show');
        $('.modal-body .div_loading').css('display', 'block');
    } else {
        $('.modal-body .div_loading').css('display', 'none');
    }
}

function exibe_mudancas(status, msg, detalhe) {

    $('.modal .modal-body .div_detalhe').html("");


    if (status == 1) {
        
        $('.modal .modal-body .form-popup').hide()
        $('.modal .modal-body .div_loading').hide()
        $('.modal .modal-title').text('Sucesso!');
        $('.modal .modal-body p.status').html(msg).css('color', 'green');
        $('.modal .modal-body p.status').show();

        if (typeof arguments[2] == 'undefined') {
            $('.modal .modal-body .div_detalhe').hide();
            $('.modal .modal-body .detalhe_desc').hide();
        } else {
            $('.modal .modal-body .div_detalhe').html(detalhe);
            $('.modal .modal-body .div_detalhe').show();
            $('.modal .modal-body .detalhe_desc').show();
        }

        $('.modal').modal('show');

    } else {

        $('.modal .modal-body .div_loading').hide()
        $('.modal .status').css('color', 'red');
        $('.modal .status').text(msg);
        $('.modal .modal-title').text("Erro");



        if (typeof arguments[2] == 'undefined') {
            $('.modal .modal-body .div_detalhe').hide();
            $('.modal .modal-body .detalhe_desc').hide();
        } else {
            $('.modal .modal-body .div_detalhe').html(detalhe);
            $('.modal .modal-body .div_detalhe').show();
            $('.modal .modal-body .detalhe_desc').show();
        }
        $('.modal').modal('show');
    }




}

function validaInput(status, el) {
    if (status == 0) {
        $(el).addClass('wrong')

    } else {
        $(el).removeClass('wrong')
    }
}

function dragElement(e, elmnt) {
    var pai, pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;


    el = document.getElementById(elmnt);

    $('.draggable').css('display', 'flex')
    el.style.position = 'absolute'
    pos3 = e.clientX;
    pos4 = e.clientY;

    document.onmouseup = closeDragElement;
    document.onmousemove = elementDrag;

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;

        // set the element's new position:
        el.style.top = (el.offsetTop - pos2) + "px";


        $(document).on('mouseenter', '.div_topico', function (e) {
            pai = this;
        })

    }

    function closeDragElement() {
        /* stop moving when mouse button is released:*/
        document.onmouseup = null;
        document.onmousemove = null;

        if (pai) { pai.append(el); }

        el.style.top = 'inherit'
        el.style.position = 'static'
        el.id = ''
        $('.draggable').css('display', 'none');
    }
}


// -----------------------


$(document).ready(function () {
    
    nivel_login = $('.nivelAcesso');
    if (nivel_login.hasClass('admin')) {
        nivel_login.text("#");
    } else {
        nivel_login.text("$");
    }  
    page_loading(false);

})