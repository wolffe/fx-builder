; (function () {
    const FXB = window.FXB;
    if (!FXB || !FXB.dom || !FXB.modal || typeof FXB.modal.open !== 'function') return;
    const qs = FXB.dom.qs;
    const on = FXB.dom.on;

    let originalColBg = '';

    function getColBgField(colEl) {
        if (!colEl) return '';
        const idx = colEl.getAttribute('data-col_index');
        return idx ? idx + '_bg_color' : '';
    }

    function applyColBgPreview(colEl, modalEl) {
        if (!colEl || !modalEl) return;
        const field = getColBgField(colEl);
        if (!field) return;
        const input = qs('[data-row_field="' + field + '"]', modalEl);
        if (input && input.value) colEl.style.setProperty('--fxb-col-bg-color', input.value);
        else colEl.style.removeProperty('--fxb-col-bg-color');
    }

    document.addEventListener('DOMContentLoaded', function () {
        on(document.body, 'click', '.fxb-col-settings-trigger', function (e, settingsBtn) {
            e.preventDefault();
            const colEl = settingsBtn.closest('.fxb-col');
            const dataTarget = settingsBtn.getAttribute('data-target');
            const container = settingsBtn.parentElement;
            const modal = dataTarget && container ? qs(dataTarget, container) : null;
            if (!colEl || !modal) return;

            const field = getColBgField(colEl);
            const input = field ? qs('[data-row_field="' + field + '"]', modal) : null;
            originalColBg = input ? (input.value || '') : '';
            FXB.modal.open(modal);
        });

        on(document.body, 'click', '.fxb-col-settings .fxb-modal-close', function (e, closeBtn) {
            e.preventDefault();
            const modalEl = closeBtn.closest('.fxb-modal');
            const colEl = closeBtn.closest('.fxb-col');
            if (colEl && modalEl) {
                applyColBgPreview(colEl, modalEl);
                FXB.modal.close(modalEl);
                originalColBg = '';
            }
        });

        on(document.body, 'click', '.fxb-col-settings .fxb-modal-cancel', function (e, cancelBtn) {
            e.preventDefault();
            const modalEl = cancelBtn.closest('.fxb-modal');
            const colEl = cancelBtn.closest('.fxb-col');
            if (!modalEl || !colEl) return;

            const field = getColBgField(colEl);
            const input = field ? qs('[data-row_field="' + field + '"]', modalEl) : null;
            if (input) input.value = originalColBg;
            applyColBgPreview(colEl, modalEl);
            FXB.modal.close(modalEl);
            originalColBg = '';
        });

        document.body.addEventListener('input', function (e) {
            const t = e.target;
            if (!(t instanceof Element)) return;
            if (!t.matches('.fxb-col-settings [data-row_field$="_bg_color"]')) return;
            const modalEl = t.closest('.fxb-col-settings');
            const colEl = t.closest('.fxb-col');
            if (!modalEl || !colEl) return;
            if (t.value) colEl.style.setProperty('--fxb-col-bg-color', t.value);
            else colEl.style.removeProperty('--fxb-col-bg-color');
        });

        document.body.addEventListener('keydown', function (e) {
            const target = e.target;
            if (!(target instanceof Element)) return;
            if (e.key === 'Enter' || e.keyCode === 13) {
                if (target.closest('.fxb-col-settings')) {
                    e.preventDefault();
                }
            }
        });
    });
})();
