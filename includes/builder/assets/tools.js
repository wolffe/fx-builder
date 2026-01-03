; (function () {
    const FXB = window.FXB;
    if (!FXB || !FXB.dom || !FXB.modal) return;
    const qs = FXB.dom.qs;
    const qsa = FXB.dom.qsa;
    const on = FXB.dom.on;
    const show = function (el) { return FXB.dom.show(el, 'block'); };
    const hide = FXB.dom.hide;

    function openTools() {
        FXB.modal.open('.fxb-tools');
    }

    function setActiveTab(tabId) {
        // tabId: '#fxb-export-tab' or '#fxb-import-tab'
        const exportTab = qs('#fxb-export-tab');
        const importTab = qs('#fxb-import-tab');
        const exportPanel = qs('#fxb-export-panel');
        const importPanel = qs('#fxb-import-panel');

        const isExport = (tabId === '#fxb-export-tab');
        if (exportTab) exportTab.classList.toggle('wp-tab-active', isExport);
        if (importTab) importTab.classList.toggle('wp-tab-active', !isExport);
        if (exportPanel) exportPanel.style.display = isExport ? 'block' : 'none';
        if (importPanel) importPanel.style.display = isExport ? 'none' : 'block';
    }

    function resetToolsUI() {
        const exportTextarea = qs('#fxb-tools-export-textarea');
        const importTextarea = qs('#fxb-tools-import-textarea');
        const importAction = qs('#fxb-tools-import-action');

        setActiveTab('#fxb-export-tab');
        if (exportTextarea) { exportTextarea.value = ''; hide(exportTextarea); }
        if (importTextarea) importTextarea.value = '';
        if (importAction) importAction.classList.add('disabled');
    }

    function closeTools() {
        FXB.modal.close('.fxb-tools');
        resetToolsUI();
    }

    function parseBuilderFromForm(formEl) {
        const rows = {};
        const items = {};
        let row_ids = '';

        const fd = new FormData(formEl);
        fd.forEach(function (value, name) {
            if (name === '_fxb_row_ids') {
                row_ids = String(value || '');
                return;
            }
            const mRow = name.match(/^_fxb_rows\[(.+?)\]\[(.+?)\]$/);
            if (mRow) {
                const rowId = mRow[1];
                const field = mRow[2];
                rows[rowId] = rows[rowId] || {};
                rows[rowId][field] = String(value || '');
                return;
            }
            const mItem = name.match(/^_fxb_items\[(.+?)\]\[(.+?)\]$/);
            if (mItem) {
                const itemId = mItem[1];
                const field2 = mItem[2];
                items[itemId] = items[itemId] || {};
                items[itemId][field2] = String(value || '');
                return;
            }
        });

        // Rebuild export payload to match historical behavior:
        // - only include rows listed in row_ids
        // - only include items referenced in those rows' col_* lists
        const payloadRows = {};
        const payloadItems = {};
        const ids = FXB.util.asCSVArray(row_ids);

        ids.forEach(function (rid) {
            const r = rows[rid];
            if (!r) return;
            payloadRows[rid] = r;
            ['col_1', 'col_2', 'col_3', 'col_4', 'col_5'].forEach(function (colKey) {
                const list = (r[colKey] || '').split(',').map(function (s) { return (s || '').trim(); }).filter(Boolean);
                list.forEach(function (iid) {
                    if (items[iid]) payloadItems[iid] = items[iid];
                });
            });
        });

        return { row_ids: row_ids, rows: payloadRows, items: payloadItems };
    }

    async function ajaxImport(jsonData) {
        if (!window.fxb_tools || !fxb_tools.ajax_url) throw new Error('Missing fxb_tools.ajax_url');
        const params = new URLSearchParams();
        params.set('action', 'fxb_import_data');
        params.set('nonce', fxb_tools.ajax_nonce);
        params.set('data', jsonData);

        const res = await fetch(fxb_tools.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: params.toString()
        });

        const resp = await res.json();
        if (!resp || resp.success !== true || !resp.data) throw new Error('Import failed');
        return resp.data;
    }

    function applyImportedPayload(payload) {
        // Persist row order into the hidden field used on save.
        const hidden = qs('input[name="_fxb_row_ids"]');
        if (hidden) hidden.value = payload.row_ids || '';

        FXB.render(payload);
        FXB.reconcile();
    }

    on(document.body, 'click', '#fxb-nav-tools', function (e) {
        e.preventDefault();
        openTools();
    });
    on(document.body, 'click', '.fxb-tools .fxb-modal-close', function (e) {
        e.preventDefault();
        closeTools();
    });
    on(document.body, 'click', '.fxb-tools-nav-bar', function (e, tabLink) {
        e.preventDefault();
        const href = tabLink.getAttribute('href');
        if (href === '#fxb-export-panel') setActiveTab('#fxb-export-tab');
        else if (href === '#fxb-import-panel') setActiveTab('#fxb-import-tab');
    });
    on(document.body, 'click', '#fxb-tools-export-action', function (e) {
        e.preventDefault();
        const form = qs('#post');
        if (!form) return;
        const payload = parseBuilderFromForm(form);
        const json = JSON.stringify(payload);

        const exportTextarea = qs('#fxb-tools-export-textarea');
        if (exportTextarea) {
            exportTextarea.value = json;
            show(exportTextarea);
            exportTextarea.focus();
            exportTextarea.select();
            exportTextarea.addEventListener('focus', function () { exportTextarea.select(); }, { once: true });
        }
    });
    on(document.body, 'click', '#fxb-tools-import-action', async function (e, btn) {
        e.preventDefault();
        if (btn.classList.contains('disabled')) return;
        if (confirm(btn.getAttribute('data-confirm') || 'Are you sure?') !== true) return;

        const textarea = qs('#fxb-tools-import-textarea');
        const jsonData = textarea ? textarea.value : '';

        // Basic client-side validation
        try {
            const obj = JSON.parse(jsonData);
            if (!obj || typeof obj !== 'object' || typeof obj.row_ids === 'undefined' || typeof obj.rows === 'undefined' || typeof obj.items === 'undefined') {
                throw new Error('invalid');
            }
        } catch (err) {
            alert(btn.getAttribute('data-alert') || 'Your data is not valid.');
            return;
        }

        try {
            const payload2 = await ajaxImport(jsonData);
            applyImportedPayload(payload2);
            closeTools();
        } catch (err2) {
            alert(btn.getAttribute('data-alert') || 'Your data is not valid.');
        }
    });

    document.body.addEventListener('input', function (e) {
        const target = e.target;
        if (!(target instanceof Element)) return;
        if (target.matches('#fxb-tools-import-textarea')) {
            const btn = qs('#fxb-tools-import-action');
            if (!btn) return;
            if (target.value && target.value.trim()) btn.classList.remove('disabled');
            else btn.classList.add('disabled');
        }
    });
})();
