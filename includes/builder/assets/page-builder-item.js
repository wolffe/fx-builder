; (function () {
    /**
     * FX Builder Items
     */

    const FXB = window.FXB;
    if (!FXB || !FXB.dom || !FXB.items || !FXB.editor || !FXB.modal || typeof FXB.reconcile !== 'function') return;

    const qs = FXB.dom.qs;

    function reconcileAfterItemDomChange() {
        // Avoid reloading all iframe previews for single-item operations.
        FXB.reconcile({
            rows: false,
            bottomAdd: false,
            sortItems: true,
            iframes: false
        });
    }

    function openEditorForTextarea(textareaEl) {
        const modal = qs('.fxb-editor');
        if (!modal) return;

        textareaEl.classList.add('fxb_editing_active');

        const editorId = 'fxb_editor';
        FXB.editor.setModalContent(editorId, textareaEl.value || '');

        FXB.modal.open(modal);
    }

    function closeEditorAndApply() {
        const editorId = 'fxb_editor';
        const itemTextarea = qs('.fxb_editing_active');
        if (itemTextarea) {
            FXB.editor.applyModalToTextarea(editorId, itemTextarea);
            itemTextarea.classList.remove('fxb_editing_active');

            const iframe = itemTextarea.parentElement ? qs('.fxb-item-iframe', itemTextarea.parentElement) : null;
            if (iframe) {
                FXB.items.loadIframeContent(iframe, FXB.items.getIframeCSS());
            }
        }

        FXB.modal.close(qs('.fxb-editor'));
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Ensure visual editor is active
        FXB.editor.switchEditor('fxb_editor');
        const postForm = qs('#post');
        if (postForm) {
            postForm.addEventListener('submit', function () {
                FXB.editor.switchEditor('fxb_editor');
            });
        }

        // One standardized init pass.
        FXB.reconcile();

        // Modal sizing handled via CSS (flex layout).
    });

    const on = FXB.dom.on;

    // Direct edits in item textareas (if user types without opening the modal) should mark dirty.
    on(document.body, 'input', '.fxb-item-textarea', function () {
    });

    // Add item
    on(document.body, 'click', '.fxb-add-item', function (e, addBtn) {
        e.preventDefault();
        const tpl = FXB.templates.get('fxb-item');
        if (!tpl) return;
        const col = addBtn.closest('.fxb-col');
        if (!col) return;
        const row = col.closest('.fxb-row');
        const itemId = Date.now();
        const colIndex = col.getAttribute('data-col_index') || (col.dataset ? col.dataset.col_index : 'col_1');
        const rowId = row ? (row.getAttribute('data-id') || row.dataset.id) : '';
        const html = tpl({
            item_id: itemId,
            item_index: '1',
            item_state: 'open',
            item_type: 'text',
            row_id: rowId,
            col_index: colIndex,
            content: ''
        });

        const container = qs('.fxb-col-content', col);
        if (container) container.insertAdjacentHTML('beforeend', html);

        FXB.items.updateItemsIndex(col);
        reconcileAfterItemDomChange();

        const newIframe = qs('.fxb-item[data-item_id="' + itemId + '"] .fxb-item-iframe');
        if (newIframe) FXB.items.loadIframeContent(newIframe, FXB.items.getIframeCSS());
    });

    // Remove item
    on(document.body, 'click', '.fxb-remove-item', function (e, removeBtn) {
        e.preventDefault();
        const msg = removeBtn.getAttribute('data-confirm') || 'Delete item?';
        if (!confirm(msg)) return;
        const item = removeBtn.closest('.fxb-item');
        const col = removeBtn.closest('.fxb-col');
        if (item) item.remove();
        if (col) FXB.items.updateItemsIndex(col);
        reconcileAfterItemDomChange();
    });

    // Duplicate item (template-based, not DOM clone)
    on(document.body, 'click', '.fxb-duplicate-item', function (e, dupBtn) {
        e.preventDefault();
        const tpl = FXB.templates.get('fxb-item');
        if (!tpl) return;
        const original = dupBtn.closest('.fxb-item');
        const col = dupBtn.closest('.fxb-col');
        const row = dupBtn.closest('.fxb-row');
        if (!original || !col) return;

        const newId = Date.now();
        const colIndex = col.getAttribute('data-col_index') || (col.dataset ? col.dataset.col_index : 'col_1');
        const rowId = row ? (row.getAttribute('data-id') || row.dataset.id) : '';
        const textarea = qs('.fxb-item-textarea', original);
        const content = textarea ? textarea.value : '';

        const html = tpl({
            item_id: newId,
            item_index: '1',
            item_state: (original.getAttribute('data-item_state') || original.dataset.item_state || 'open'),
            item_type: (original.getAttribute('data-item_type') || original.dataset.item_type || 'text'),
            row_id: rowId,
            col_index: colIndex,
            content: content
        });

        original.insertAdjacentHTML('afterend', html);

        const newItem = qs('.fxb-item[data-item_id="' + newId + '"]');
        if (newItem) {
            const newTextarea = qs('.fxb-item-textarea', newItem);
            if (newTextarea) newTextarea.value = content;
            const iframe = qs('.fxb-item-iframe', newItem);
            if (iframe) FXB.items.loadIframeContent(iframe, FXB.items.getIframeCSS());
        }

        FXB.items.updateItemsIndex(col);
        reconcileAfterItemDomChange();
    });

    // Toggle item state
    on(document.body, 'click', '.fxb-toggle-item', function (e, toggleBtn) {
        e.preventDefault();
        const itemEl = toggleBtn.closest('.fxb-item');
        if (!itemEl) return;
        let state = itemEl.getAttribute('data-item_state') || itemEl.dataset.item_state || 'open';
        state = (state === 'open') ? 'close' : 'open';
        itemEl.dataset.item_state = state;
        itemEl.setAttribute('data-item_state', state);
        const stateInput = qs('input[data-item_field="item_state"]', itemEl);
        if (stateInput) stateInput.value = state;
    });

    // Open editor modal (click overlay)
    on(document.body, 'click', '.fxb-item-iframe-overlay', function (e, overlay) {
        e.preventDefault();
        const itemEl = overlay.closest('.fxb-item');
        const textareaEl = itemEl ? qs('.fxb-item-textarea', itemEl) : null;
        if (textareaEl) openEditorForTextarea(textareaEl);
    });

    // Close editor modal (Apply)
    on(document.body, 'click', '.fxb-editor .fxb-modal-close', function (e) {
        e.preventDefault();
        closeEditorAndApply();
    });
})();


