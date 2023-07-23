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
    const editor = CKEDITOR.replace(textareaId, {
        "htmlEncodeOutput":false,
        "allowedContent":true,
        "format_tags": formatTags
    });

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