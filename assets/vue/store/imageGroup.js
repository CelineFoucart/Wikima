import { defineStore } from "pinia";
import axios from 'axios';

export const useImageGroupStore = defineStore('imageGroup', {
    state: () => ({
        imageGroups: [],
    }),

    actions: {
        async getImageGroups() {
            try {
                const url = Routing.generate("api_image_group_index");
                let response = await axios.get(url);
                this.imageGroups = response.data;
                return true;
            } catch (error) {
                this.imageGroups = [];
                return false;
            }
        },

    }
});