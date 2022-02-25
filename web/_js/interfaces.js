
$('.acc_header_phy').on('click', function () {
    
    if($('.tr_phy').is(':visible')){
        $('.acc_header_phy .coll_icon').removeClass('fa-minus-square').addClass('fa-plus-square')
        $('.tr_phy').hide()
    }else{
        $('.acc_header_phy .coll_icon').removeClass('fa-plus-square').addClass('fa-minus-square')
        $('.tr_phy').show()
    }
    
    
    
    
})

$('.acc_header_vir').on('click', function () {
    if($('.tr_vir').is(':visible')){
        $('.acc_header_vir .coll_icon').removeClass('fa-minus-square').addClass('fa-plus-square')
        $('.tr_vir').hide()
    }else{
        $('.acc_header_vir .coll_icon').removeClass('fa-plus-square').addClass('fa-minus-square')
        $('.tr_vir').show()
    }
})
