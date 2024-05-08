$( document ).ready(function() {
    $('.nav-link-hamburger').on('click', function (evt) {
        evt.preventDefault();
        if ($(window).width() <= '1024') {
        $('.main-sidebar').toggle();
        $('.main').toggle();
        } else {
            $('.main-sidebar-desktop').toggle();
        }
    })
});