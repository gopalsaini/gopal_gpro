    function countdownStart(){

     
    // countdown strat
        (function() {
            const second = 1000,
                minute = second * 60,
                hour = minute * 60,
                day = hour * 24;

            //I'm adding this section so I don't have to keep updating this pen every year :-)
            //remove this if you don't need it
            // let today = new Date(),
            let today = new Date(new Date())
                    
                dd = String(today.getDate()).padStart(2, "0"),
                mm = String(today.getMonth() + 1).padStart(2, "0"),
                yyyy = today.getFullYear(),
                nextYear = yyyy,
                dayMonth = "11/12/",
                birthday = dayMonth + nextYear;

            today = mm + "/" + dd + "/" + yyyy;
            if (today > birthday) {
                birthday = dayMonth + nextYear;
            }
            //end

            const countDown = new Date(birthday).getTime(),
                x = setInterval(function() {

                    const now = new Date().getTime(),
                        distance = countDown - now;

                        document.getElementById("days").innerText = Math.floor(distance / (day)),
                        document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                        document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                        document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

                    //do something later when date is reached
                    if (distance < 0) {
                        document.getElementById("headline").innerText = "Event Day!";
                        document.getElementById("countdown").style.display = "none";
                        document.getElementById("content").style.display = "block";
                        clearInterval(x);
                    }
                    //seconds
                }, 0)
        }());
    }


// toggle

$(".navbar-toggler").on("click", function() {
    var w = $('#sidebar').width();
    var pos = $('#sidebar').offset().left;

    if (pos === 0) {
        $("#sidebar").animate({ "left": -w }, "slow");
    } else {
        $("#sidebar").animate({ "left": "0" }, "slow");
    }

});
$('#sidebar').on('hide.bs.collapse', function() {
    $('.navbar-toggler').removeClass('open');
})
$('#sidebar').on('show.bs.collapse', function() {
    $('.navbar-toggler').addClass('open');
});

//  timer

//  attend-slider

$('.attend-slider .owl-carousel').owlCarousel({
    loop: true,
    margin: 10,
    nav: true,
    dots: false,
    center: true,
    navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 2
        },
        768: {
            items: 2
        },
        1000: {
            items: 3
        }
    }
})

//  speaker-slider

$('.speaker-slider .owl-carousel').owlCarousel({
    loop: true,
    margin: 10,
    nav: true,
    dots: false,
    navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 2
        },
        1000: {
            items: 3
        }
    }
})

//  testimonial-slider

$('.testimonial-slider .owl-carousel').owlCarousel({
    loop: true,
    margin: 10,
    nav: true,
    dots: false,
    center: true,
    navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 2
        },
        1000: {
            items: 3
        }
    }
})

//  counter 
var isAlreadyRun = false;
$(window).scroll(function() {
    $('.counter-wrapper').each(function(i) {
        var bottom_of_object = $(this).position().top + $(this).outerHeight() / 2;
        var bottom_of_window = $(window).scrollTop() + $(window).height();
        if (bottom_of_window > (bottom_of_object + 20)) {
            if (!isAlreadyRun) {
                $('.counter-count').each(function() {

                    $(this).prop('con counter-right-border', 0).animate({
                        Counter: $(this).text()
                    }, {
                        duration: 3500,
                        easing: 'swing',
                        step: function(now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                });
            }
            isAlreadyRun = true;
        }
    });
});