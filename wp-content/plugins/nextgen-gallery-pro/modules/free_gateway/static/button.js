jQuery(function($) {
    // We move the form to the bottom of the body element. This ensures that the form has no parent element that could use a positioning method other
    // than static, and that has a different z-index value that conflicts (and makes the form appear behind the overlay).
    // This problem happens with the Photocrati Theme
    var free_form_container = $('#ngg_free_form_container').detach();
    var free_form = $('<form/>').attr('id', 'ngg_free_form');
    free_form.append(free_form_container);
    $('body').append(free_form);

    $('#ngg_free_button').on('click', function(event) {
        event.preventDefault();
        var overlay = $('<div id="ngg_free_overlay"></div>');
        $('body').append(overlay);
        $('html, body').addClass('free_form_open');

        // address, city, state, zip, country
        $('#nextgen_free_customer_address, ' +
          '#nextgen_free_customer_city, ' +
          '#nextgen_free_customer_state, ' +
          '#nextgen_free_customer_postal, ' +
          '#nextgen_free_customer_country')
          .toggle(Ngg_Pro_Cart.instance.has_shippable_items);

        $('#ngg_free_form_container').show();
        $('#customer_name').focus();
    });

    $('#ngg_free_button_cancel').on('click', function(event) {
        event.preventDefault();
        $('html, body').removeClass('free_form_open');
        $('#ngg_free_form_container').hide();
        $('#ngg_free_overlay').remove();
    });

    free_form_container.find('input').each(function() {
        $(this).placeholder();
    });

    $('#ngg_free_button_submit').click(function(event) {
        event.preventDefault();
        $(this).attr('disabled', 'disabled');
        $(this).text($(this).attr('data-processing-msg'));
        var post_data = $('#ngg_pro_checkout').serialize() + '&' + $('#ngg_free_form').serialize();

        post_data += "&action=free_checkout";

        var $button = $(this);

        $.post(photocrati_ajax.url, post_data, function(response){
            if (typeof(response) != 'object') {
                response = JSON.parse(response);
            }
            if (typeof(response.error) != 'undefined') {
                $button.removeAttr('disabled');
                $button.text($button.attr('data-submit-msg'));

                alert(response.error);
            } else {
                window.location = response.redirect;
            }
        });
    });
});