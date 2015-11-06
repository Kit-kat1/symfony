/**
 * Created by gunko on 11/5/15.
 */
$(document).ready(function() {
    $(".submit").click(function(){
        var websites = [];
        var i = 0;
        $("input[name='websites[]']:checked").each(function() {
            websites[i] = $(this).val();
            i++;
        });
        console.log(websites);
        var user = $(".user_id").val();
        var data = {
            dd: '0',
            user: user,
            website: websites
        };
        console.log(data);
        $.ajax({
            url: '/profile/website/notification/save',
            type: "POST",
            dataType: 'json',
            data: data,
            complete: function () {
                    alert("Information about notifying added to database!");
            }
        });
    });

    $(".saveNotifyingList").click(function(){
        var users = [];
        var i = 0;
        $(".notifyingList a").each(function() {
            users[i] = $(this).text();
            i++;
        });
        var website = $(".website_id").val();
        var data = {
            dd: '1',
            user: users,
            website: website
        };
        console.log(data);
        $.ajax({
            url: '/profile/website/notification/save',
            type: "POST",
            dataType: 'json',
            data: data,
            complete: function () {
                alert("Information about notifying added to database!");
            }
        });
    });
});
