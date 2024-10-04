<template>
    <div :id="id" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" :class="dialogClassList" role="document">
            <div :class="'modal-content ' + contentClass">
                <div :class="'modal-header d-block ' + headerClass">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h5 class="modal-title" :class="titleClass">{{ title }}</h5> 
                        </div>
                        <div class="col-4 text-end">
                            <div class="btn-group">
                                <button type="button" class="btn" aria-label="fermeture" @click="maximize = !maximize"> 
                                    <i class="fa-solid fa-expand" v-if="maximize === false"></i>
                                    <i class="fa-solid fa-compress" v-else></i>
                                </button>
                                <button type="button" class="btn" aria-label="fermeture" @click.prevent="onClose"> 
                                    <i class="fa-solid fa-xmark fa-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <slot name="body"></slot>
                </div>
                <div class="modal-footer" v-if="$slots.footer" :class="footerClass">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from "bootstrap";

export default {
    name: 'BootstrapModal',

    emits: ["on-close"],

    props: {
        title: String,
        dialogClass: {
            type: String,
            default: ''
        },
        titleClass: {
            type: String,
            default: ''
        },
        contentClass: {
            type: String,
            default: ''
        },
        headerClass: {
            type: String,
            default: ''
        },
        footerClass: {
            type: String,
            default: ''
        },
    },

    data() {
        return {
            maximize: false,
            modal: null
        }
    },

    computed: {
        id() {
            return this.uniqid() + this.uniqid() + this.uniqid();
        },

        dialogClassList() {
            const size = this.maximize ? 'modal-fullscreen' : 'modal-dialog-scrollable';

            return size + ' ' + this.dialogClass;
        }
    },

    mounted () {
        this.element = document.getElementById(this.id);
        this.modal = Modal.getOrCreateInstance(this.element);
        this.modal.show();
    },

    unmounted() {
        this.modal.hide();
    },

    methods: {
        uniqid() {
            return Math.random().toString(36).substring(2, 27);
        },

        onClose() {
            this.$emit('on-close');
        },
    },
}
</script>
