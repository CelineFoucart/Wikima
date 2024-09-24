import { defineStore } from 'pinia';
import axios from 'axios';

export const useTimelineStore = defineStore('timeline', {
    state: () => ({ 
        timeline: null
    }),

    actions: {
        async getTimeline(id) {
            try {
                const url = Routing.generate("api_timeline_show", {id: id});
                const response = await axios.get(url);
                this.timeline = response.data;
                return true;
            } catch (error) {
                console.log(error)
                return false;
            }
        },
    }
})