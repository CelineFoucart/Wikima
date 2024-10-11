<template>
    <form method="get">
        <div class="mb-3">
            <label for="query" class="fw-bold form-label">Par mot clé</label>
            <input type="text" id="query" v-model="params.query" placeholder="Recherche..." class="form-control search-input form-control">
        </div>
        <div class="mb-3">
            <label for="categories" class="fw-bold form-label">Par catégorie</label>
            <select id="categories" v-model="params.categories" class="form-control"></select>
        </div>
        <div class="mb-3">
            <label for="portals" class="fw-bold form-label">Par portails</label>
            <select id="portals" v-model="params.portals" class="form-control"></select>
        </div>
        <div class="mb-3">
            <label for="tags" class="fw-bold form-label">Par types</label>
            <select id="tags" v-model="params.tags" class="form-control"></select>
        </div>
        <button type="button" class="btn btn-sm btn-primary me-1" @click="reset">Tout afficher</button>
        <button type="button" class="btn btn-sm btn-primary" @click="search">Rechercher</button>
    </form>
</template>

<script>
export default {
    name: 'SearchForm',

    emits: ['on-search'],

    data() {
        return {
            params: {
                query: null,
                categories: [],
                portals: [],
                tags: []
            }
        }
    },

    methods: {
        reset() {
            this.params.query = null;
            this.params.categories = [];
            this.params.portals = [];
            this.params.tags = [];
        },

        search() {
            this.$emit('on-search', this.params);
        }
    },
}
</script>