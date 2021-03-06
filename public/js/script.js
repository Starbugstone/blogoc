//---------------------------------------------
// The navbar on top if scroll up
//---------------------------------------------
//navbar slide in effects and shown on scroll
//---------------------------------------------

function isScrollingUp($currentTop) {
    var $mainNav = $("#mainNav");
    if ($currentTop > 1 && $mainNav.hasClass("is-fixed")) {
        $mainNav.addClass("is-visible");
    } else {
        $mainNav.removeClass("is-visible is-fixed");
    }
}

function isScrollingDown($currentTop) {
    var $mainNav = $("#mainNav");
    $mainNav.removeClass("is-visible");
    if ($currentTop > $mainNav.height() && !$mainNav.hasClass("is-fixed")) {
        $mainNav.addClass("is-fixed");
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


//---------------------------------------------
// Setup TinyMce for comments
//---------------------------------------------
function setupTinymce($selector) {
    //getting our base URL
    var url = window.location.origin;
    tinymce.init({
        selector: $selector,

        /* display statusbar */
        statubar: false,
        branding: false,

        /* theme of the editor */
        theme: "modern",
        skin: "lightgray",

        /* width and height of the editor */
        min_height: 450,

        /* plugin */
        plugins: [
            "lists textcolor emoticons"
        ],

        /* toolbar */
        menubar: false,
        toolbar: [
            "bold italic underline strikethrough | bullist numlist emoticons | alignleft aligncenter alignright alignjustify | undo redo | alignnone removeformat"
        ],



    });
}

$("#next").mouseover(function() {
    $( this ).addClass("jello");
})
.mouseout(function() {
    $( this ).removeClass("jello");
});

$("#previous").mouseover(function() {
    $( this ).addClass("jello");
})
    .mouseout(function() {
        $( this ).removeClass("jello");
    });