document.addEventListener('DOMContentLoaded', function () {
    const FXB = window.FXB;
    if (!FXB || !FXB.dom || !FXB.modal) return;
    const qs = FXB.dom.qs;
    const on = FXB.dom.on;

    const body = document.body;

    // Modal sizing is handled via CSS (flex layout); no JS height math needed.

    on(body, 'click', '#fxb-nav-css', function (e) {
        e.preventDefault();
        FXB.modal.open('.fxb-custom-css');
    });
    on(body, 'click', '.fxb-custom-css .fxb-modal-close', function (e) {
        e.preventDefault();
        FXB.modal.close('.fxb-custom-css');
    });
    on(body, 'click', '.fxb-custom-css .fxb-modal-cancel', function (e) {
        e.preventDefault();
        FXB.modal.close('.fxb-custom-css');
    });

    // no resize handler needed
});
