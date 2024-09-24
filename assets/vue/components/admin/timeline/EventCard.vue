<template>
    <section class="card bg-light">
        <div class="card-body">
            <div class="float-end">
                <a href="#" class="btn btn-success btn-sm me-1">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger d-inline" data-bs-toggle="modal" data-bs-target="#deleteModal57">
                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                </button>
            </div>
            <h4 class="h6">{{ event.title }}</h4>
            <p>{{ event.duration }}</p>
            <div v-if="event.presentation !== null">{{ truncate(event.presentation) }}</div>
        </div>
    </section>
</template>

<script>
    export default {
        name: 'EventCard',

        props: {
            event: Object,
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
            }
        },
    }
</script>