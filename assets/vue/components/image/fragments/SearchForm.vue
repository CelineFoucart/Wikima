<template>
    <form method="get">
        <div class="mb-3">
            <label for="query" class="fw-bold form-label">Par mot clé</label>
            <input type="text" id="query" v-model="params.query" placeholder="Recherche..." class="form-control search-input form-control">
        </div>
        <div class="mb-3">
            <label for="categories" class="fw-bold form-label">Par catégorie</label>
            <vue-multiselect 
                v-model="params.categories" 
                :options="categoryStore.categories" 
                placeholder="Choisir" 
                :multiple="true"
                :close-on-select="false"
                select-label="Appuyer sur entrée pour choisir"
                selected-label="Sélectionné"
                deselect-label="Appuyer sur entrée pour enlever"
                track-by="id"
                label="title"
            > 
                <template #noResult>Aucun résultat</template>
                <template #noOptions>La liste est vide</template>
            </vue-multiselect>
        </div>
        <div class="mb-3">
            <label for="portals" class="fw-bold form-label">Par portails</label>
            <vue-multiselect 
                v-model="params.portals" 
                :options="portalStore.portals" 
                placeholder="Choisir" 
                :multiple="true"
                :close-on-select="false"
                select-label="Appuyer sur entrée pour choisir"
                selected-label="Sélectionné"
                deselect-label="Appuyer sur entrée pour enlever"
                track-by="id"
                label="title"
            > 
                <template #noResult>Aucun résultat</template>
                <template #noOptions>La liste est vide</template>
            </vue-multiselect>
        </div>
        <div class="mb-3">
            <label for="tags" class="fw-bold form-label">Par types</label>
            <vue-multiselect 
                v-model="params.tags" 
                :options="mediaStore.types" 
                placeholder="Choisir" 
                :multiple="true"
                :close-on-select="false"
                select-label="Appuyer sur entrée pour choisir"
                selected-label="Sélectionné"
                deselect-label="Appuyer sur entrée pour enlever"
                track-by="id"
                label="title"
            > 
                <template #noResult>Aucun résultat</template>
                <template #noOptions>La liste est vide</template>
            </vue-multiselect>
        </div>
        <button type="button" class="btn btn-sm btn-primary me-1" @click="reset">Tout afficher</button>
        <button type="button" class="btn btn-sm btn-primary" @click="search">Rechercher</button>
        <loading v-if="loading" />
    </form>
</template>

<script>
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';
import { useCategoryStore } from '@store/category.js';
import { usePortalStore } from '@store/portal.js';
import Loading from '@components/fragments/Loading.vue';
import VueMultiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
    name: 'SearchForm',

    emits: ['on-search'],

    components: {
        'loading': Loading,
        'vue-multiselect': VueMultiselect
    },

    data() {
        return {
            params: {
                query: null,
                categories: [],
                portals: [],
                tags: []
            },
            loading: false
        }
    },

    computed: {
        ...mapStores(useMediaStore, useCategoryStore, usePortalStore),
    },

    async mounted () {
        this.loading = true;
        await this.mediaStore.getMediaTypes();
        await this.portalStore.getPortals();
        await this.categoryStore.getCategories();
        this.loading = false;
    },

    methods: {
        reset() {
            this.params.query = null;
            this.params.categories = [];
            this.params.portals = [];
            this.params.tags = [];

            this.search();
        },

        search() {
            this.$emit('on-search', this.params);
        }
    },
}
</script>