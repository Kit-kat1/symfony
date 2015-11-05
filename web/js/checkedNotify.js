/**
 * Created by gunko on 11/5/15.
 */
$(document).ready(function() {
    console.log("ready");
    $(".submit").click(function(){
        var websites = [];
        var i = 0;
        $("input[name='websites[]']:checked").each(function() {
            websites[i] = $(this).val();
            i++;
        });
        console.log(websites);
        var user = $(".user").val();
        var data = {
            user: user,
            website: websites
        };
        console.log(data);
        $.ajax({
            url: '/profile/website/notification/save',
            type: "POST",
            dataType: 'json',
            //contentType: 'application/json; charset=UTF-8',
            data: JSON.stringify(data),
            success: function(data) {
                alert (data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert('Error: ' +  errorThrown);
            }
        });
    });
});
