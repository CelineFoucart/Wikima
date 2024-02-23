const CONTENT_CSS = ['/assets/plugins/bootstrap/bootstrap.min.css', '/assets/editor/editor.css'];
const SKIN = 'tinymce-5';
const LANGUAGE = "fr_FR";
const HOST = location.origin;
const CONTEXTMENU = "alignleft aligncenter alignright alignjustify | bold italic underline | forecolor backcolor fontsizes | image link table | selectall cut copy paste removeformat";
const QUICK_INSERT_TOOLBAR = 'quicktable quicklink | hr pagebreak | bullist numlist';
const QUICK_SELECTION_TOOLBAR = 'bold italic underline bullist quicklink blockquote quicktable';

function enableBasicEditor(textareaId) {
    document.querySelector(`#${textareaId}`).removeAttribute('required');

    tinyMCE.init({
        selector: `#${textareaId}`,
        language: LANGUAGE,
        skin: SKIN,
        branding: false,
        promotion: false,
        content_css: CONTENT_CSS,
        link_context_toolbar: true,
        quickbars_insert_toolbar: QUICK_INSERT_TOOLBAR,
        contextmenu: CONTEXTMENU,
        quickbars_selection_toolbar: QUICK_SELECTION_TOOLBAR,
        toolbar: 'undo redo |' +
            'fontsizeinput bold italic underline align bullist numlist blockquote link quicktable emoticons | fullscreen help',
        menu: {
            file: { title: 'File', items: 'code wordcount | visualaid visualchars visualblocks | preview fullscreen | newdocument print ' },
            edit: { title: 'Edit', items: 'undo redo | cut copy paste removeformat | selectall | searchreplace' },
            insert: { title: 'Insert', items: 'image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime' },
            format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | blocks fontfamily fontsize align lineheight | forecolor backcolor | removeformat' },
            table: { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
        },
        menubar: 'file format edit insert table',
        plugins: [
            'advlist', 'anchor', 'autolink', 'charmap', 'fullscreen', 'help', 'image', 'importcss', 'link', 'lists', 'media', 'nonbreaking',
            'preview', 'quickbars', 'searchreplace', 'table', 'visualblocks', 'visualchars', 'wordcount', 'emoticons', 'insertdatetime'
        ],
        convert_urls: false,
    });
}

let modalElement = null;
let modal  = null;
function enableWikiLinksPlugin(textareaId, editor) {
    if (modal !== null && modalElement !== null) {
        return;
    }

    const tablePersonId = textareaId + 'tablePerson';
    const tablePlaceId = textareaId + 'tablePlace';
    const tableArticleId = textareaId + 'tableArticle';

    modalElement = document.createElement('div');
    modalElement.classList = 'modal';
    modalElement.tabindex = "-1";
    modalElement.innerHTML = `
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0 p-3 pb-0">
                    <h4 class="modal-title fw-normal h5">Rechercher un élément</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                        <li class="nav-item ms-2" role="presentation">
                            <button class="nav-link active" id="${textareaId}-article-tab" data-bs-toggle="tab" data-bs-target="#${textareaId}-article-tab-pane" type="button" role="tab" aria-controls="article-tab-pane" aria-selected="true">Articles</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="${textareaId}-person-tab" data-bs-toggle="tab" data-bs-target="#${textareaId}-person-tab-pane" type="button" role="tab" aria-controls="person-tab-pane" aria-selected="false">Personnages</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="${textareaId}-place-tab" data-bs-toggle="tab" data-bs-target="#${textareaId}-place-tab-pane" type="button" role="tab" aria-controls="place-tab-pane" aria-selected="false">Lieux</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3" id="myTabContent" style="min-height: 150px">
                        <div class="tab-pane fade show active" id="${textareaId}-article-tab-pane" role="tabpanel" aria-labelledby="${textareaId}-article-tab" tabindex="0">
                            <table class="table table-striped" id="${tableArticleId}">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th data-orderable="false">Choisir</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="${textareaId}-person-tab-pane" role="tabpanel" aria-labelledby="${textareaId}-person-tab" tabindex="0">
                            <table class="table table-striped" id="${tablePersonId}">
                                <thead>
                                    <tr>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th data-orderable="false">Choisir</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="${textareaId}-place-tab-pane" role="tabpanel" aria-labelledby="${textareaId}-place-tab" tabindex="0">
                            <table class="table table-striped" id="${tablePlaceId}">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th data-orderable="false">Choisir</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modalElement);
    modal = new bootstrap.Modal(modalElement, {
        keyboard: false
    });

    const $tablePerson = $('#' + tablePersonId).DataTable({
        language: {
            url: '/assets/plugins/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging: true,
        info: false,
        autoWidth: false,
        ajax: '/api/admin/persons',
        columns: [
            { data: "firstname", name: "p.firstname" },
            { data: "lastname", name: "p.lastname" },
            {
                data: "actions",
                name: null,
                render: function (data, type, row) {
                    return `
                        <button 
                            class="btn btn-primary btn-sm person-btn-action" 
                            data-slug="${row.slug}"
                            data-title="${row.firstname} ${row.lastname}"
                            title="choisir">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        createdRow: function (row, data, dataIndex) {
            $(row).find('td:eq(2)').addClass('text-end').css("width", "10%");
        }
    });
    $tablePerson.on('draw', function () {
        const btns = document.querySelectorAll('.person-btn-action');
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.hide();
                const text = editor.selection.getContent({ format: 'html' });
                let title = btn.dataset.title;
                if (text.length > 0) {
                    title = text;
                }
                editor.insertContent(`<a href="${HOST}/persons/${btn.dataset.slug}">${title}</a>`);
            })
        });
    });

    const $tablePlace = $('#' + tablePlaceId).DataTable({
        language: {
            url: '/assets/plugins/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging: true,
        info: false,
        autoWidth: false,
        ajax: '/api/admin/place',
        columns: [
            { data: "title", name: "p.title" },
            {
                data: "actions",
                name: null,
                render: function (data, type, row) {
                    return `
                        <button 
                            class="btn btn-primary btn-sm place-btn-action" 
                            data-slug="${row.slug}"
                            data-title="${row.title}"
                            title="choisir">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        createdRow: function (row, data, dataIndex) {
            $(row).find('td:eq(1)').addClass('text-end').css("width", "10%");
        }
    });
    $tablePlace.on('draw', function () {
        const btns = document.querySelectorAll('.place-btn-action');
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.hide();
                const text = editor.selection.getContent({ format: 'html' });
                let title = btn.dataset.title;
                if (text.length > 0) {
                    title = text;
                }

                editor.insertContent(`<a href="${HOST}/places/${btn.dataset.slug}">${title}</a>`);
            })
        });
    });

    const $tableArticle = $('#' + tableArticleId).DataTable({
        language: {
            url: '/assets/plugins/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging: true,
        info: false,
        autoWidth: false,
        ajax: '/api/admin/articles',
        columns: [
            { data: "title", name: "a.title" },
            {
                data: "actions",
                name: null,
                render: function (data, type, row) {
                    return `
                        <button 
                            class="btn btn-primary btn-sm article-btn-action" 
                            data-slug="${row.slug}"
                            data-title="${row.title}"
                            title="choisir">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        createdRow: function (row, data, dataIndex) {
            $(row).find('td:eq(1)').addClass('text-end').css("width", "10%");
        }
    });
    $tableArticle.on('draw', function () {
        const btns = document.querySelectorAll('.article-btn-action');
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.hide();
                const text = editor.selection.getContent({ format: 'html' });
                let title = btn.dataset.title;
                if (text.length > 0) {
                    title = text;
                }

                editor.insertContent(`<a href="${HOST}/articles/${btn.dataset.slug}">${title}</a>`);
            })
        });
    });
}

