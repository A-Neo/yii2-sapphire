$(document).ready(function () {

    $('.wrapp_language .wrapp_arrow-tr').on('click', function (e) {
        $(this).parent().find('>.tr').toggle(300);
    });
    $('#tab-clones a').on('click', function (e) {
        $('#tab-clones a').removeClass('active');
        $('#tab-clones a').removeClass('on');
        $(this).addClass('on');
    });
    $('#menu-ctl').on('click', function (e) {
        menu = $('#w-nav-menu');
        if (menu.hasClass('active')) {
            menu.removeClass('active');
            menu.animate({left: '-32vh'})
        } else {
            menu.addClass('active');
            menu.animate({left: '0'})
        }
    });
    $('.link_icon-unlock').on('click', function (e) {
        e.preventDefault();
        $('.link_icon-unlock').hide();
        $('.link_icon-padlock').show();
        $(this).closest('.form-group').find('input').attr('type', 'text');
        return false;
    });

    $('.link_icon-padlock').on('click', function (e) {
        e.preventDefault();
        $('.link_icon-padlock').hide();
        $('.link_icon-unlock').show();
        $(this).closest('.form-group').find('input').attr('type', 'password');
        return false;
    });

    $('.nav-item a').on('click', function (e) {
        var target = $(this).attr('href');
        if(target && (t = $(this).closest('.nav-tabs').next().find('.tab-pane'+target))) {
            e.preventDefault();
            $(this).closest('.nav-tabs').find('a').removeClass('on');
            $(this).closest('.nav-tabs').next().find('.tab-pane').removeClass('active');
            t.addClass('active');
            $(this).addClass('on');
            return false;
        }
        return true;
    });
    
    $('.wallet_type').change( _ => {
        $('.wallet_payout_description').hide()
        switch($('.wallet_type').val()) {
            case 'payeer':
                $('.wallet_payeer').show();
                break
            case 'perfect':
                $('.wallet_perfect').show();
                break
            case 'tether':
                $('.wallet_tether').show();
                break
            case 'banki_rf':
                $('.wallet_banki_rf').show();
                break
            case 'dc':
                $('.wallet_dc').show();
                break
            default:
                break;
          }
        console.log($('.wallet_type').val())
    })

});