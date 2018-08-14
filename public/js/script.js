//---------------------------------------------
// The navbar on top if scroll up
//---------------------------------------------
//navbar slide in effects and shown on scroll
//---------------------------------------------

function isScrollingUp($currentTop) {
    if ($currentTop > 1 && $("#mainNav").hasClass("is-fixed")) {
        $("#mainNav").addClass("is-visible");
    } else {
        $("#mainNav").removeClass("is-visible is-fixed");
    }
}

function isScrollingDown($currentTop) {
    $("#mainNav").removeClass("is-visible");
    if ($currentTop > $("#mainNav").height() && !$("#mainNav").hasClass("is-fixed")) {
        $("#mainNav").addClass("is-fixed");
    }
}

//setting the initial position to 0
var $previousTop = 0;

//Minimum screen width, don't want the effect on mobile. Avoids IOS stuttering bug
var MQL = 992;

if ($(window).width() > MQL) {
    $(window).on("scroll",
        function () {
            var $currentTop = $(window).scrollTop(); //our position in the window, returns the number of pixels from the window top to the site top
            //check if user is scrolling up or down
            if ($currentTop < $previousTop) {
                isScrollingUp($currentTop);
            } else {
                isScrollingDown($currentTop);
            }
            $previousTop = $currentTop;
        });
}

//---------------------------------------------
//end navbar
//---------------------------------------------


//---------------------------------------------
// Init wow for animation on scroll
//---------------------------------------------
/*global WOW*/
new WOW().init();
