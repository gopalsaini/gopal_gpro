
//sidebar open and close
$(".toggle").on("click", function () {
    $("#sidebar").toggleClass("open");
});
$("#toggle_close").on("click", function () {
    $("#sidebar").removeClass("open");
});

        // menu fixed
        $(window).scroll(function () {
            var window_top = $(window).scrollTop() + 1;
            if (window_top > 100) {
            $('header').addClass('menu-fixed animated fadeInDown');
            } else {
            $('header').removeClass('menu-fixed animated fadeInDown');
            }
        });

	// responsive sab menu
    (function ($) {
        $(document).ready(function () {

            $('#cssmenu li.active').addClass('open').children('ul').show();
            $('#cssmenu li.has-sub>a').on('click', function () {
                $(this).removeAttr('href');
                var element = $(this).parent('li');
                if (element.hasClass('open')) {
                    element.removeClass('open');
                    element.find('li').removeClass('open');
                    element.find('ul').slideUp(200);
                }
                else {
                    element.addClass('open');
                    element.children('ul').slideDown(200);
                    element.siblings('li').children('ul').slideUp(200);
                    element.siblings('li').removeClass('open');
                    element.siblings('li').find('li').removeClass('open');
                    element.siblings('li').find('ul').slideUp(200);
                }
            });
        });
    })(jQuery);

// banner slider
$('.au-banner-wrap .owl-carousel').owlCarousel({
    loop:true,
    margin:10,
    nav:false,
    dots:true,
    autoplay: true,
    autoplayTimeout: 6000,
    smartSpeed: 6000,
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    responsive:{
        0:{
            items:1
        },
        1000:{
            items:1
        }
    }
});