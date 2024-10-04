<template>
    <section class="card bg-light">
        <div class="card-body">
            <div class="float-end">
                <a href="#" class="btn btn-success btn-sm me-1">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger d-inline" @click="openDelete = true">
                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                </button>
            </div>
            <h4 class="h6">{{ event.title }}</h4>
            <p>{{ event.duration }}</p>
            <div v-if="event.presentation !== null">{{ truncate(event.presentation) }}</div>
        </div>
    </section>
    <delete-modal v-if="openDelete" :loading="loading" :title="event.title" @on-confirm="deleteEvent" @on-close="openDelete = false" />
</template>

<script>
import DeleteModal from '@components/fragments/DeleteModal.vue';
import EventModal from '@components/admin/timeline/EventModal.vue';

export default {
    name: 'EventCard',

    components: {
        'delete-modal': DeleteModal,
        'event-modal': EventModal
    },

    props: {
        event: Object,
    },

    data() {
        return {
            openEdit: false,
            openDelete: false,
            loading: false
        }
    },

    methods: {
        truncate(text, limit=190) {
            if (limit <= 0 || text.length <= limit) {
                return text;
            }

            text = text.substring(0, limit);
            const index = text.indexOf(" ", limit);
            text = index === -1 ? text : text.substring(0, index);

            return text + '...';
        },

        async deleteEvent() {
            this.loading = true;
            this.loading = false;
        }
    },
}
</script>