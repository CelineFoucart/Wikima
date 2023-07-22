/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'fr';
	config.contentsCss = ['/assets/bootstrap/bootstrap.min.css', '/assets/ckeditor/contents.css'];
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo', 'cleanup'  ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'ExportPdf,Scayt,About,Language,Flash,Smiley,Save,Iframe';
	config.removePlugins = 'exportpdf,scayt';
	config.format_tags = 'p;h3;h4;h5;h6;pre;address;div';

	config.templates_files = [
		'/assets/ckeditor/templates.js'
	];
};
