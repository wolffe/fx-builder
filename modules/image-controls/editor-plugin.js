(function () {
	'use strict';

	var getImage = function (editor) {
		var node = editor.selection.getNode();

		return node && node.nodeName === 'IMG'
			? node
			: editor.dom.getParent(node, 'img');
	};

	var getStyle = function (editor, image, property) {
		return editor.dom.getStyle(image, property) || '';
	};

	var getInitialData = function (editor, image) {
		return {
			borderRadius: getStyle(editor, image, 'border-radius'),
			padding: getStyle(editor, image, 'padding'),
			margin: getStyle(editor, image, 'margin'),
			loading: image.getAttribute('loading') || '',
			fetchpriority: image.getAttribute('fetchpriority') || ''
		};
	};

	var setAttribute = function (image, name, value) {
		if (value) {
			image.setAttribute(name, value);
			return;
		}

		image.removeAttribute(name);
	};

	var setStyles = function (editor, image, data) {
		editor.dom.setStyle(image, 'border-radius', data.borderRadius || '');
		editor.dom.setStyle(image, 'padding', data.padding || '');
		editor.dom.setStyle(image, 'margin', data.margin || '');
	};

	var applyData = function (editor, image, data) {
		editor.undoManager.transact(function () {
			setStyles(editor, image, data);
			setAttribute(image, 'loading', data.loading);
			setAttribute(image, 'fetchpriority', data.fetchpriority);
		});

		editor.nodeChanged();
		editor.focus();
	};

	var openDialog = function (editor) {
		var image = getImage(editor);
		var data;

		if (!image) {
			editor.windowManager.alert('Select an image first.');
			return;
		}

		data = getInitialData(editor, image);

		editor.windowManager.open({
			title: 'Image controls',
			body: [
				{
					type: 'textbox',
					name: 'borderRadius',
					label: 'Border radius',
					value: data.borderRadius,
					placeholder: '8px'
				},
				{
					type: 'textbox',
					name: 'padding',
					label: 'Padding',
					value: data.padding,
					placeholder: '0.5rem'
				},
				{
					type: 'textbox',
					name: 'margin',
					label: 'Margin',
					value: data.margin,
					placeholder: '1rem 0'
				},
				{
					type: 'listbox',
					name: 'loading',
					label: 'Loading',
					value: data.loading,
					values: [
						{ text: 'Default', value: '' },
						{ text: 'Lazy', value: 'lazy' },
						{ text: 'Eager', value: 'eager' }
					]
				},
				{
					type: 'listbox',
					name: 'fetchpriority',
					label: 'Fetch priority',
					value: data.fetchpriority,
					values: [
						{ text: 'Default', value: '' },
						{ text: 'High', value: 'high' },
						{ text: 'Low', value: 'low' },
						{ text: 'Auto', value: 'auto' }
					]
				}
			],
			onsubmit: function (event) {
				applyData(editor, image, event.data);
			}
		});
	};

	tinymce.PluginManager.add('fxb_image_controls', function (editor) {
		editor.addButton('fxb_image_controls', {
			tooltip: 'Image controls',
			icon: 'image',
			onclick: function () {
				openDialog(editor);
			}
		});

		editor.addMenuItem('fxb_image_controls', {
			text: 'Image controls',
			context: 'insert',
			onclick: function () {
				openDialog(editor);
			}
		});
	});
}());
