// Toggle the side navigation
$("#sidebarToggle").click(function(e) {
    e.preventDefault();
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
});

