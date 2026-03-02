/**
 * Ideal Mega Menu - Admin JavaScript
 *
 * @package Ideal_Mega_Menu
 * @version 1.0.0
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        initImageUpload();
        initMegaMenuToggle();
    });

    /**
     * Initialize media uploader for menu item images.
     */
    function initImageUpload() {
        $(document).on('click', '.imm-upload-image', function (e) {
            e.preventDefault();

            var button = $(this);
            var inputField = button.siblings('.imm-image-url');

            var mediaUploader = wp.media({
                title: 'Select Menu Item Image',
                button: {
                    text: 'Use This Image',
                },
                multiple: false,
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader
                    .state()
                    .get('selection')
                    .first()
                    .toJSON();
                inputField.val(attachment.url);
            });

            mediaUploader.open();
        });
    }

    /**
     * Toggle mega menu column settings visibility.
     */
    function initMegaMenuToggle() {
        $(document).on(
            'change',
            'input[id^="edit-menu-item-imm-mega-menu-"]',
            function () {
                var isChecked = $(this).is(':checked');
                var settingsBlock = $(this).closest('.imm-menu-item-settings');
                var columnsField = settingsBlock.find(
                    '.field-imm-columns'
                );

                if (isChecked) {
                    columnsField.slideDown(200);
                } else {
                    columnsField.slideUp(200);
                }
            }
        );
    }
})(jQuery);
