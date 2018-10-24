//---------------------------------------------
// Setting up the default AJAX calls
//---------------------------------------------

$.ajaxSetup({
    type: "POST",
    //adding our csrf token security
    headers : {
        "csrf_token": $("meta[name=\"csrf_token\"]").attr("content")
    }
});

//reforce the settings by calling this function.
function setupAjax() {
    $.ajaxSetup({
        type: "POST",
        //adding our csrf token security
        headers : {
            "csrf_token": $("meta[name=\"csrf_token\"]").attr("content")
        }
    });
}