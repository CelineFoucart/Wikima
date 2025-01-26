<template>
    <header class="bg-light border rounded p-2">
        <div class="row align-items-center">
            <div class="col-8">
                <h3 class="h6 mb-0">
                    {{ mediaStore.pagination.firstItemNumber }} à {{  mediaStore.pagination.lastItemNumber }} sur
                    {{ mediaStore.pagination.totalCount }} image{{ mediaStore.pagination.totalCount > 1 ? 's' : '' }}
                </h3>
            </div>
            <div class="col-4 text-end">
                <button type="button" class="btn btn-success btn-sm" @click="openUploadModal = true">
                    <i class="fas fa-plus-circle fa-fw" aria-hidden="true"></i>
                    <span class="ps-1 d-none d-md-inline-block">Ajouter</span>
                </button>
            </div>
        </div>
        <upload-modal 
            v-if="openUploadModal"
            :portal="portalId" 
            :category="categoryId"
            @on-append="onAppend" 
            @on-close="openUploadModal = false"
        />
    </header>
</template>

<script>
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';
import UploadModal from '@components/image/fragments/UploadModal.vue';

export default {
    name: 'HeaderGallery',

    components: {
        'upload-modal': UploadModal,
    },

    emits: ['on-refresh'],

    data() {
        return {
            openUploadModal: false,
            categoryId: null,
            portalId: null,
        }
    },

    computed: {
        ...mapStores(useMediaStore),
    },

    mounted () {
        const status = this.getParamsFromUrl();
        if (status) {
            this.openUploadModal = true;
        }
    },

    methods: {
        getParamsFromUrl() {
            const url = new URL(window.location.href);
            let openModal = false;
            if (url.searchParams.has('category') && url.searchParams.get('category') !== null) {
                this.categoryId = parseInt(url.searchParams.get('category'));
                url.searchParams.delete('category');
            } else if (url.searchParams.has('portal') && url.searchParams.get('portal') !== null) {
                this.portalId = parseInt(url.searchParams.get('portal'));
                url.searchParams.delete('portal');
            } else if (url.searchParams.has('create')) {
                openModal = true;
                url.searchParams.delete('create');
            }

            window.history.pushState({}, document.title, url);

            return this.portalId !== null || this.categoryId !== null || openModal === true;
        },

        async onAppend() {
            this.openUploadModal = false;
            this.$emit('on-refresh');
        },
    },
}
</script>