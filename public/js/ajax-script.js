//---------------------------------------------
// Setting up the default AJAX calls
//---------------------------------------------

$.ajaxSetup({
    type: "POST",
    //adding our csrf token security
    headers : {
        "Csrftoken": $("meta[name=\"csrf_token\"]").attr("content")
    }
});

//reforce the settings by calling this function.
function setupAjax() {
    $.ajaxSetup({
        type: "POST",
        //adding our csrf token security
        headers : {
            "csrftoken": $("meta[name=\"csrf_token\"]").attr("content")
        }
    });
}