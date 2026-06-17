(function () {
    tinymce.PluginManager.add('custom_line_height', function (editor) {
        editor.addButton('lineheight', {
            type: 'menubutton',
            text: 'Line Height',
            menu: [
                { text: '1.0', onclick: function () { applyLineHeight(editor, '1.0'); } },
                { text: '1.25', onclick: function () { applyLineHeight(editor, '1.25'); } },
                { text: '1.5', onclick: function () { applyLineHeight(editor, '1.5'); } },
                { text: '1.75', onclick: function () { applyLineHeight(editor, '1.75'); } },
                { text: '2.0', onclick: function () { applyLineHeight(editor, '2.0'); } },
                { text: '2.25', onclick: function () { applyLineHeight(editor, '2.25'); } },
                { text: '2.5', onclick: function () { applyLineHeight(editor, '2.5'); } },
            ]
        });

        function applyLineHeight(editor, value) {
            const blocks = editor.selection.getSelectedBlocks();

            if (!blocks.length) {
                const node = editor.selection.getNode();
                const block = editor.dom.getParent(node, editor.dom.isBlock);
                if (block) {
                    blocks.push(block);
                }
            }

            blocks.forEach(function (block) {
                editor.dom.setStyle(block, 'line-height', value);
            });

            if (blocks.length) {
                editor.nodeChanged();
            }
        }
    });
})();
