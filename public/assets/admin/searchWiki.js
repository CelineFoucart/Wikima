

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