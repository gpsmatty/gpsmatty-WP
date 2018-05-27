(function($) {
    $('span.tooltip, label.tooltip').tooltip();

    $.datetimepicker.setLocale('en');

    var $date_start = $('#coupon_date_start');
    var $date_end   = $('#coupon_date_end');
    var format = 'Y-m-d';

    $date_start.datetimepicker({
        timepicker: false,
        format: format,
        onShow: function() {
            this.setOptions({
                maxDate: $date_end.val() ? $date_end.val() : false
            });
        }
    });

    $date_end.datetimepicker({
        timepicker: false,
        format: format,
        onShow: function() {
            this.setOptions({
                minDate: $date_start.val() ? $date_start.val() : false
            });
        }
    });
})(jQuery);
