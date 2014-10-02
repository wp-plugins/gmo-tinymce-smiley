tinymce.create('tinymce.plugins.smiley', {
	getInfo: function() {
		return {
			"longname"  : "GMO TinyMCE Smiley",
			"author"    : "WP Shop byGMO",
			"authorurl" : "http://wpshop.com",
			"infourl"   : "http://wpshop.com",
			"version"   : tinymce.majorVersion + "." + tinymce.minorVersion
		}
	},
	init: function(editor) {
		function openDialog() {
			var _html = '';
			var win;
			
			var _height = window.innerHeight * 0.5;
			var _width = window.innerWidth * 0.6;
			_html += '<div class="mce-smiley" style="min-width: 400px; width: ' + _width + 'px; max-height: ' + _height + 'px; overflow: auto;">';
			for(key in editor.settings.smiley_emotion){
				_html += '<p title="' + editor.settings.smiley_emotion[key] + '">' + editor.settings.smiley_emotion[key] + '</p>';
			}
			_html += '</div>';
			
			win = editor.windowManager.open(
				{
					title: "Smiley",
					spacing: 10,
					padding: 10,
					items: [
						{
							type: 'container',
							html: _html,
							onclick: function(e) {
								var target = e.target;
								if (target.nodeName == 'P') {
									editor.execCommand('mceInsertContent', false, tinymce.trim(target.innerText || target.textContent));
									if (!e.ctrlKey) {
										win.close();
									}
								}
							}
						}
					],
					buttons: [
						{
							text: "Close",
							onclick: function() {
								win.close();
							}
						}
					]
				}
			);
		}
		
		editor.addButton( 'gmo-tinymce-smiley', {
			text: '(^_^)',
			tooltip: 'Smiley',
			onclick: openDialog
		});
		
		editor.addMenuItem('gmo-tinymce-smiley', {
			text: '(^_^)',
			tooltip: 'Smiley',
			onclick: openDialog,
			context: 'insert'
		});
	}
});

tinymce.PluginManager.add('gmo_tinymce_smiley', tinymce.plugins.smiley);
