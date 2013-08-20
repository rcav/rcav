/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	config.extraPlugins = 'sharedspace,font,justify,sourcedialog,colorbutton';
	config.sharedSpaces = {
		top : 'wysiwyg_buttons'
	};

	config.allowedContent = true;

	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'paragraph', groups: ['align', 'font'] },
		{ name: 'links' },
		{ name: 'font' },
		{ name: 'basicstyles' },
		{ name: 'document', groups: [ 'mode' ] },
		{ name: 'colors' },
		{ name: 'others' },
		{ name: 'insert' },
        { name: 'styles' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript,Table,HorizontalRule,RemoveFormat,Strike,Anchor,SpecialChar,BlockQuote,Smiley,PageBreak,NewPage,Flash,IFrame,Styles,JustifyBlock';

	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';
};
