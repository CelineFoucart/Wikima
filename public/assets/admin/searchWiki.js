

function enableSearchTextPlugin(articleRoute, personRoute, placeRoute, tablePersonId, tableArticleId, tablePlaceId, modalId, editor) {
    editor.ui.addButton('autolinks',
        {
            label: "Ajouter le lien d'un élément textuel",
            command: 'autolinks_action', 
            toolbar: 'insert', 
            icon: '/assets/icons/articles.svg' 
        }
    );

    const modal = new bootstrap.Modal(document.getElementById(modalId), {
        keyboard: false
    });

    const $tablePerson = $(tablePersonId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging:false,
        info: false,
        autoWidth: false,
        ajax: personRoute,
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

    const $tableArticle = $(tableArticleId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging:false,
        info: false,
        autoWidth: false,
        ajax: articleRoute,
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

    const $tablePlace = $(tablePlaceId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        processing: true,
        serverSide: true,
        paging:false,
        info: false,
        autoWidth: false,
        ajax: placeRoute,
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

    editor.addCommand("autolinks_action", {
        exec: function (editor) {            
            modal.show();
        }
    });

    editor.ui.addButton('custom_models',
        {
            label: "Insérer l'un de vos modèles",
            command: 'custom_models_action', 
            toolbar: 'clipboard', 
            icon: '/assets/icons/templates.svg' 
        }
    );
    const tableId = 'table-modele';
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
    const modalModel = new bootstrap.Modal(modalElement, {
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
                            class="btn btn-primary btn-sm person-btn-action ${tableId}" 
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
                modalModel.hide();
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

function enableImagePlugin(tableId, modalId, editor) {
    editor.ui.addButton('gallery',
        {
            label: "Parcourir la galerie",
            command: 'gallery_action', 
            toolbar: 'insert', 
            icon: '/assets/icons/gallery.svg' 
        }
    );

    $(tableId).DataTable({
        language: {
            url: '/assets/DataTables/i18n/fr-FR.json',
        },
        paging:false,
        info: false,
        order: [[1, 'asc']]
    });
    
    const imageBtns = document.querySelectorAll('.close-btn-image');
    imageBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            modalGalleryModal.hide();
            const path = `/uploads/${btn.dataset.filename}`;
            const title = btn.dataset.title;
            editor.insertHtml( `
                <figure>
                    <a href="/images/${btn.dataset.slug}">
                        <img src="${path}" alt="${title}" title="${title}" style="max-width:100%">
                    </a>
                </figure> <p>&nbsp;</p>` 
            ); 
        })
    });

    const modalGalleryModal = new bootstrap.Modal(document.getElementById(modalId), {
        keyboard: false
    })

    editor.addCommand("gallery_action", {
        exec: function (editor) {            
            modalGalleryModal.show();
        }
    });
}