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

export function enableBasicEditor(textareaSelector) {
    document.querySelector(`#${textareaSelector}`).removeAttribute('required');

    tinymce.init({
        selector: '#' + textareaSelector,
        language: LANGUAGE,
        skin: SKIN,
        branding: false,
        promotion: false,
        link_context_toolbar: true,
        insertdatetime_formats: DATETIME_FORMAT,
        quickbars_insert_toolbar: QUICK_INSERT_TOOLBAR,
        contextmenu: CONTEXTMENU,
        quickbars_selection_toolbar: QUICK_SELECTION_TOOLBAR,
        toolbar: 'undo redo |' +
            'fontsizeinput bold italic underline align bullist numlist blockquote link quicktable emoticons | fullscreen',
        menu: {
            file: { title: 'File', items: 'code wordcount | visualaid visualchars visualblocks | preview fullscreen | newdocument print ' },
            edit: { title: 'Edit', items: 'undo redo | cut copy paste removeformat | selectall | searchreplace' },
            insert: { title: 'Insert', items: 'image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime' },
            format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | blocks fontfamily fontsize align lineheight | forecolor backcolor | removeformat' },
            table: { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
        },
        menubar: 'file format edit insert table',
        plugins: [
            'advlist', 'anchor', 'autolink', 'charmap', 'fullscreen', 'image', 'importcss', 'link', 'lists', 'media', 'nonbreaking',
            'preview', 'quickbars', 'searchreplace', 'table', 'visualblocks', 'visualchars', 'wordcount', 'emoticons', 'insertdatetime'
        ],
        convert_urls: false,
    });
}
