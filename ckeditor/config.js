/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
    config.font_names = '나눔고딕/Nanum Gothic;맑은고딕/Malgun gothic;굴림/Gulim;돋음/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana';
    config.filebrowserImageUploadUrl = "/EditorUpload";
    config.uiColor =  '#A41010'; //#14B8C4, #0AD4EB, #9ADB17, #E51669, #FAAC0F, #000000, #E48CF2 ... you want
    config.language = 'ko';
    config.enterMode = CKEDITOR.ENTER_BR //CKEDITOR.ENTER_DIV, CKEDITOR.ENTER_P,
    config.shiftEnterMode = CKEDITOR.ENTER_P;
    
	config.toolbar_Full = [
  		{ name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
  		{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
  		{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
  		{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
  		'/',
  		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
  		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
  		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
  		'/',
  		{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },  		
  		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
  		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
  		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
	];
 
	config.toolbar_Basic = [
  		{ name: 'document', items: [ 'Source', '-', 'Templates' ] },
  		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },  		
  		{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak' ] },
     	{ name: 'tools', items: [ 'Maximize' ] },
  		'/',
  		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },  		
  		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
  		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] }
	];		
	
    config.toolbar_Min =
    [
        { name: 'document', items: ['Source'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
        { name: 'colors', items: ['TextColor', 'BGColor'] },
        { name: 'tools', items: ['Maximize'] }
	];	
};
