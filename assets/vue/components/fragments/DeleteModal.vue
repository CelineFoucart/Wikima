<template>
    <div>
        <div class="modal-backdrop fade show"></div>
        <div class="modal fade show" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title h5 text-danger" id="modalDeleteConfigLabel">Suppression</h3>
                        <button type="button" class="btn-close" aria-label="fermeture" @click.prevent="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger fs-5 mb-0">
                            Attention, toute suppression est définitive ! Voulez-vous vraiment supprimer 
                            l'élément <span class="fw-bold">{{ title }}</span> ?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" tabindex="-1" @click.prevent="closeModal">
                            <i class="fa-solid fa-xmark fa-fw"></i> Annuler
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" @click.prevent="accept">
                            <i class="fa-solid fa-spinner fa-spin fa-fw" v-if="loading"></i>
                            <i class="fa-solid fa-trash fa-fw" v-else></i>
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "DeleteModal",

        props: {
            title: String,
            loading: {
                type: Boolean,
                default: false
            }
        },

        methods: {
            closeModal() {
                this.$emit('on-cancel');
            },

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