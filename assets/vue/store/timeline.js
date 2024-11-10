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
                return false;
            }
        },

        async sortTimelineEvent(eventId, position) {
            try {
                const url = Routing.generate("api_timeline_event_position", {id: this.timeline.id, eventId: eventId});
                const response = await axios.put(url, { position: position });
                this.timeline = response.data;
                return true;
            } catch (error) {
                return false;
            }
        },

        async addEvent(data) {
            try {
                const url = Routing.generate("api_timeline_event_add", {id: this.timeline.id});
                const response = await axios.post(url, data);
                this.timeline = response.data;
                return true;
            } catch (error) {
                return false;
            }
        },

        async editEvent(event, data) {
            try {
                const url = Routing.generate("api_timeline_event_edit", {id: this.timeline.id, eventId: event.id});
                const response = await axios.put(url, data);
                this.timeline = response.data;
                return true;
            } catch (error) {
                return false;
            }
        },

        async removeEvent(event) {
            try {
                const url = Routing.generate("api_timeline_event_delete", {id: this.timeline.id, eventId: event.id});
                const response = await axios.delete(url);
                this.timeline = response.data;
                return true;
            } catch (error) {
                return false;
            }
        }
    }
})