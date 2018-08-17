$("#ajaxtest").click(function(){
    $.ajax({
        url: "/ajax/debug/test",
        success: function(result){
            $("#div1").html(result);
        }
    });
});