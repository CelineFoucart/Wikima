import { Modal } from "bootstrap";

export const HOST = location.origin;
export let modalElement = null;
export let modal  = null;

export function enableWikiLinksPlugin(textareaId, editor) {
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
    modal = new Modal(modalElement, {
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
        ajax: '/api/article',
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
