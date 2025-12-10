import Routing from 'fos-router';

$(document).ready(function() {
    $('.js-permission-check:not(.disabled)').click(function(evt) {
        evt.preventDefault();
        let elem = $(this);
        let currData = $(this).data();
        let currUrl = Routing.generate('admin_update_list_perm', { id: currData.list });
        $.ajax({
            url: currUrl,
            method: 'POST',
            data: {
                perm: currData.role,
                userId: currData.user
            },
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown + '(' + textStatus + ')');
            },
            success: function(data, textStatus, jqXHR) {
                elem.toggleClass('text-success fa-check').toggleClass('text-danger fa-times');
            }
        });
    })
})