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
        <upload-modal v-if="openUploadModal" @on-append="onAppend" @on-close="openUploadModal = false" />
    </header>
</template>

<script>
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';
import { createToastify } from '@functions/toastify.js';
import UploadModal from '@components/image/fragments/UploadModal.vue';

export default {
    name: 'HeaderGallery',

    components: {
        'upload-modal': UploadModal,
    },

    emits: ['on-refresh'],

    data() {
        return {
            openUploadModal: false
        }
    },

    computed: {
        ...mapStores(useMediaStore),
    },

    methods: {
        async onAppend() {
            this.openUploadModal = false;
            this.$emit('on-refresh');
        },
    },
}
</script>