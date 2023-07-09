const basicToolbar = [
    ["Bold","Italic","Underline","Strike","Subscript","Superscript","-","RemoveFormat"],
    ["NumberedList","BulletedList","-","Outdent","Indent","-","Blockquote","-","JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"], 
    ["Link","Unlink"]
];

const contentsCss = ['/assets/bootstrap/bootstrap.min.css', '/assets/ckeditor/contents.css'];

function enableBasicEditor(textareaId) {
    CKEDITOR.replace(textareaId, {
        "language":"fr",
        "toolbar": basicToolbar,
        "htmlEncodeOutput":false,
        "allowedContent":true,
        "removePlugins":"exportpdf",
        "format_tags":"p;h3;h4;h5;h6;pre;address;div",
        "contentsCss": contentsCss
    });
}

function enableFullEditor(textareaId, formatTags = "p;h2;h3;h4;h5;h6;pre;address;div") {
    CKEDITOR.replace(textareaId, {
        "htmlEncodeOutput":false,
        "allowedContent":true,
        "format_tags": formatTags
    });
}