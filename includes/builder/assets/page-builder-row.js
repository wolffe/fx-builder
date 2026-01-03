; (function () {
    const FXB = window.FXB;
    if (!FXB || !FXB.dom || !FXB.modal || !FXB.rows || !FXB.items || typeof FXB.reconcile !== 'function') return;
    const qs = FXB.dom.qs;
    const on = FXB.dom.on;

    function reconcileRowsOnly() {
        FXB.reconcile({ sortItems: false, iframes: false });
    }

    function getRowConfigFromThumb(thumbEl) {
        const rowId = Date.now();
        const layout = thumbEl.getAttribute('data-row-layout') || '1';
        const colNum = thumbEl.getAttribute('data-row-col_num') || '1';

        // Provide defaults for all fields referenced in templates to avoid "undefined".
        return {
            id: rowId,
            index: '1',
            state: 'open',
            layout: layout,
            col_num: colNum,
            col_1: '',
            col_2: '',
            col_3: '',
            col_4: '',
            col_5: '',
            row_title: '',
            row_html_width: 'default',
            row_html_height: '',
            row_html_height_unit: 'px',
            row_html_id: '',
            row_html_class: '',
            row_column_align: 'start',
            row_column_gap: '',
            row_column_gap_unit: 'px'
        };
    }

    // Modal sizing handled via CSS (flex layout).

    document.addEventListener('DOMContentLoaded', function () {
        reconcileRowsOnly();

        // Row sortable.
        const fxb = document.getElementById('fxb');
        if (fxb && typeof Sortable !== 'undefined') {
            new Sortable(fxb, {
                handle: '.fxb-row-handle',
                animation: 150,
                onEnd: function () {
                    reconcileRowsOnly();
                }
            });
        }

        // Add new row from layout thumb.
        on(document.body, 'click', '.fxb-add-row .layout-thumb', function (e, thumb) {
            e.preventDefault();
            const rowTemplate = FXB.templates.get('fxb-row');
            if (!rowTemplate) return;
            const addRowWrap = thumb.closest('.fxb-add-row');
            const method = addRowWrap ? addRowWrap.getAttribute('data-add_row_method') : 'append';
            const cfg = getRowConfigFromThumb(thumb);
            const html = rowTemplate(cfg);
            if (fxb) {
                if (method === 'prepend') fxb.insertAdjacentHTML('afterbegin', html);
                else fxb.insertAdjacentHTML('beforeend', html);
            }
            FXB.reconcile({ iframes: false });
        });

        // Remove row.
        on(document.body, 'click', '.fxb-remove-row', function (e, removeBtn) {
            e.preventDefault();
            const msg = removeBtn.getAttribute('data-confirm') || 'Delete row?';
            if (!confirm(msg)) return;
            const rowEl = removeBtn.closest('.fxb-row');
            if (rowEl) rowEl.remove();
            reconcileRowsOnly();
        });

        // Toggle row state.
        on(document.body, 'click', '.fxb-toggle-row', function (e, toggleBtn) {
            e.preventDefault();
            const row = toggleBtn.closest('.fxb-row');
            if (!row) return;
            let state = (row.dataset && row.dataset.state) ? row.dataset.state : row.getAttribute('data-state');
            state = (state === 'open') ? 'close' : 'open';
            row.dataset.state = state;
            row.setAttribute('data-state', state);
            const stateInput = qs('input[data-row_field="state"]', row);
            if (stateInput) stateInput.value = state;
        });

        // Open row settings.
        on(document.body, 'click', '.fxb-settings', function (e, settingsBtn) {
            e.preventDefault();
            const dataTarget = settingsBtn.getAttribute('data-target');
            const container = settingsBtn.parentElement;
            const modal = dataTarget && container ? qs(dataTarget, container) : null;
            if (modal) FXB.modal.open(modal);
        });

        // Close settings (Apply).
        on(document.body, 'click', '.fxb-row-settings .fxb-modal-close', function (e, closeBtn) {
            e.preventDefault();
            const modalEl = closeBtn.closest('.fxb-modal');
            const rowEl = closeBtn.closest('.fxb-row');
            if (rowEl && modalEl) {
                const titleInput = qs('input[data-row_field="row_title"]', modalEl);
                const title = titleInput ? titleInput.value : '';
                const titleBadge = qs('.fxb_row_title', rowEl);
                if (titleBadge) {
                    titleBadge.dataset.rowTitle = title;
                    titleBadge.setAttribute('data-row-title', title);
                }
                FXB.modal.close(modalEl);
            }
        });

        // Prevent Enter in settings modal from submitting the post form.
        document.body.addEventListener('keydown', function (e) {
            const target = e.target;
            if (!(target instanceof Element)) return;
            if (e.key === 'Enter' || e.keyCode === 13) {
                if (target.closest('.fxb-row-settings')) {
                    e.preventDefault();
                }
            }
        });

        // No resize handler needed (CSS handles modal sizing).

        // mousedown/mouseup visual grabbing indicator.
        on(document.body, 'mousedown', '.fxb-grab', function (e, grab) {
            grab.classList.add('fxb-grabbing');
        });
        on(document.body, 'mouseup', '.fxb-grab', function (e, grab) {
            grab.classList.remove('fxb-grabbing');
        });

        // Layout change: update row dataset + hidden input.
        document.body.addEventListener('change', function (e) {
            const t = e.target;
            if (!(t instanceof Element)) return;
            if (t.matches('select[data-row_field="layout"]')) {
                const row = t.closest('.fxb-row');
                if (!row) return;
                const newLayout = t.value;
                const opt = t.options[t.selectedIndex];
                const newColNum = opt ? opt.getAttribute('data-col_num') : '';

                row.dataset.layout = newLayout;
                row.setAttribute('data-layout', newLayout);
                if (newColNum) {
                    row.dataset.col_num = newColNum;
                    row.setAttribute('data-col_num', newColNum);
                    const colInput = qs('input[data-row_field="col_num"]', row);
                    if (colInput) {
                        colInput.value = newColNum;
                    }
                }
            }
        });

    });
})();
