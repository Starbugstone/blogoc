//---------------------------------------------
// The navbar on top if scroll up
//---------------------------------------------
//navbar shown on scroll
//---------------------------------------------

function scrollingUp($currentTop){
    if ($currentTop > 1 && $("#mainNav").hasClass("is-fixed")) {
        $("#mainNav").addClass("is-visible");
    } else {
        $("#mainNav").removeClass("is-visible is-fixed");
    }
}

function scrollingDown($currentTop){
    $("#mainNav").removeClass("is-visible");
    if ($currentTop > $("#mainNav").height() && !$("#mainNav").hasClass("is-fixed")){
        $("#mainNav").addClass("is-fixed");
    }
}

//setting the initial position to 0
var $previousTop = 0;
// Show the navbar when the page is scrolled up
var MQL = 992; //Minimum screen width, don't want the effect on mobile. Avoids IOS bug

//primary navigation slide-in effect
if ($(window).width() > MQL) {
    $(window).on("scroll",
        function() {
            var $currentTop = $(window).scrollTop(); //our position in the window, returns the number of pixels from the window top to the site top
            //check if user is scrolling up
            if ($currentTop < $previousTop) {
                //if scrolling up...
                scrollingUp($currentTop);
            } else {
                //scrolling down...
                scrollingDown($currentTop);
            }
            $previousTop = $currentTop;
        }
        );
}

//---------------------------------------------
//end navbar
//---------------------------------------------


//---------------------------------------------
// Init wow for animation on scroll
//---------------------------------------------
/*global WOW*/
new WOW().init();
