//Setting up the default ajax calls
function setupAjax() {
    $.ajaxSetup({
        type: "POST",
        //adding our csrf token security
        headers: {
            "csrf_token": $("meta[name=\"csrf_token\"]").attr("content")
        }

    });
}

//Setting the toastr alert buttons
function setupToastr(){
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "6000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };
}

