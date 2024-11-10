<template>
    <form name="image" method="post" enctype="multipart/form-data">
        <modal title="Uploader une image" dialogClass="modal-xl" contentClass="border-top-primary" @on-close="$emit('on-close')">
            <template #body>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image_title" class="form-label required">Titre</label>
                        <input type="text" id="image_title" required="required" class="form-control" v-model="media.title">
                    </div>
                    <div class="mb-2">
                        <label for="image_slug" class="form-label required">Slug</label>
                        <div class="input-group">
                            <input type="text" id="image_slug" required="required" class="form-control" v-model="media.slug">
                            <span class="input-group-text" data-action="slugify" data-target="#image_slug" role="btton" data-source="#image_title" data-bs-toggle="tooltip"
                                aria-label="Générer" data-bs-original-title="Générer">
                                <i class="fas fa-sync-alt"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image_keywords" class="form-label required">Mots clés</label>
                        <input type="text" id="image_keywords" required="required" class="form-control" v-model="media.keywords">
                    </div>
                    <div class="mb-3">
                        <label for="image_description" class="form-label required">Description</label>
                        <textarea id="image_description" required="required" class="form-control" v-model="media.description"></textarea>
                        <div class="form-text mb-0 help-text">Courte présentation de moins de 255 caractères.</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="image_categories">Catégories</label>
                                <vue-multiselect 
                                    v-model="media.categories" 
                                    :options="categoryStore.categories" 
                                    placeholder="Choisir" 
                                    :multiple="true"
                                    :close-on-select="false"
                                    select-label="Appuyer sur entrée pour choisir"
                                    selected-label="Sélectionné"
                                    deselect-label="Appuyer sur entrée pour enlever"
                                    track-by="id"
                                    label="title"
                                > 
                                    <template #noResult>Aucun résultat</template>
                                    <template #noOptions>La liste est vide</template>
                                </vue-multiselect>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="image_portals">Portails</label>
                                <vue-multiselect 
                                    v-model="media.portals" 
                                    :options="portalStore.portals" 
                                    placeholder="Choisir" 
                                    :multiple="true"
                                    :close-on-select="false"
                                    select-label="Appuyer sur entrée pour choisir"
                                    selected-label="Sélectionné"
                                    deselect-label="Appuyer sur entrée pour enlever"
                                    track-by="id"
                                    label="title"
                                > 
                                    <template #noResult>Aucun résultat</template>
                                    <template #noOptions>La liste est vide</template>
                                </vue-multiselect>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="image_tags">Types d'image</label>
                                <vue-multiselect 
                                    v-model="media.tags" 
                                    :options="mediaStore.types" 
                                    placeholder="Choisir" 
                                    :multiple="true"
                                    :close-on-select="false"
                                    select-label="Appuyer sur entrée pour choisir"
                                    selected-label="Sélectionné"
                                    deselect-label="Appuyer sur entrée pour enlever"
                                    track-by="id"
                                    label="title"
                                > 
                                    <template #noResult>Aucun résultat</template>
                                    <template #noOptions>La liste est vide</template>
                                </vue-multiselect>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="image_imageGroups">groupes d'image</label>
                                <vue-multiselect 
                                    v-model="media.imageGroups" 
                                    :options="imageGroupStore.imageGroups" 
                                    placeholder="Choisir" 
                                    :multiple="true"
                                    :close-on-select="false"
                                    select-label="Appuyer sur entrée pour choisir"
                                    selected-label="Sélectionné"
                                    deselect-label="Appuyer sur entrée pour enlever"
                                    track-by="id"
                                    label="title"
                                > 
                                    <template #noResult>Aucun résultat</template>
                                    <template #noOptions>La liste est vide</template>
                                </vue-multiselect>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label required" for="image_imageFile_file">Fichier de l'image</label>
                        <input type="file" id="image_imageFile_file"  required="required" accept="image/*" class="form-control" @on-change="onUpload">
                    </div>
                </div>
                <loading v-if="loading" />
            </template>
            <template #footer>
                <button type="button" class="btn btn-primary btn-sm" @click.prevent="onSave">
                    <i class="fa-solid fa-spinner fa-spin" v-if="loading"></i>
                    <i class="fa-solid fa-floppy-disk" v-else></i>
                    Ajouter
                </button>
            </template>
        </modal>
    </form>
</template>

<script>
import BootstrapModal from '@components/fragments/BootstrapModal.vue';
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';
import { useCategoryStore } from '@store/category.js';
import { useImageGroupStore } from '@store/imageGroup.js';
import { usePortalStore } from '@store/portal.js';
import Loading from '@components/fragments/Loading.vue';
import VueMultiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
    name: 'UploadModal',

    components: {
        "modal": BootstrapModal,
        'loading': Loading,
        'vue-multiselect': VueMultiselect
    },

    data() {
        return {
            loading: false,
            media: {
                title: null,
                slug: null,
                keywords: null,
                description: null,
                categories: [],
                portals: [],
                tags: [],
                imageGroups: [],
                imageFile: null
            }
        }
    },

    computed: {
        ...mapStores(useMediaStore, useCategoryStore, usePortalStore, useImageGroupStore),
    },

    async mounted () {
        this.loading = true;
        await this.mediaStore.getMediaTypes();
        await this.portalStore.getPortals();
        await this.categoryStore.getCategories();
        await this.imageGroupStore.getCategories();
        this.loading = false;
    },

    methods: {
        onSave() {
            
        },

        onUpload(e) {
            console.log(e)
        }
    },
}
</script>
