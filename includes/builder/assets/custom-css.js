jQuery(document).ready(function ($) {
    /**
     * OPEN CUSTOM CSS MODAL
     */
    $(document.body).on('click', '#fxb-nav-css', function (e) {
        e.preventDefault();

        /* Show Editor Modal & Modal Overlay */
        $('.fxb-custom-css').show();
        $('.fxb-modal-overlay').show();

        /* Fix Height */
        $('.fxb-custom-css .fxb-modal-content').css("height", $('.fxb-custom-css').height() - 35 + "px");
        $(window).resize(function () {
            $('.fxb-custom-css .fxb-modal-content').css("height", "auto").css("height", $('.fxb-custom-css').height() - 35 + "px");
        });
    });

    /**
     * CLOSE CUSTOM CSS MODAL
     */
    $(document.body).on('click', '.fxb-custom-css .fxb-modal-close', function (e) {
        e.preventDefault();

        /* Show Editor Modal & Modal Overlay */
        $('.fxb-custom-css').hide();
        $('.fxb-modal-overlay').hide();
    });
});
