<template>
    <div class="image-block">
        <a :href="showRoute" :title="media.title">
            <img :src="thumbRoute" :alt="media.title" class="thumbnail">
        </a>
    </div>
</template>

<script>
    export default {
        name: 'MediaCard',

        props: {
            media: Object,
        },

        computed: {
            showRoute() {
                return Routing.generate('file_show', {id: this.media.id});
            },

            thumbRoute() {
                return Routing.generate('file_thumb', {id: this.media.id});
            }
        },
    }
</script>

<style>
.thumbnail {
    width: 150px;
    height: 150px;
    object-fit: cover;
}
</style>