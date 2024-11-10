<template>
    <article class="card border-top-primary">
        <header class="card-header">
            <div class="float-end">
				<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm" @click="openUploadModal = true">
                        <i class="fa-solid fa-upload fa-fw"></i> 
                        <span class="ps-1 d-none d-md-inline-block">Ajouter</span>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" @click="openGalleryModal = true">
                        <i class="fa-solid fa-images fa-fw"></i> 
                        <span class="ps-1 d-none d-md-inline-block">Galerie</span>
                    </button>
                </div>
			</div>
            <h3 class="card-title h5 mb-0">Galerie</h3>
            <app-gallery-modal 
                v-if="openGalleryModal" 
                :type="type" 
                :entity="entityId" 
                :dateFormat="dateFormat" 
                @on-close="openGalleryModal = false" 
            />
            <upload-modal v-if="openUploadModal" @on-append="onAppend" @on-close="openUploadModal = false" />
        </header>
        <div class="card-body">
            <div class="image-container mb-2 align-items-start">
                <media-card 
                    v-for="media in entityGalleryStore.medias" 
                    :media="media" 
                    :dateFormat="dateFormat"
                    :withEntity="false"
                    :withRemoveBtn="true"
                    :key="media.id" 
                    @on-open-lightbox="openLightBox(sourceIndex[media.id] ? sourceIndex[media.id] : null)"
                    @on-append="onAppend"
                    @on-remove="onRemove"
                ></media-card>
                <div class="text-muted" v-if="entityGalleryStore.medias.length === 0 && loading === false">
                    Cette galerie ne contient pas d'images.
                </div>
            </div>
            <fs-lightbox :toggler="toggler" :slide="slide" :sources="sources"></fs-lightbox>
            <loading v-if="loading" />
        </div>
    </article>
</template>

<script>
import AppGalleryModal from '@components/image/AppGalleryModal.vue';
import UploadModal from '@components/image/fragments/UploadModal.vue';
import Loading from '@components/fragments/Loading.vue';
import MediaCard from '@components/image/fragments/MediaCard.vue';
import FsLightbox from "fslightbox-vue/v3";
import { createToastify } from '@functions/toastify.js';
import { useEntityGalleryStore } from '@store/entityGallery.js';
import { mapStores } from "pinia";

export default {
    name: 'EntityGallery',

    components: {
        'app-gallery-modal': AppGalleryModal,
        'media-card': MediaCard,
        'upload-modal': UploadModal,
        'fs-lightbox': FsLightbox,
        'loading': Loading,
    },

    computed: {
        ...mapStores(useEntityGalleryStore),
    },

    props: {
        entityId: Number,
        type: String,
        dateFormat: String,
    },

    data() {
        return {
            openGalleryModal: false,
            openUploadModal: false,
            sources: [],
            toggler: false,
            slide: 1,
            sourceIndex: {},
            loading: false
        }
    },

    async mounted () {
        this.loading = true;
        const status = await this.entityGalleryStore.getMedias(this.type, this.entityId);
        if (!status) {
            createToastify("La récupération des données a échoué.", "error");
        }
        this.setSourcesForLightBox();
        this.loading = false;
    },

    methods: {
        async onAppend(media) {
            this.openUploadModal = false;
            const status = await this.entityGalleryStore.appendMedia(this.type, this.entityId, media);
            if (!status) {
            createToastify("L'ajout a échoué.", "error");
        }
        },

        async onRemove(media) {
            this.loading = true;
            const status = await this.entityGalleryStore.removeMedia(this.type, this.entityId, media);
            if (!status) {
                createToastify("L'opération a échoué.", "error");
            }
            this.loading = false;
        },

        setSourcesForLightBox() {
            let index = 1;
            this.sources = [];
            this.entityGalleryStore.medias.forEach(element => {
                this.sources.push(Routing.generate('file_show', {id: element.id}));
                this.sourceIndex[element.id] = index;
                index++;
            });
        },

        openLightBox(index) {
            if (index === null) {
                return;
            }
            this.slide = index;
            this.toggler = !this.toggler;
        },
    },
}
</script>