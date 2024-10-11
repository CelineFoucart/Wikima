<template>
    <article class="card border-top-primary">
        <header class="card-header">
            <h2 class="card-title h5 mb-0">Galerie</h2>
        </header>
        <div class="card-body">
            <media-container :withEntity="withEntity" @on-append-media="appendMedias"></media-container>
        </div>
    </article>
</template>

<script>
import MediaContainer from '@components/image/MediaContainer.vue';

export default {
    name: 'ImageIndex',

    components: {
        "media-container": MediaContainer,
    },

    props: {
        withEntity: {
            type: Boolean,
            default: false
        },
    },

    methods: {
        appendMedias(payload) {
            this.$emit('on-append-media', payload);
        },
    },
}
</script>