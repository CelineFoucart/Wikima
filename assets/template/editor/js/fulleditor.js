import tinymce from "tinymce";
import 'tinymce/models/dom/model'
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/autosave';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/codesample';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/image';
import 'tinymce/plugins/importcss';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/media';
import 'tinymce/plugins/nonbreaking';
import 'tinymce/plugins/pagebreak';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/quickbars';
import 'tinymce/plugins/save';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/table';
import 'tinymce/plugins/template';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/emoticons';
import 'tinymce/plugins/emoticons/js/emojis';
import './lang/fr_FR';

import { LANGUAGE, SKIN, DATETIME_FORMAT, QUICK_INSERT_TOOLBAR, CONTEXTMENU, QUICK_SELECTION_TOOLBAR } from "./editor";
import { modal, enableWikiLinksPlugin } from "./wikiLinks";

export function enableFullEditor(textareaId, headings = "Titre 1=h2; Titre 2=h3; Titre 3=h4; Titre 4=h5; Titre 5=h6;", withScript = false) {
    document.querySelector(`#${textareaId}`).removeAttribute('required');
    const block_formats = 'Paragraph=p;' + headings + 'Division=div; Code=code;';

    const configs = {
        selector: '#' + textareaId,
        language: LANGUAGE,
        skin: SKIN,
        promotion: false,
        branding: false,
        link_context_toolbar: true,
        block_formats: block_formats,
        insertdatetime_formats: DATETIME_FORMAT,
        quickbars_insert_toolbar: QUICK_INSERT_TOOLBAR,
        contextmenu: CONTEXTMENU,
        quickbars_selection_toolbar: QUICK_SELECTION_TOOLBAR,
        element_format : "xhtml",
        toolbar: 'undo redo  | blocks | fontsizeinput |' + 
            'bold italic underline align | ' +
            'bullist numlist outdent indent | blockquote link image quicktable template searchWikiButton charmap |'  +
            'searchreplace preview fullscreen ' + (withScript ? 'code' : ''),
        menu: {
            file: { title: 'File', items: 'code wordcount | visualaid visualchars visualblocks | selectall preview fullscreen | newdocument print ' },
            format: { title: 'Format', items: 'bold italic underline strikethrough subscript superscript codeformat | blocks fontfamily fontsize align lineheight | forecolor backcolor | removeformat' },
            insert: {title: 'Insert', items: 'image link media template searchWikiButton inserttable | charmap hr | pagebreak nonbreaking anchor emoticons | insertdatetime'},
        
        },
        menubar: 'file format edit insert table',
        plugins: [
            'advlist', 'anchor', 'autolink', 'charmap', 'code', 'fullscreen', 'image', 'importcss', 'insertdatetime', 'link', 'lists',
            'media', 'nonbreaking', 'pagebreak', 'preview', 'quickbars', 'searchreplace', 'table', 'template', 'visualblocks',
            'visualchars', 'wordcount', 'emoticons'
        ],
        templates: '/api/template',
        setup: function (editor) {
            editor.ui.registry.addButton('searchWikiButton', {
                icon: 'browse',
                tooltip: "Rechercher dans l'encyclopédie",
                onAction: function (_) {
                    enableWikiLinksPlugin(textareaId, editor);
                    modal.show();
                }
            });
            editor.ui.registry.addMenuItem('searchWikiButton', {
                icon: 'browse',
                text: "Rechercher un contenu...",
                onAction: function (_) {
                    enableWikiLinksPlugin(textareaId, editor);
                    modal.show();
                }
            });
        },
        a11y_advanced_options: true,
        image_title: true,
        file_picker_types: 'image',
        image_caption: true,
        image_class_list: [
            { title: 'Sans formatage', value: 'img-fluid' },
            { title: 'Miniature', value: 'img-thumbnail img-fluid' },
            { title: 'Image avec bord arrondi', valuee: 'rounded img-fluid' }
        ],
        convert_urls: false,
    };

    if (withScript) {
        configs.extended_valid_elements = 'script[src|async|defer|type|charset]';
    }
    
    tinymce.init(configs);
}