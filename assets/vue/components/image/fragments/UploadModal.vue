<template>
    <form name="image" method="post" enctype="multipart/form-data">
        <modal title="Uploader une image" dialogClass="modal-xl" contentClass="border-top-primary" @on-close="$emit('on-close')">
            <template #body>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image_title" class="form-label required">Titre</label>
                        <input type="text" id="image_title" required="required" class="form-control" v-model="media.title" :class="{ 'is-invalid': v$.media.title.$errors.length }">
                        <div class="invalid-feedback">Cette valeur ne doit pas être vide et doit faire entre 2 et 255 caractères.</div>
                    </div>
                    <slug-type v-model:data="media.slug" id="image_slug" :title="media.title" :errors="v$.media.slug.$errors" />
                    <div class="mb-3">
                        <label for="image_keywords" class="form-label required">Mots clés</label>
                        <input type="text" id="image_keywords" required="required" class="form-control" v-model="media.keywords" :class="{ 'is-invalid': v$.media.keywords.$errors.length }">
                        <div class="invalid-feedback">Cette valeur ne doit pas être vide et doit faire entre 2 et 255 caractères.</div>
                    </div>
                    <div class="mb-3">
                        <label for="image_description" class="form-label required">Description</label>
                        <textarea id="image_description" required="required" class="form-control" v-model="media.description" :class="{ 'is-invalid': v$.media.description.$errors.length }"></textarea>
                        <div class="form-text mb-0 help-text">Courte présentation de moins de 255 caractères.</div>
                        <div class="invalid-feedback">Cette valeur ne doit pas être vide et doit faire entre 10 et 255 caractères.</div>
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
                                <label class="form-label" for="image_imageGroups">Groupes d'image</label>
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
                        <input 
                            type="file"
                            ref="fileInput"
                            id="image_imageFile_file" 
                            required="required" 
                            accept="image/*" 
                            class="form-control"
                            @change="onUpload"
                            :class="{ 'is-invalid': v$.media.imageFile.$errors.length }"
                        >
                        <div class="invalid-feedback">Cette valeur ne doit pas être vide.</div>
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
import SlugType from '@components/fragments/form/SlugType.vue';
import { useVuelidate } from '@vuelidate/core'
import { required, maxLength, minLength  } from '@vuelidate/validators';
import { createToastify } from '@functions/toastify.js';

export default {
    name: 'UploadModal',

    components: {
        "modal": BootstrapModal,
        'loading': Loading,
        'slug-type': SlugType,
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
            },
            v$: useVuelidate(),
        }
    },

    computed: {
        ...mapStores(useMediaStore, useCategoryStore, usePortalStore, useImageGroupStore),
    },

    validations () {
        return {
            media: {
                title: { required, maxLength: maxLength(255), minLength: minLength(2) },
                slug: { required, maxLength: maxLength(255), minLength: minLength(2) },
                keywords: { required, maxLength: maxLength(255), minLength: minLength(2) },
                description: { required, maxLength: maxLength(255), minLength: minLength(10) },
                imageFile: { required }
            }
        }
    },

    async mounted () {
        this.loading = true;
        await this.mediaStore.getMediaTypes();
        await this.portalStore.getPortals();
        await this.categoryStore.getCategories();
        await this.imageGroupStore.getImageGroups();
        this.loading = false;
    },

    methods: {
        async onSave() {
            this.loading = true;
            const result = await this.v$.$validate();

            if (!result) {
                createToastify('Le formulaire comporte des erreurs.', 'error');
                this.loading = false;
                return;
            }

            const status = await this.mediaStore.postMedia(this.media);
            if (!status) {
                createToastify("L'opération a échoué.", "error");
            } else if(this.mediaStore.lastInserted !== null) {
                this.$emit('on-append', this.mediaStore.lastInserted);
            }

            this.loading = false;
        },

        onUpload() {
            if (this.$refs.fileInput.files.length > 0) {
                this.media.imageFile = this.$refs.fileInput.files[0];
            }
        }
    },
}
</script>
