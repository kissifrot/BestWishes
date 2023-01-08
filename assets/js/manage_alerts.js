import Routing from 'fos-router';

$(document).ready(function() {
    $('.js-alert-check:not(.disabled)').click(function(evt) {
        evt.preventDefault();
        let elem = $(this);
        let currData = $(this).data();
        let currUrl = Routing.generate('user_update_list_alert', { id: currData.list });
        $.ajax({
            url: currUrl,
            method: 'POST',
            data: {
                alert: currData.alert
            },
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown) {

            },
            success: function(data, textStatus, jqXHR) {
                elem.toggleClass('text-success fa-check').toggleClass('text-danger fa-times');
            }
        });
    })
})
