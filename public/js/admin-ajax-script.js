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

