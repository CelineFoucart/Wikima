<template>
    <div class="image-block">
        <div v-tooltip="media.title" @click="$emit('on-open-lightbox')" role="button">
            <img :src="thumbRoute" :alt="media.title" class="thumbnail">
        </div>
        <div class="w-100 mt-1 d-flex" :class="(withEntity || withRemoveBtn) ? 'justify-content-between' : 'justify-content-end'">
            <button type="button" v-if="withEntity" class="btn btn-sm btn-default" v-tooltip="'Ajouter'" @click="onAppend">
                <i class="fas fa-plus fa-fw"></i>
            </button>     
            <button type="button" v-if="withRemoveBtn" class="btn btn-sm btn-danger" v-tooltip="'Retirer'"  @click="onRemove">
                <i class="fa-solid fa-link-slash fa-fw"></i>
            </button>             

            <div class="btn-group">
                <button type="button" @click="openShowModal = true" class="btn btn-sm btn-default" v-tooltip="'Voir les information'">
                    <i class="fas fa-info-circle fa-fw"></i>
                </button>
                <div class="dropdown btn-group">
                    <button class="btn btn-sm btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical fa-fw"></i>
                        <span class="visually-hidden">Actions</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" :href="convertMapRoute">
                                <i class="fas fa-map fa-fw"></i> Convertir en carte
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" :href="editRoute">
                                <i class="fas fa-pencil-alt fa-fw" aria-hidden="true"></i> Éditer
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" :href="publicShowRoute">
                                <i class="fas fa-book-reader fa-fw"></i> Afficher publiquement
                            </a>
                        </li>
                    </ul>
                </div>
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

    emits: ['on-open-lightbox', 'on-append', 'on-remove'],

    props: {
        media: Object,
        dateFormat: String,
        withEntity: Boolean,
        withRemoveBtn: {
            type: Boolean,
            default: false
        }
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

    methods: {
        onAppend() {
            this.$emit('on-append', this.media);
        },

        onRemove() {
            this.$emit('on-remove', this.media);
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