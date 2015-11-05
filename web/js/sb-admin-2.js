jQuery( document ).ready(function( $ ) {
    $("#phoneNumber").mask("+99 (999) 999-9999");
    $('#tokenfield').tokenfield({
        autocomplete: {
            source: ['ROLE_ADMIN','ROLE_SUPER_ADMIN','ROLE_OWNER'],
            delay: 100
        },
        showAutocompleteOnFocus: true
    });

    //var url = Routing.generate('filterUsers', {filter: 'WILDCARD'});
    //
    //// Trigger typeahead + bloodhound
    //var users = new Bloodhound({
    //    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    //    queryTokenizer: Bloodhound.tokenizers.whitespace,
    //    prefetch: Routing.generate('allUsers'),
    //    remote: {
    //        url: url,
    //        wildcard: 'WILDCARD'
    //    }
    //});
    //
    //users.initialize();
    //
    //$('#users_typeahead .typeahead').typeahead(null, {
    //    name: 'users',
    //    displayKey: 'value',
    //    source: users.ttAdapter()
    //});

    //var engine = new Bloodhound({
    //    datumTokenizer: Bloodhound.tokenizers.whitespace,
    //    queryTokenizer: Bloodhound.tokenizers.whitespace,
    //    remote: {
    //        url: 'users/getAll/%QUERY',
    //        wildcard: '%QUERY',
    //        filter: function (data) {
    //            return $.map(data, function(type) {
    //                console.log(type['users']);
    //                return {value: type['users']};
    //            })
    //        }
    //    }
    //});
    //
    //engine.initialize();
    //
    //$('#typeahead').typeahead(null, {
    //    name: 'engine',
    //    display: 'value',
    //    source: engine.ttAdapter()
    //});
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