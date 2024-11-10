import { defineStore } from "pinia";
import axios from 'axios';

export const useEntityGalleryStore = defineStore('entityGallery', {
    state: () => ({ medias: [] }),

    actions: {
        pushMedia(media) {
            this.medias.push(media);
        },

        async getMedias(type, entity) {
            try {
                const url = Routing.generate(`api_${type}_gallery`, { id: entity });
                let response = await axios.get(url);
                this.medias = response.data;
                return true;
            } catch (error) {
                console.log(error)
                this.medias = [];
                return false;
            }
        },

        async appendMedia(type, entity, media) {
            try {
                const url = Routing.generate(`api_${type}_gallery_append`, { id: entity, mediaId: media.id });
                await axios.post(url, { media: media });
                this.pushMedia(media);
                return true;
            } catch (error) {
                return false;
            }
        },

        async removeMedia(type, entity, media) {
            try {
                const url = Routing.generate(`api_${type}_gallery_remove`, { id: entity, mediaId: media.id });
                await axios.delete(url);

                const index = this.medias.findIndex((element) => element.id === media.id);
                if (index !== -1) {
                    this.medias.splice(index, 1);
                }

                return true;
            } catch (error) {
                return false;
            }
        }
    }
});