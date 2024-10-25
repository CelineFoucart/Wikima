<template>
    <div class="image-block">
        <div v-tooltip="media.title" @click="$emit('on-open-lightbox')" role="button">
            <img :src="thumbRoute" :alt="media.title" class="thumbnail">
        </div>
        <div class="w-100 mt-1 d-flex justify-content-between">
            <a :href="publicShowRoute" class="btn btn-sm btn-default" v-tooltip="'Afficher publiquement'">
                <i class="fas fa-book-reader"></i>
            </a>                   

            <div class="btn-group">
                <button type="button" @click="openShowModal = true" class="btn btn-sm btn-default" v-tooltip="'Voir les information'">
                    <i class="fas fa-info-circle"></i>
                </button>
                <a :href="convertMapRoute" class="btn btn-default btn-sm" v-tooltip="'convertir en carte'">
                    <i class="fas fa-map"></i>
                </a>
                <a :href="editRoute" class="btn btn-sm btn-default" v-tooltip="'Éditer'">
                    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
    <show-modal :image="media" :dateFormat="dateFormat" v-if="openShowModal" @on-close="openShowModal = false" />
</template>

<script>
import ShowModal from '@components/image/fragments/ShowModal.vue';

export default {
    name: 'MediaCard',

    components: {
        'show-modal': ShowModal,
    },

    emits: ['on-open-lightbox'],

    props: {
        media: Object,
        dateFormat: String
    },

    data() {
        return {
            openShowModal: false
        }
    },

    computed: {
        thumbRoute() {
            return Routing.generate('file_thumb', {id: this.media.id});
        },

        publicShowRoute() {
            return Routing.generate('app_image_show', {slug: this.media.slug});
        },

        editRoute() {
            return Routing.generate('admin_app_image_edit', {id: this.media.id});
        },

        convertMapRoute() {
            return Routing.generate('admin_app_map_create', {image: this.media.id})
        }
    },
}
</script>

<style>
.thumbnail {
    width: 150px;
    height: 150px;
    object-fit: cover;
}
</style>