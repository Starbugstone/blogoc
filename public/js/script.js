//---------------------------------------------
// The navbar on top if scroll up
//---------------------------------------------

// Show the navbar when the page is scrolled up
var MQL = 992; //Minimum screen width, don't want the effect on mobile

//primary navigation slide-in effect
if ($(window).width() > MQL) {
    var headerHeight = $('#mainNav').height();
    $(window).on('scroll', {
            previousTop: 0
        },
        function() {
            var currentTop = $(window).scrollTop();
            //check if user is scrolling up
            if (currentTop < this.previousTop) {
                //if scrolling up...
                if (currentTop > 0 && $('#mainNav').hasClass('is-fixed')) {
                    $('#mainNav').addClass('is-visible');
                } else {
                    $('#mainNav').removeClass('is-visible is-fixed');
                }
            } else if (currentTop > this.previousTop) {
                //if scrolling down...
                $('#mainNav').removeClass('is-visible');
                if (currentTop > headerHeight && !$('#mainNav').hasClass('is-fixed')) $('#mainNav').addClass('is-fixed');
            }
            this.previousTop = currentTop;
        });
}
//---------------------------------------------
//end navbar
//---------------------------------------------


//---------------------------------------------
// Init wow for animation on scroll
//---------------------------------------------
new WOW().init();
