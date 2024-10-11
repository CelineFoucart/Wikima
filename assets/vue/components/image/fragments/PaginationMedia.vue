<template>
    <div class="row border-top pt-2 m-0 align-items-center">
        <div class="d-flex gap-1 justify-content-end">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <label for="length">Par page</label>
                <select id="length" class="custom-select custom-select-sm form-control w-auto  h-100" v-model="currentLimit">
                    <option :value="limit" v-for="limit in limits">{{limit}}</option>
                </select>
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0 justify-content-end">
                    <li class="page-item" :class="{'disabled': currentPage === 1 || emptyPagination}">
                        <a class="page-link" href="#" aria-label="Précédent" @click.prevent="previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item" :class="{'active': pagination.first === currentPage}" v-if="needFirstPage">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.first)">{{pagination.first}}</a>
                    </li>
                    <li class="page-item disabled" v-if="needFirstPage">
                        <span class="page-link">…</span>
                    </li>
                    <template v-for="page in pagesInRange" :key="page">
                        <li class="page-item" :class="{'active': page === currentPage}" v-if="page > 0 && emptyPagination === false">
                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{page}}</a>
                        </li>
                    </template>
                    <li class="page-item disabled" v-if="needLastPage">
                        <span class="page-link">…</span>
                    </li>
                    <li class="page-item" :class="{'active': pagination.last === currentPage}" v-if="needLastPage">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.last)">{{pagination.last}}</a>
                    </li>
                    <li class="page-item" :class="{'disabled': last === currentPage}">
                        <a class="page-link" href="#" aria-label="Suivant" @click.prevent="next"><span aria-hidden="true">&raquo;</span></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PaginationMedia',

    props: {
        pagination: {
            type: Object,
            default: null
        },
    },

    data() {
        return {
            limits: [10, 15, 25, 50, 100],
            currentPage: 1,
            currentLimit: 10
        }
    },

    mounted () {
        this.currentLimit = this.pagination.numItemsPerPage;
    },

    watch: {
        currentLimit() {
            this.currentPage = 1;
            this.$emit('on-change', {limit: this.currentLimit, page: this.currentPage});
        },

        pagination: {
            deep: true,
            handler(val) {
                this.currentPage = this.pagination.current;
            },
        },
    },

    computed: {        
        totalCount() {
            if (this.pagination === null) {
                return 0;
            }

            return this.pagination.totalCount;
        },

        pagesInRange() {
            if (this.pagination === null) {
                return [];
            }

            return this.pagination.pagesInRange;
        },

        last() {
            if (this.pagination === null) {
                return 0;
            }

            return 0;
        },

        emptyPagination() {
            return this.pagesInRange.includes(0);
        },

        needLastPage() {
            if (this.emptyPagination || this.pagesInRange.length === 0) {
                return false;
            }

            return this.pagesInRange[this.pagesInRange.length-1] !== this.pagination.last;
        },

        needFirstPage() {
            if (this.emptyPagination || this.pagesInRange.length === 0) {
                return false;
            }
            return this.pagesInRange[0] !== this.pagination.first;
        }
    },

    methods: {
        changePage(newPage) {
            this.currentPage = newPage;
            this.$emit('on-change', {limit: this.currentLimit, page: this.currentPage});
        },

        previous() {
            if (this.currentPage === 1) {
                return;
            }

            this.currentPage--;
            this.$emit('on-change', {limit: this.currentLimit, page: this.currentPage});
        },

        next() {
            if (this.currentPage === this.pagination.last) {
                return;
            }

            this.currentPage++;
            this.$emit('on-change', {limit: this.currentLimit, page: this.currentPage});
        }
    },
}
</script>

<style lang="css" scoped>
label {
    font-weight: normal !important;
    margin-bottom: 0;
}
</style>