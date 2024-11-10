import { defineStore } from "pinia";
import axios from 'axios';

export const useMediaStore = defineStore('media', {
    state: () => ({
        medias: [],
        pagination: { current: 1, numItemsPerPage: 10, totalCount: 0, firstItemNumber: 0, pagesInRange: [], lastItemNumber: 0 },
        types: [],
        lastInserted: null
    }),

    actions: {
        async getMedias(page = 1, limit = 10, filters) {
            try {
                const params = {
                    'page': page,
                    'length': limit,
                    'portals[]': filters.portals,
                    'tags[]': filters.tags,
                    'categories[]': filters.categories,
                    'query': filters.query
                };


                const url = Routing.generate("api_image_index", params);
                let response = await axios.get(url);
                this.medias = response.data.medias;
                this.pagination = response.data.pagination;
                return true;
            } catch (error) {
                this.medias = [];
                this.pagination = [];
                return false;
            }
        },

        async getMediaTypes() {
            try {
                let response = await axios.get(Routing.generate("api_image_type_index"));
                this.types = response.data;
                return true;
            } catch (error) {
                this.types = [];
                return false;
            }
        },

        async postMedia(media) {
            try {
                const formData = new FormData();
                formData.append("title", media.title);
                formData.append("slug", media.slug);
                formData.append("keywords", media.keywords);
                formData.append("description", media.description);
                formData.append("imageFile", media.imageFile);

                media.categories.forEach(category => {
                    formData.append("categories[]", category.id);
                });
                
                media.portals.forEach(portal => {
                    formData.append("portals[]", portal.id);
                });
                
                media.tags.forEach(tag => {
                    formData.append("tags[]", tag.id);
                });
                
                media.imageGroups.forEach(imageGroup => {
                    formData.append("imageGroups[]", imageGroup.id);
                });

                const response = await axios.post(Routing.generate("api_image_create"), formData);
                this.lastInserted = response.data;
                return true;
            } catch (error) {
                return false;
            }
        },

        async putMedia(mediaId, params) {
            try {
                const response = await axios.put(Routing.generate("api_image_edit", { id: mediaId }), params);

                let index = this.medias.findIndex((item) => item.id == mediaId);
                if (index !== -1) {
                    this.medias[index] = response.data;
                }

                return true;
            } catch (error) {
                return false;
            }
        },

        async deleteMedia(mediaId) {
            try {
                await axios.delete(Routing.generate("api_image_delete", { id: mediaId }));
                return true;
            } catch (error) {
                return false;
            }
        },
    }
});