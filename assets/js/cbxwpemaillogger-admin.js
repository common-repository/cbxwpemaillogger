(function ($) {
    'use strict';

    $(document).ready(function ($) {

        $('#cbxscratingreviewlogsearch-search-input').attr("placeholder", cbxwpemaillogger_dashboard.search_placeholder);


        var $ranges = {};

        $ranges[cbxwpemaillogger_dashboard.daterangepicker.today]        = [moment(), moment()];
        $ranges[cbxwpemaillogger_dashboard.daterangepicker.yesterday]    = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
        $ranges[cbxwpemaillogger_dashboard.daterangepicker.last_7_days]  = [moment().subtract(6, 'days'), moment()];
        $ranges[cbxwpemaillogger_dashboard.daterangepicker.last_30_days] = [moment().subtract(29, 'days'), moment()];
        $ranges[cbxwpemaillogger_dashboard.daterangepicker.this_month]   = [moment().startOf('month'), moment().endOf('month')];
        $ranges[cbxwpemaillogger_dashboard.daterangepicker.last_month]   = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

        $("#cbxscratingreviewlog-logdate-input").daterangepicker({
            timePicker: false,
            opens: 'left',
            autoUpdateInput: false,
            ranges: $ranges,
            locale: {
                format: 'DD-MMM-YYYY',
                cancelLabel: cbxwpemaillogger_dashboard.daterangepicker.clear,
                customRangeLabel: cbxwpemaillogger_dashboard.daterangepicker.custom_range,
                applyLabel: cbxwpemaillogger_dashboard.daterangepicker.apply,
            }
        });

        $("#cbxscratingreviewlog-logdate-input").on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $("#cbxscratingreviewlog-logdate-input").on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });


        //delete email log
        $('.cbxwpemaillogger_actions_delete').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);


            var $id   = Number($this.data('id'));
            var $busy = Number($this.data('busy'));

            if ($busy == 0) {
                Ply.dialog({
                    "confirm-step": {
                        ui: "confirm",
                        data: {
                            text: cbxwpemaillogger_dashboard.deleteconfirm,
                            ok: cbxwpemaillogger_dashboard.deleteconfirmok, // button text
                            cancel: cbxwpemaillogger_dashboard.deleteconfirmcancel
                        },
                        backEffect: "3d-flip[-180,180]"
                    }
                }).always(function (ui) {
                    if (ui.state) {
                        // Ok
                        //send ajax request to delete
                        $this.data('busy', 1);

                        $.ajax({

                            type: "post",
                            dataType: "json",
                            url: cbxwpemaillogger_dashboard.ajaxurl,
                            data: {
                                action: "cbxwpemaillogger_log_delete",
                                id: $id,
                                security: cbxwpemaillogger_dashboard.nonce
                            },
                            success: function (data, textStatus, XMLHttpRequest) {
                                Ply.dialog("alert", data.message);

                                if (Number(data.success) === 1) {
                                    $this.closest('tr.cbxwpemaillogger_row').remove();
                                }

                            }
                        });

                    } else {
                        // Cancel
                        // ui.by — 'overlay', 'x', 'esc'
                    }
                });


            }
        });//end ajax log delete

        //re-send email
        $('.cbxwpemaillogger_actions_resend').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);


            var $id   = Number($this.data('id'));
            var $busy = Number($this.data('busy'));

            if ($busy == 0) {
                Ply.dialog({
                    "confirm-step": {
                        ui: "confirm",
                        data: {
                            text: cbxwpemaillogger_dashboard.resendconfirm,
                            ok: cbxwpemaillogger_dashboard.deleteconfirmok, // button text
                            cancel: cbxwpemaillogger_dashboard.deleteconfirmcancel
                        },
                        backEffect: "3d-flip[-180,180]"
                    }
                }).always(function (ui) {
                    if (ui.state) {
// Ok
                        //send ajax request to delete
                        $this.data('busy', 1);

                        $.ajax({

                            type: "post",
                            dataType: "json",
                            url: cbxwpemaillogger_dashboard.ajaxurl,
                            data: {
                                action: "cbxwpemaillogger_log_resend",
                                id: $id,
                                security: cbxwpemaillogger_dashboard.nonce
                            },
                            success: function (data, textStatus, XMLHttpRequest) {
                                $this.data('busy', 0);

                                Ply.dialog("alert", data.message);
                            }
                        });
                    }
                });
            }//end checking busy

        });//end ajax email resend
    });

})(jQuery);
