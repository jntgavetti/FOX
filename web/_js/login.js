  /* Nivel de acesso */

  $('.body-login #save').on('click', function (e) {
    e.preventDefault();
    $('p.aviso').text("");

    let dados_form = $('.body-login form').serialize();
    $.ajax({
        type: 'POST',
        async: true,
        data: dados_form,
        url: '_controler/valida_login.php',
        complete: function (msg) {
            if (msg.responseText.length != 0) {
                $('p.aviso').text(msg.responseText);
                $('p.aviso').css('display', 'block');
            } else {
                window.location.href = "main.php";
            }


        }
    })
})

$('.body-login form input').on('click', function (e) {
    $('p.aviso').text("");
})

