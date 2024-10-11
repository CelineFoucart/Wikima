<template>
    <section>
        <header-gallery />
        <div class="row flex-lg-row-reverse mt-3">
            <div class="col-lg-4 border-search">
                <search-form @on-search="onSearch" />
            </div>
            <div class="col-lg-8">
                <media-list />
                <pagination-media :pagination="mediaStore.pagination" @on-change="onPaginate" />
            </div>
        </div>
        <loading v-if="loading" />
    </section>
</template>

<script>
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';
import HeaderGallery from '@components/image/fragments/HeaderGallery.vue';
import SearchForm from '@components/image/fragments/SearchForm.vue';
import MediaList from '@components/image/fragments/MediaList.vue';
import PaginationMedia from '@components/image/fragments/PaginationMedia.vue';
import Loading from '@components/fragments/Loading.vue';

export default {
    name: 'MediaContainer',

    components: {
        'header-gallery': HeaderGallery,
        'search-form': SearchForm,
        'media-list': MediaList,
        'pagination-media': PaginationMedia,
        'loading': Loading,
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
            loading: false
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
            
            // this.setSourcesForLightBox();
            this.loading = false;
        },
    },
}
</script>

<style scoped>
@media (min-width: 992px) {
    .border-search {
        border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color);
    }
}
</style>