function enableFullEditor(textareaId, headings = "Titre 1=h2; Titre 2=h3; Titre 3=h4; Titre 4=h5; Titre 5=h6;", withScript = false) {
    document.querySelector(`#${textareaId}`).removeAttribute('required');
    const block_formats = 'Paragraph=p;' + headings + 'Division=div; Code=code;';

    const configs = {
        selector: '#' + textareaId,
        block_formats: block_formats,
        element_format : "xhtml",
        skin: SKIN,
        language: LANGUAGE,
        promotion: false,
        branding: false,
        content_css: CONTENT_CSS,
        link_context_toolbar: true,
        quickbars_insert_toolbar: QUICK_INSERT_TOOLBAR,
        contextmenu: CONTEXTMENU,
        quickbars_selection_toolbar: QUICK_SELECTION_TOOLBAR,
        toolbar: 'undo redo  | blocks | fontsizeinput |' + 
            'bold italic underline align | ' +
            'bullist numlist outdent indent | blockquote link image quicktable template searchWikiButton charmap |'  +
            'searchreplace preview fullscreen ' + (withScript ? 'code' : ''),
        menu: {
            file: { title: 'File', items: 'code wordcount | visualaid visualchars visualblocks | selectall preview fullscreen | newdocument print ' },
            format: { title: 'Format', items: 'bold italic underline strikethrough subscript superscript codeformat | blocks fontfamily fontsize align lineheight | forecolor backcolor | removeformat' },
            insert: {title: 'Insert', items: 'image link media template searchWikiButton inserttable | charmap hr | pagebreak nonbreaking anchor emoticons | insertdatetime'},
        
        },
        menubar: 'file format edit insert table help',
        plugins: [
            'advlist', 'anchor', 'autolink', 'charmap', 'code', 'fullscreen', 'help', 'image', 'importcss', 'insertdatetime', 'link', 'lists',
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
    
    tinyMCE.init(configs);
}