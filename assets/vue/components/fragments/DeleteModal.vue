<template>
    <modal title="Confirmation de suppression" contentClass="delete-modal" titleClass="text-danger">
        <template #body>
            <p class="mb-0">
                Attention, toute suppression est définitive ! Voulez-vous vraiment supprimer 
                l'élément "<em>{{ title }}</em>" ?
            </p>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary btn-sm" tabindex="-1" @click.prevent="$emit('on-close')">
                <i class="fas fa-times fa-fw"></i> Annuler
            </button>
            <button type="button" class="btn btn-danger btn-sm" @click.prevent="accept">
                <i class="fa-solid fa-spinner fa-spin fa-fw" v-if="loading"></i>
                <i class="fas fa-trash-alt fa-fw" v-else></i>
                Confirmer
            </button>
        </template>
    </modal>
</template>

<script>
import BootstrapModal from '@components/fragments/BootstrapModal.vue';

export default {
    name: "DeleteModal",

    components: {
        'modal': BootstrapModal,
    },

    emits: ['on-confirm'],

    props: {
        title: String,
        loading: {
            type: Boolean,
            default: false
        }
    },

    methods: {
        accept() {
            this.$emit('on-confirm');
        }
    },
}
</script>

<style scoped>
#modalDelete {
    display: block;
}
</style>