<template>
    <form method="post">
        <modal title="Evénement" dialogClass="modal-lg" contentClass="border-top-primary" @on-close="$emit('on-close')">
            <template #body>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="timeline_event_title" class="form-label required">Titre</label>
                            <input type="text" id="timeline_event_title" v-model="data.title" class="form-control" :class="{ 'is-invalid': v$.data.title.$errors.length }">
                            <div class="invalid-feedback">Cette valeur ne doit pas être vide et doit faire entre 3 et 255 caractères.</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="timeline_event_duration" class="form-label required">Date ou durée</label>
                            <input type="text" id="timeline_event_duration" v-model="data.duration" class="form-control" :class="{ 'is-invalid': v$.data.duration.$errors.length }">
                            <div class="invalid-feedback">Cette valeur ne doit pas être vide et doit faire entre 1 et 255 caractères.</div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="timeline_event_presentation" class="form-label required">Présentation</label>
                    <textarea id="timeline_event_presentation" v-model="data.presentation" rows="5" class="form-control" :class="{ 'is-invalid': v$.data.presentation.$errors.length }"></textarea>
                    <div class="invalid-feedback">Cette valeur ne doit pas être vide et doit faire entre 3 et 2500 caractères.</div>
                </div>
            </template>
            <template #footer>
                <button type="button" class="btn btn-success btn-sm" @click.prevent="onSave">
                    <i class="fa-solid fa-spinner fa-spin" v-if="loading"></i>
                    <i class="fa-solid fa-floppy-disk" v-else></i>
                    Enregistrer
                </button>
            </template>
        </modal>
    </form>
</template>

<script>
import { mapStores } from "pinia";
import { useTimelineStore } from '@store/timeline.js';
import BootstrapModal from '@components/fragments/BootstrapModal.vue';
import { useVuelidate } from '@vuelidate/core'
import { required, maxLength, minLength  } from '@vuelidate/validators';
import { createToastify } from '@functions/toastify.js';

export default {
    name: 'EventModal',

    components: {
        'modal': BootstrapModal,
    },

    props: {
        event: {
            type: Object,
            default: null
        },
    },

    data() {
        return {
            data: {
                title: null,
                duration: null,
                presentation: null
            },
            v$: useVuelidate(),
            loading: false
        }
    },

    computed: {
        ...mapStores(useTimelineStore),
    },

    validations () {
        return {
            data: {
                title: { required, maxLength: maxLength(255), minLength: minLength(3) },
                duration: { required, maxLength: maxLength(255), minLength: minLength(1) },
                presentation: { required, maxLength: maxLength(2500), minLength: minLength(3) }
            }
        }
    },

    mounted () {
        if (this.event !== null) {
            this.data.title = this.event.title;
            this.data.duration = this.event.duration;
            this.data.presentation = this.event.presentation;
        }
    },

    methods: {
        async onSave() {
            this.loading = true;
            const result = await this.v$.$validate();

            if (!result) {
                createToastify('Le formulaire comporte des erreurs.', 'error');
                this.loading = false;
                return;
            }

            let status = false;
            if (this.event !== null) {
                status = await this.timelineStore.editEvent(this.event, this.data)
            } else {
                status = await this.timelineStore.addEvent(this.data)
            }

            if (status) {
                createToastify("L'événement a été sauvegardé.", 'success');
                this.$emit('on-close');
            } else {
                createToastify('Le formulaire comporte des erreurs.', 'error');
            }
        }
    },
}
</script>