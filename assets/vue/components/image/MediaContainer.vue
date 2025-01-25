<template>
    <section>
        <header-gallery @on-refresh="getMedias" />
        <div class="row flex-lg-row-reverse mt-3">
            <div class="col-lg-4 col-xl-3 border-search position-relative">
                <search-form @on-search="onSearch" />
            </div>
            <div class="col-lg-8 col-xl-9">
                <div class="image-container mb-2 align-items-start">
                    <media-card 
                        v-for="media in mediaStore.medias" 
                        :media="media" 
                        :dateFormat="dateFormat"
                        :withEntity="withEntity"
                        :key="media.id" 
                        @on-open-lightbox="openLightBox(sourceIndex[media.id] ? sourceIndex[media.id] : null)"
                        @on-append="onAppend"
                        @on-remove="getMedias"
                    ></media-card>
                </div>
                <pagination-media :pagination="mediaStore.pagination" @on-change="onPaginate" />
            </div>
        </div>
        <loading v-if="loading" />
        <fs-lightbox :toggler="toggler" :slide="slide" :sources="sources"></fs-lightbox>
    </section>
</template>

<script>
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';
import HeaderGallery from '@components/image/fragments/HeaderGallery.vue';
import SearchForm from '@components/image/fragments/SearchForm.vue';
import MediaCard from '@components/image/fragments/MediaCard.vue';
import PaginationMedia from '@components/image/fragments/PaginationMedia.vue';
import Loading from '@components/fragments/Loading.vue';
import FsLightbox from "fslightbox-vue/v3";
import { createToastify } from '@functions/toastify.js';

export default {
    name: 'MediaContainer',

    components: {
        'header-gallery': HeaderGallery,
        'search-form': SearchForm,
        'pagination-media': PaginationMedia,
        'loading': Loading,
        'media-card': MediaCard,
        'fs-lightbox': FsLightbox,
    },

    emits: ['on-append', 'on-delete'],

    props: {
        dateFormat: String,
        withEntity: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            params: {
                query: null,
                categories: [],
                portals: [],
                tags: [],
            },
            currentPage: 1,
            currentLimit: 10,
            loading: false,
            sources: [],
            toggler: false,
            slide: 1,
            sourceIndex: {},
        }
    },

    computed: {
        ...mapStores(useMediaStore),
    },

    async mounted () {
        await this.getMedias();
    },

    methods: {
        async onSearch(filter) {
            this.params.query = filter.query;

            this.params.categories = [];
            for (const category of filter.categories) {
                this.params.categories.push(category.id);
            }

            this.params.portals = [];
            for (const portal of filter.portals) {
                this.params.portals.push(portal.id);
            }

            this.params.tags = [];
            for (const tag of filter.tags) {
                this.params.tags.push(tag.id);
            }

            await this.getMedias();
        },

        async onPaginate(pagination) {
            this.currentLimit = pagination.limit;
            this.page = pagination.page;
            await this.getMedias();
        },

        async getMedias() {
            this.loading = true;
            const status = await this.mediaStore.getMedias(this.page, this.currentLimit, this.params);
            if (!status) {
                createToastify("Le chargement des médias a échoué", 'error');
            } 
            
            this.setSourcesForLightBox();
            this.loading = false;
        },

        setSourcesForLightBox() {
            let index = 1;
            this.sources = [];
            this.mediaStore.medias.forEach(element => {
                this.sources.push(Routing.generate('file_show', {id: element.id}));
                this.sourceIndex[element.id] = index;
                index++;
            });
        },

        openLightBox(index) {
            if (index === null || this.selectMultiple === true) {
                return;
            }
            this.slide = index;
            this.toggler = !this.toggler;
        },

        async onAppend(media) {
            this.$emit('on-append', media);
        }
    },
}
</script>

<style scoped>
@media (min-width: 992px) {
    .border-search {
        border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color);
    }
}

.image-container {
    min-height: 375px;
}
</style>
