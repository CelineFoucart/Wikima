import { defineStore } from "pinia";
import axios from 'axios';

export const useCategoryStore = defineStore('category', {
    state: () => ({
        categories: [],
    }),

    actions: {
        async getCategories() {
            try {
                const url = Routing.generate("api_category_index");
                let response = await axios.get(url);
                this.categories = response.data;
                return true;
            } catch (error) {
                this.categories = [];
                return false;
            }
        },

    }
});