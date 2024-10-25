<template>
    <div class="image-container mb-2 align-items-start">
        <media-card :media="media" v-for="media in mediaStore.medias" :key="media.id"></media-card>
    </div>
</template>

<script>
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';
import MediaCard from '@components/image/fragments/MediaCard.vue';

export default {
    name: 'MediaList',

    components: {
        'media-card': MediaCard,
    },

    computed: {
        ...mapStores(useMediaStore),
    },
}
</script>

<style lang="css" scoped>
.image-container {
    min-height: 375px;
}
</style>