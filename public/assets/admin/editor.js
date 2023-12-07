const basicToolbar = [
    ["Bold","Italic","Underline","Strike","Subscript","Superscript","-", "CopyFormatting", "RemoveFormat"],
    ["NumberedList","BulletedList","-","Outdent","Indent","-","Blockquote", "Image", "Table", "SpecialChar", "-","JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"], 
    ["FontSize", "TextColor", "BGColor", "-", "Link", "Unlink", "-", 'Maximize']
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

function enableTemplatePlugin(textareaId, editor) {
    editor.ui.addButton('custom_models',
        {
            label: "Insérer l'un de vos modèles",
            command: 'custom_models_action', 
            toolbar: 'clipboard', 
            icon: '/assets/icons/templates.svg' 
        }
    );

    const tableId = textareaId + 'table-modele';
    const modalElement = document.createElement('div');
    modalElement.classList = 'modal';
    modalElement.tabindex = "-1";
    modalElement.innerHTML = `
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light p-2">
                    <h5 class="modal-title">Modèles personnalisés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <table class="table table-striped" id="${tableId}">
                    <thead>
                        <tr>
                            <th>Modèle</th>
                            <th data-orderable="false">Choisir</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modalElement);
    const modal = new bootstrap.Modal(modalElement, {
        keyboard: false
    });
    const $table = $('#'+tableId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        info: false,
        autoWidth: false,
        ajax: '/api/template',
        columns: [
            { 
                data: "title", 
                name:"t.title", 
                render: function ( data, type, row ) {
                    return `
                        <p class="fw-bold mb-0">${row.title}</p>
                        <p class="mb-0">${row.description ? row.description : ''}</p>
                    `;
                }
            },
            { 
                data: "actions", 
                name:null, 
                render: function ( data, type, row ) {
                    return `
                        <button 
                            class="btn btn-primary btn-sm template-btn-action ${tableId}" 
                            title="choisir">
                            <i class="fas fa-check"></i>
                        </button>
                        <div style="display:none">${row.content}</div>
                    </div>`;
                }
            }
        ],
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(1)').addClass('text-end').css("width", "10%");
        }
    });
    $table.on( 'draw', function () {
        const btns = document.querySelectorAll('.'+tableId);
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                modal.hide();
                const template = btn.nextElementSibling.innerHTML;
                editor.insertHtml( template ); 
            })
        });
    });

    editor.addCommand("custom_models_action", {
        exec: function (editor) {  
            modal.show();
        }
    });
}

function enableWikiLinksPlugin(textareaId, editor) {
    editor.ui.addButton('autolinks',
        {
            label: "Ajouter le lien d'un élément textuel",
            command: 'autolinks_action', 
            toolbar: 'insert', 
            icon: '/assets/icons/articles.svg' 
        }
    );

    const tablePersonId = textareaId + 'tablePerson';
    const tablePlaceId = textareaId + 'tablePlace';
    const tableArticleId = textareaId + 'tableArticle';

    const modalElement = document.createElement('div');
    modalElement.classList = 'modal';
    modalElement.tabindex = "-1";
    modalElement.innerHTML = `
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light p-2">
                    <h4 class="modal-title h5">Rechercher un élément</h4>
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
                    <div class="tab-content" id="myTabContent">
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
    const modal = new bootstrap.Modal(modalElement, {
        keyboard: false
    });

    const $tablePerson = $('#'+tablePersonId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging:true,
        info: false,
        autoWidth: false,
        ajax: '/api/admin/persons',
        columns: [
            { data: "firstname", name:"p.firstname" },
            { data: "lastname", name:"p.lastname" },
            { 
                data: "actions", 
                name:null, 
                render: function ( data, type, row ) {
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
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(2)').addClass('text-end').css("width", "10%");
        }
    });
    $tablePerson.on( 'draw', function () {
        const btns = document.querySelectorAll('.person-btn-action');
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.hide();
                const text = editor.getSelection().getSelectedText();
                let title = btn.dataset.title;
                if (text.length > 0) {
                    title = text;
                }

                editor.insertHtml( `<a href="/persons/${btn.dataset.slug}">${title}</a>` ); 
            })
        });
    });

    const $tablePlace = $('#'+tablePlaceId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging:true,
        info: false,
        autoWidth: false,
        ajax: '/api/admin/place',
        columns: [
            { data: "title", name:"p.title" },
            { 
                data: "actions", 
                name:null, 
                render: function ( data, type, row ) {
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
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(1)').addClass('text-end').css("width", "10%");
        }
    });
    $tablePlace.on( 'draw', function () {
        const btns = document.querySelectorAll('.place-btn-action');
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.hide();
                const text = editor.getSelection().getSelectedText();
                let title = btn.dataset.title;
                if (text.length > 0) {
                    title = text;
                }

                editor.insertHtml( `<a href="/places/${btn.dataset.slug}">${title}</a>` ); 
            })
        });
    });

    const $tableArticle = $('#'+tableArticleId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging:true,
        info: false,
        autoWidth: false,
        ajax: '/api/admin/articles',
        columns: [
            { data: "title", name:"a.title" },
            { 
                data: "actions", 
                name:null, 
                render: function ( data, type, row ) {
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
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(1)').addClass('text-end').css("width", "10%");
        }
    });
    $tableArticle.on( 'draw', function () {
        const btns = document.querySelectorAll('.article-btn-action');
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.hide();
                const text = editor.getSelection().getSelectedText();
                let title = btn.dataset.title;
                if (text.length > 0) {
                    title = text;
                }

                editor.insertHtml( `<a href="/articles/${btn.dataset.slug}">${title}</a>` ); 
            })
        });
    });

    editor.addCommand("autolinks_action", {
        exec: function (editor) {            
            modal.show();
        }
    });
}

function enableFullEditor(textareaId, formatTags = "p;h2;h3;h4;h5;h6;pre;address;div") {
    const editor = CKEDITOR.replace(textareaId, {
        "htmlEncodeOutput":false,
        "allowedContent":true,
        "format_tags": formatTags
    });

    enableTemplatePlugin(textareaId, editor);
    enableWikiLinksPlugin(textareaId, editor);
    
}