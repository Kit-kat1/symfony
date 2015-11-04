//$(function() {
//    getStatus();
//});
//
//function getStatus() {
//    $.getJSON('/dashboard', function(data) {
//        console.log('ssssssss');
//        $('#websites').html(data.websites);
//        $('#up').html(data.up);
//        $('#down').html(data.down);
//    });
//    setTimeout("getStatus()",10000);
//}

jQuery( document ).ready(function( $ ) {
    $("#phoneNumber").mask("+99 (999) 999-9999");
    $('#tokenfield').tokenfield({
        autocomplete: {
            source: ['ROLE_ADMIN','ROLE_SUPER_ADMIN','ROLE_OWNER'],
            delay: 100
        },
        showAutocompleteOnFocus: true
    })
});
//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {

    //$(document).ready(function (){
    //    $("#phoneNumber").mask('+7(999)999-99-99');
    //    });
    //$('#history_back').click(function() {
    //    history.go(-1)
    //});
    //$('.navbar-toggle').click(function () {
    //    $('.navbar-nav').toggleClass('slide-in');
    //    $('.side-body').toggleClass('body-slide-in');
    //    $('#search').removeClass('in').addClass('collapse').slideUp(200);
    //
    //    /// uncomment code for absolute positioning tweek see top comment in css
    //    //$('.absolute-wrapper').toggleClass('slide-in');
    //
    //});
    //
    //// Remove menu for searching
    //$('#search-trigger').click(function () {
    //    $('.navbar-nav').removeClass('slide-in');
    //    $('.side-body').removeClass('body-slide-in');
    //
    //    /// uncomment code for absolute positioning tweek see top comment in css
    //    //$('.absolute-wrapper').removeClass('slide-in');
    //
    //});
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