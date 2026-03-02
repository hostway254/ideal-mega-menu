/**
 * Ideal Mega Menu – Admin JS
 */
(function ($) {
    'use strict';

    $(document).ready(function () {

        // ─── Tabs ───
        $('.imm-tabs .nav-tab').on('click', function (e) {
            e.preventDefault();
            var target = $(this).attr('href');

            $('.imm-tabs .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            $('.imm-tab-content').removeClass('imm-tab-active');
            $(target).addClass('imm-tab-active');
        });

        // ─── Color pickers ───
        $('.imm-color-picker').wpColorPicker();

    });
})(jQuery);