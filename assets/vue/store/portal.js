import { defineStore } from "pinia";
import axios from 'axios';

export const usePortalStore = defineStore('portal', {
    state: () => ({
        portals: [],
    }),

    actions: {
        async getPortals() {
            try {
                const url = Routing.generate("api_portal_index");
                let response = await axios.get(url);
                this.portals = response.data;
                return true;
            } catch (error) {
                this.portals = [];
                return false;
            }
        },

    }
});