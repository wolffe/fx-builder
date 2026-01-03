; (function () {
    /**
     * FXB Core (no dependencies)
     * - Provides a single namespace (window.FXB)
     * - Small shared utilities used across modules
     * - Lightweight event bus via CustomEvent
     */

    const FXB = window.FXB || {};
    window.FXB = FXB;

    FXB.dom = FXB.dom || {};
    FXB.dom.qs = function (sel, root) { return (root || document).querySelector(sel); };
    FXB.dom.qsa = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
    FXB.dom.hide = FXB.dom.hide || function (el) { if (el) el.style.display = 'none'; };
    FXB.dom.show = FXB.dom.show || function (el, display) {
        if (!el) return;
        el.style.display = display || 'block';
    };

    /**
     * Delegated event listener helper.
     * Calls handler(event, matchedElement) when event.target is within selector.
     */
    FXB.dom.on = function (root, eventName, selector, handler, options) {
        if (!root || !eventName || !selector || typeof handler !== 'function') return function () { };
        const listener = function (e) {
            const t = e && e.target;
            if (!(t instanceof Element)) return;
            const match = t.closest(selector);
            if (!match) return;
            handler(e, match);
        };
        root.addEventListener(eventName, listener, options);
        return function () { root.removeEventListener(eventName, listener, options); };
    };

    FXB.util = FXB.util || {};
    FXB.util.asCSVArray = function (csv) {
        if (!csv || typeof csv !== 'string') return [];
        return csv.split(',').map(function (s) { return (s || '').trim(); }).filter(Boolean);
    };
    FXB.util.safeJSONParse = function (text) {
        try { return JSON.parse(text); } catch (e) { return null; }
    };

    FXB.templates = FXB.templates || {};
    FXB.templates.get = function (name) {
        if (!name) return null;
        if (typeof wp === 'undefined' || typeof wp.template !== 'function') return null;
        return wp.template(name);
    };

    FXB.rows = FXB.rows || {};

    function buildColHTML(rowId, rowData, itemsById, itemTemplate) {
        const out = {};
        if (!rowData) return out;
        const cols = ['col_1', 'col_2', 'col_3', 'col_4', 'col_5'];
        cols.forEach(function (colKey) {
            const ids = FXB.util.asCSVArray(rowData[colKey] || '');
            out[colKey] = ids.map(function (itemId) {
                const item = itemsById && itemsById[itemId] ? itemsById[itemId] : null;
                if (!item) return '';
                // Ensure context fields are correct for this placement.
                item.row_id = rowId;
                item.col_index = colKey;
                return itemTemplate(item);
            }).join('');
        });
        return out;
    }

    function hideWithRestore(el) {
        if (!el) return;
        let current = el.style.display;
        if (!current || current === 'none') {
            current = window.getComputedStyle(el).display;
        }
        if (current && current !== 'none') el.setAttribute('data-fxb-display', current);
        el.style.display = 'none';
    }

    function showWithRestore(el) {
        if (!el) return;
        const prev = el.getAttribute('data-fxb-display');
        if (prev) {
            el.style.display = prev;
            return;
        }
        // Default for add-row controls.
        if (el.classList && el.classList.contains('fxb-add-row')) {
            el.style.display = 'grid';
            return;
        }
        el.style.display = '';
    }

    // Keep these helpers internal; no longer exported on FXB.dom.

    FXB.modal = FXB.modal || {};
    FXB.modal._stack = FXB.modal._stack || [];
    FXB.modal._lastFocus = FXB.modal._lastFocus || new Map();
    FXB.modal._overlay = FXB.modal._overlay || null;

    function getOverlay() {
        if (FXB.modal._overlay && FXB.modal._overlay.isConnected) return FXB.modal._overlay;
        FXB.modal._overlay = FXB.dom.qs('.fxb-modal-overlay');
        return FXB.modal._overlay;
    }

    function resolveModal(modalOrSelector) {
        if (!modalOrSelector) return null;
        if (typeof modalOrSelector === 'string') return FXB.dom.qs(modalOrSelector);
        if (modalOrSelector instanceof Element) return modalOrSelector;
        return null;
    }

    function syncOverlay() {
        const overlay = getOverlay();
        if (!overlay) return;
        FXB.dom.show(overlay, (FXB.modal._stack.length > 0) ? 'block' : 'none');
    }

    FXB.modal.open = function (modalOrSelector) {
        const modal = resolveModal(modalOrSelector);
        if (!modal) return;

        FXB.modal._lastFocus.set(modal, document.activeElement);

        FXB.dom.show(modal, 'block');
        if (FXB.modal._stack.indexOf(modal) === -1) FXB.modal._stack.push(modal);
        syncOverlay();
    };

    FXB.modal.close = function (modalOrSelector) {
        const modal = resolveModal(modalOrSelector);
        if (!modal) return;

        FXB.dom.hide(modal);
        const idx = FXB.modal._stack.indexOf(modal);
        if (idx !== -1) FXB.modal._stack.splice(idx, 1);
        syncOverlay();

        const prev = FXB.modal._lastFocus.get(modal);
        if (prev && typeof prev.focus === 'function') {
            prev.focus();
        }
        FXB.modal._lastFocus.delete(modal);
    };

    FXB.modal.closeTop = function () {
        const top = FXB.modal._stack.length ? FXB.modal._stack[FXB.modal._stack.length - 1] : null;
        if (top) FXB.modal.close(top);
    };

    if (!FXB.modal._escBound) {
        FXB.modal._escBound = true;
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                if (FXB.modal._stack.length) {
                    FXB.modal.closeTop();
                }
            }
        });
    }

    /**
     * Update row indexes and the `_fxb_row_ids` hidden input from the current DOM order.
     */
    FXB.rows.updateRowsIndex = function () {
        const qs = FXB.dom.qs;
        const qsa = FXB.dom.qsa;

        const rowIds = [];
        qsa('#fxb > .fxb-row').forEach(function (rowEl, i) {
            const num = i + 1;
            const rowId = rowEl.getAttribute('data-id') || (rowEl.dataset ? rowEl.dataset.id : '');
            if (rowId) rowIds.push(rowId);

            if (rowEl.dataset) rowEl.dataset.index = String(num);
            rowEl.setAttribute('data-index', String(num));

            const indexBadge = qs('.fxb_row_index', rowEl);
            if (indexBadge) indexBadge.setAttribute('data-row-index', String(num));

            const indexInput = qs('input[data-row_field="index"]', rowEl);
            if (indexInput) {
                indexInput.value = String(num);
            }
        });

        const hidden = qs('input[name="_fxb_row_ids"]');
        if (hidden) {
            hidden.value = rowIds.join();
        }
    };

    /**
     * Show/hide the bottom add-row control based on whether any rows exist.
     */
    FXB.rows.updateBottomAddRowVisibility = function () {
        const qs = FXB.dom.qs;
        const hasRows = !!qs('#fxb .fxb-row');
        const bottom = qs('.fxb-add-row[data-add_row_method="append"]');
        if (!bottom) return;
        if (hasRows) showWithRestore(bottom);
        else hideWithRestore(bottom);
    };

    // Core editor helpers.

    FXB.editor = FXB.editor || {};

    /**
     * Similar to wpautop().
     */
    FXB.editor.autop = function (text) {
        let preserve_linebreaks = false,
            preserve_br = false,
            blocklist = 'table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre' +
                '|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section' +
                '|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary';

        text = (text || '').replace(/\r\n|\r/g, '\n');
        if (text.indexOf('\n') === -1) return text;

        if (text.indexOf('<object') !== -1) {
            text = text.replace(/<object[\s\S]+?<\/object>/g, function (a) {
                return a.replace(/\n+/g, '');
            });
        }

        text = text.replace(/<[^<>]+>/g, function (a) {
            return a.replace(/[\n\t ]+/g, ' ');
        });

        if (text.indexOf('<pre') !== -1 || text.indexOf('<script') !== -1) {
            preserve_linebreaks = true;
            text = text.replace(/<(pre|script)[^>]*>[\s\S]*?<\/\1>/g, function (a) {
                return a.replace(/\n/g, '<wp-line-break>');
            });
        }

        if (text.indexOf('[caption') !== -1) {
            preserve_br = true;
            text = text.replace(/\[caption[\s\S]+?\[\/caption\]/g, function (a) {
                a = a.replace(/<br([^>]*)>/g, '<wp-temp-br$1>');
                a = a.replace(/<[^<>]+>/g, function (b) {
                    return b.replace(/[\n\t ]+/, ' ');
                });
                return a.replace(/\s*\n\s*/g, '<wp-temp-br />');
            });
        }

        text = text + '\n\n';
        text = text.replace(/<br \/>\s*<br \/>/gi, '\n\n');
        text = text.replace(new RegExp('(<(?:' + blocklist + ')(?: [^>]*)?>)', 'gi'), '\n$1');
        text = text.replace(new RegExp('(</(?:' + blocklist + ')>)', 'gi'), '$1\n\n');
        text = text.replace(/<hr( [^>]*)?>/gi, '<hr$1>\n\n');
        text = text.replace(/\s*<option/gi, '<option');
        text = text.replace(/<\/option>\s*/gi, '</option>');
        text = text.replace(/\n\s*\n+/g, '\n\n');
        text = text.replace(/([\s\S]+?)\n\n/g, '<p>$1</p>\n');
        text = text.replace(/<p>\s*?<\/p>/gi, '');
        text = text.replace(new RegExp('<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi'), '$1');
        text = text.replace(/<p>(<li.+?)<\/p>/gi, '$1');
        text = text.replace(/<p>\s*<blockquote([^>]*)>/gi, '<blockquote$1><p>');
        text = text.replace(/<\/blockquote>\s*<\/p>/gi, '</p></blockquote>');
        text = text.replace(new RegExp('<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)', 'gi'), '$1');
        text = text.replace(new RegExp('(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi'), '$1');
        text = text.replace(/(<br[^>]*>)\s*\n/gi, '$1');
        text = text.replace(/\s*\n/g, '<br />\n');
        text = text.replace(new RegExp('(</?(?:' + blocklist + ')[^>]*>)\\s*<br />', 'gi'), '$1');
        text = text.replace(/<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)/gi, '$1');
        text = text.replace(/(?:<p>|<br ?\/?>)*\s*\[caption([^\[]+)\[\/caption\]\s*(?:<\/p>|<br ?\/?>)*/gi, '[caption$1[/caption]');

        text = text.replace(/(<(?:div|th|td|form|fieldset|dd)[^>]*>)(.*?)<\/p>/g, function (a, b, c) {
            if (c.match(/<p( [^>]*)?>/)) return a;
            return b + '<p>' + c + '</p>';
        });

        if (preserve_linebreaks) text = text.replace(/<wp-line-break>/g, '\n');
        if (preserve_br) text = text.replace(/<wp-temp-br([^>]*)>/g, '<br$1>');
        return text;
    };

    /**
     * Force switch to Visual editor for a TinyMCE instance.
     */
    FXB.editor.switchEditor = function (id) {
        if (typeof tinymce === 'undefined') return;
        id = id || 'content';

        const editor = tinymce.get(id);
        const wrap = document.getElementById('wp-' + id + '-wrap');
        const textarea = document.getElementById(id);

        if (editor && !editor.isHidden()) return false;

        if (typeof window.QTags !== 'undefined') {
            window.QTags.closeAllTags(id);
        }
        if (editor) editor.show();
        else if (window.tinyMCEPreInit && window.tinyMCEPreInit.mceInit && window.tinyMCEPreInit.mceInit[id]) {
            tinymce.init(window.tinyMCEPreInit.mceInit[id]);
        }

        if (wrap) {
            wrap.classList.remove('html-active');
            wrap.classList.add('tmce-active');
        }
        if (textarea) textarea.setAttribute('aria-hidden', 'true');
        if (typeof window.setUserSetting === 'function') window.setUserSetting('editor', 'tinymce');
    };

    FXB.editor.hasTinyMCE = function () {
        return (typeof tinymce !== 'undefined' && typeof tinymce.get === 'function');
    };

    FXB.editor.get = function (id) {
        if (!FXB.editor.hasTinyMCE()) return null;
        return tinymce.get(id || 'content');
    };

    FXB.editor.setModalContent = function (editorId, rawHtml) {
        editorId = editorId || 'fxb_editor';
        FXB.editor.switchEditor(editorId);
        const ed = FXB.editor.get(editorId);
        if (ed) {
            ed.show();
            ed.setContent(FXB.editor.autop(rawHtml || ''));
            ed.undoManager && ed.undoManager.clear && ed.undoManager.clear();
            return;
        }
        const plain = document.getElementById(editorId);
        if (plain) plain.value = String(rawHtml || '');
    };

    FXB.editor.applyModalToTextarea = function (editorId, targetTextarea) {
        editorId = editorId || 'fxb_editor';
        FXB.editor.switchEditor(editorId);
        const ed = FXB.editor.get(editorId);
        if (ed) {
            ed.save();
            ed.hide();
        }
        const editorTextarea = document.getElementById(editorId);
        if (targetTextarea && editorTextarea) targetTextarea.value = editorTextarea.value || '';
    };

    FXB.items = FXB.items || {};

    FXB.items.getIframeCSS = function () {
        if (typeof tinymce === 'undefined' || typeof tinyMCEPreInit === 'undefined') return '';
        const init = tinyMCEPreInit.mceInit && tinyMCEPreInit.mceInit.fxb_editor;
        if (!init || !init.content_css) return '';
        return String(init.content_css)
            .split(',')
            .map(function (item) { return '<link type="text/css" rel="stylesheet" href="' + item + '" />'; })
            .join('');
    };

    /**
     * Render preview for one item iframe. Uses srcdoc when possible.
     */
    FXB.items.loadIframeContent = function (iframeEl, headHtml) {
        if (!iframeEl) return;
        const qs = FXB.dom.qs;

        let editor_body_class = '';
        if (typeof tinymce !== 'undefined' && typeof tinyMCEPreInit !== 'undefined' && tinyMCEPreInit.mceInit && tinyMCEPreInit.mceInit.fxb_editor) {
            editor_body_class = tinyMCEPreInit.mceInit.fxb_editor.body_class || '';
        } else {
            headHtml = "<style>.wp-editor{font-family: Consolas,Monaco,monospace;font-size: 13px;line-height: 150%;}</style>";
        }

        const rawTextarea = iframeEl.parentElement ? qs('.fxb-item-textarea', iframeEl.parentElement) : null;
        const raw = rawTextarea ? rawTextarea.value : '';
        const content = FXB.editor.autop(raw);

        const html = '<!doctype html><html><head>' + (headHtml || '') + '</head><body id="tinymce" class="wp-editor ' + editor_body_class + '">' + content + '</body></html>';
        iframeEl.srcdoc = html;
    };

    /**
     * Update item indexes and per-column hidden lists from current DOM order.
     */
    FXB.items.updateItemsIndex = function (colEl) {
        const qs = FXB.dom.qs;
        const qsa = FXB.dom.qsa;
        if (!colEl) return;

        const rowEl = colEl.closest('.fxb-row');
        const rowId = rowEl ? (rowEl.getAttribute('data-id') || (rowEl.dataset ? rowEl.dataset.id : '')) : '';
        const colIndex = colEl.getAttribute('data-col_index') || (colEl.dataset ? colEl.dataset.col_index : '');
        const itemsInput = qs('input[data-row_field="' + colIndex + '"]', colEl);

        const ids = [];
        qsa('.fxb-col-content > .fxb-item', colEl).forEach(function (itemEl, i) {
            const num = i + 1;
            const itemId = itemEl.getAttribute('data-item_id') || (itemEl.dataset ? itemEl.dataset.item_id : '');
            if (itemId) ids.push(itemId);

            if (itemEl.dataset) itemEl.dataset.item_index = String(num);
            itemEl.setAttribute('data-item_index', String(num));

            const badge = qs('.fxb_item_index', itemEl);
            if (badge) {
                badge.setAttribute('data-item-index', String(num));
                if (badge.dataset) badge.dataset.itemIndex = String(num);
            }

            const idxInput = qs('input[data-item_field="item_index"]', itemEl);
            if (idxInput) {
                idxInput.value = String(num);
            }

            if (itemEl.dataset) itemEl.dataset.row_id = rowId;
            const rowIdInput = qs('input[data-item_field="row_id"]', itemEl);
            if (rowIdInput) {
                rowIdInput.value = rowId;
            }

            if (itemEl.dataset) itemEl.dataset.col_index = colIndex;
            const colInput = qs('input[data-item_field="col_index"]', itemEl);
            if (colInput) {
                colInput.value = colIndex;
            }
        });

        if (itemsInput) {
            itemsInput.value = ids.join();
        }
    };

    /**
     * Init SortableJS for item containers (idempotent).
     */
    FXB.items.sortInit = function () {
        if (typeof Sortable === 'undefined') return;
        FXB.dom.qsa('.fxb-col-content').forEach(function (colContent) {
            if (colContent._fxbSortable) return;
            colContent._fxbSortable = new Sortable(colContent, {
                handle: '.fxb-item-handle',
                animation: 150,
                group: 'shared',
                onEnd: function (evt) {
                    const col = evt.from ? evt.from.closest('.fxb-col') : null;
                    if (col) FXB.items.updateItemsIndex(col);
                    const toCol = evt.to ? evt.to.closest('.fxb-col') : null;
                    if (toCol && toCol !== col) FXB.items.updateItemsIndex(toCol);
                }
            });
        });
    };

    /**
     * Refresh all iframe previews (delegates to the existing item renderer).
     */
    FXB.items.loadAllIframes = function () {
        const css = FXB.items.getIframeCSS();
        FXB.dom.qsa('.fxb-item-iframe').forEach(function (iframe) {
            FXB.items.loadIframeContent(iframe, css);
        });
    };

    // Events.

    FXB.events = FXB.events || {};
    FXB.events.emit = function (name, detail) {
        window.dispatchEvent(new CustomEvent(name, { detail: detail }));
    };

    /**
     * Reconcile UI state after DOM changes (render/import/drag/drop).
     */
    FXB.reconcile = function (opts) {
        opts = opts || {};
        const doRows = (opts.rows !== false);
        const doBottomAdd = (opts.bottomAdd !== false);
        const doSortItems = (opts.sortItems !== false);
        const doIframes = (opts.iframes !== false);

        function run() {
            if (doRows && FXB.rows && typeof FXB.rows.updateRowsIndex === 'function') {
                FXB.rows.updateRowsIndex();
            }

            if (doBottomAdd && FXB.rows && typeof FXB.rows.updateBottomAddRowVisibility === 'function') {
                FXB.rows.updateBottomAddRowVisibility();
            }
            if (doSortItems && FXB.items && typeof FXB.items.sortInit === 'function') {
                FXB.items.sortInit();
            }
            if (doIframes && FXB.items && typeof FXB.items.loadAllIframes === 'function') {
                FXB.items.loadAllIframes();
            }
        }

        run();

        FXB.events.emit('fxb:reconcile', {});
    };

    /**
     * Render builder UI from a payload:
     * { row_ids: string, rows: object, items: object }
     *
     * Uses wp.template('fxb-row') / wp.template('fxb-item') and batches DOM writes.
     * Keeps side-effects (sortable init, previews, indexes) delegated to existing modules for now.
     */
    FXB.render = function (payload) {
        if (!payload || typeof payload !== 'object') return;

        const fxb = document.getElementById('fxb');
        if (!fxb) return;

        FXB.events.emit('fxb:render:before', { payload: payload });

        // Clear current UI.
        fxb.innerHTML = '';

        const rowTemplate = FXB.templates.get('fxb-row');
        const itemTemplate = FXB.templates.get('fxb-item');
        if (!rowTemplate || !itemTemplate) return;

        // Rows: build a fragment and append once.
        const frag = document.createDocumentFragment();
        const rowIds = FXB.util.asCSVArray(payload.row_ids || '');
        rowIds.forEach(function (rowId) {
            if (!payload.rows || !payload.rows[rowId]) return;
            const rowData = payload.rows[rowId];
            rowData.col_html = buildColHTML(rowId, rowData, payload.items, itemTemplate);
            const html = rowTemplate(rowData);
            const tmp = document.createElement('div');
            tmp.innerHTML = html;
            while (tmp.firstChild) frag.appendChild(tmp.firstChild);
        });
        fxb.appendChild(frag);

        // Post-render reconcile.
        if (typeof FXB.reconcile === 'function') {
            FXB.reconcile();
        }

        // Existing modules still own reconcile for now.
        FXB.events.emit('fxb:render:after', { payload: payload });
    };

    // Bootstrap loader (replaces assets/bootstrap.js).
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.FXB_BOOTSTRAP) return;
        FXB.render(window.FXB_BOOTSTRAP);
    });
})();


