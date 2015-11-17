jQuery( document ).ready(function( $ ) {
    $('.delete-website').on('click', function() {
        var id = $(this).attr('data-item');
        $.ajax({
            url: '/profile/website/delete',
            dataType: 'json',
            data: { "id": id },
            type: 'DELETE',
            complete: function() {
                location.reload();
            }
        });
    });

    $('.delete-user').on('click', function() {
        var id = $(this).attr('data-item');
        $.ajax({
            url: '/admin/user/delete',
            dataType: 'json',
            data: { "id": id },
            type: 'DELETE',
            complete: function() {
                location.reload();
            }
        });
    });

    $('#fos_user_registration_form_phoneNumber').mask("+99 (999) 999-9999");
    $(".phoneNumber").mask("+99 (999) 999-9999");
    $('.tokenfield').tokenfield({
        autocomplete: {
            source: ['ROLE_ADMIN','ROLE_SUPER_ADMIN','ROLE_OWNER', 'ROLE_USER'],
            delay: 100
        },
        showAutocompleteOnFocus: true
    });
});

$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
});