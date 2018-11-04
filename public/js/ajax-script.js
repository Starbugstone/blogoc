//Setting up the default ajax calls
$.ajaxSetup({
    //adding our csrf token security
    headers : {
        "csrf_token": $("meta[name=\"csrf_token\"]").attr("content")
    }
});



//test ajax request response
$("#ajaxtest").click(function(){
    $.ajax({
        url: "/ajax/debug/test",
        success: function($result){
            $("#ajaxtest").html($result.status);
        }
    });
});