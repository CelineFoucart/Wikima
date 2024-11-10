<template>
    <modal title="Galerie" dialogClass="modal-xl" contentClass="border-top-primary" @on-close="$emit('on-close')">
        <template #body>
            <media-container 
                :dateFormat="dateFormat" 
                :withEntity="true" 
                @on-append="appendMedia"
            ></media-container>
            <loading v-if="loading" />
        </template>
    </modal>
</template>

<script>
import BootstrapModal from '@components/fragments/BootstrapModal.vue';
import Loading from '@components/fragments/Loading.vue';
import { mapStores } from "pinia";
import MediaContainer from '@components/image/MediaContainer.vue';
import { createToastify } from '@functions/toastify.js';
import { useEntityGalleryStore } from '@store/entityGallery.js';

export default {
    name: 'AppGalleryModal',

    emits: ['on-close'],

    components: {
        "media-container": MediaContainer,
        "modal": BootstrapModal,
        'loading': Loading,
    },

    props: {
        type: String,
        entity: Number,
        dateFormat: String,
    },

    data() {
        return {
            loading: false,
        }
    },

    computed: {
        ...mapStores(useEntityGalleryStore),
    },

    methods: {
        async appendMedia(media) {
            this.loading = true;
            const status = await this.entityGalleryStore.appendMedia(this.type, this.entity, media);
            
            if (status) {
                createToastify("Les modifications ont été sauvegardée. ", 'success');
            } else {
                createToastify("L'opération a échoué", 'error');
            }
            this.loading = false;
        },
    },
}
</script>
