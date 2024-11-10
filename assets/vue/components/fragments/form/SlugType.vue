<template>
    <div class="mb-3">
        <label :for="id" class="form-label required">Slug</label>
        <div class="input-group">
            <input type="text" :id="id" required="required" class="form-control" v-model="slug" :class="{ 'is-invalid': errors.length }">
            <span class="input-group-text button-sync" v-tooltip="'Générer'" @click="generate">
                <i class="fas fa-sync-alt"></i>
            </span>
        </div>
        <div class="invalid-feedback d-block" v-if="errors.length">
            Cette valeur ne doit pas être vide et doit faire entre 12 et 255 caractères.
        </div>
    </div>
</template>

<script>
export default {
    name: 'SlugType',

    props: {
        data: {
            type: String,
            default: null
        },
        id: String,
        title: {
            type: String,
            default: null
        },
        errors: {
            type: Array,
            default: []
        } 
    },

    data() {
        return {
            slug: null
        }
    },

    watch: {
        slug() {
            this.$emit('update:data', this.slug);
        },

        data() {
            if (this.data !== this.slug) {
                this.slug = this.data;
            }
        },
    },

    mounted () {
        this.slug = this.data;
    },

    methods: {
        slugify(str) {
            str = str.trim();
            str = str.toLowerCase();
            str = str.replace(/[\s_-]+/g, '-');
            str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            str = str.replace(/[^\w\s-]/g, '');

            return str;
        },

        generate() {
            if (this.title === null) {
                return;
            }

            this.slug = this.slugify(this.title);
            return;
        }
    },
}
</script>

<style scoped>
.button-sync {
    cursor: pointer;
}
</style>
