<template>
    <div class="row g-3" v-if="timelineStore.timeline">
        <div class="col-md-9">
            <card bodyClass="card-show">
                <template #title>Informations</template>
                <template #content>
                    <table class="table table-striped mb-0">
                        <tbody>
                            <tr>
                                <th>Titre</th>
                                <td>
                                    <a :href="path('app_timeline_show', {slug: timelineStore.timeline.slug})">
                                        {{ timelineStore.timeline.title }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td class="nl2br">{{ timelineStore.timeline.description }}</td>
                            </tr>
                            <tr>
                                <th>Précédent</th>
                                <td>
                                    <span v-if="timelineStore.timeline.previous">
                                        <a :href="path('admin_app_timeline_show', {id: timelineStore.timeline.previous.id})">
                                            {{ timelineStore.timeline.previous.title }}
                                        </a>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Suivant</th>
                                <td>
                                    <span v-if="timelineStore.timeline.next">
                                        <a :href="path('admin_app_timeline_show', {id: timelineStore.timeline.next.id})">
                                            {{ timelineStore.timeline.next.title }}
                                        </a>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Catégories</th>
                                <td>
                                    <ul class="mb-0" v-if="timelineStore.timeline.categories.length > 0">
                                        <li v-for="category in timelineStore.timeline.categories" :key="category.title">
                                            <a :href="path('app_category_show', {slug: category.slug})">{{ category.title }}</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Portails</th>
                                <td>
                                    <ul class="mb-0" v-if="timelineStore.timeline.portals.length > 0">
                                        <li v-for="portal in timelineStore.timeline.portals" :key="portal.title">
                                            <a :href="path('app_portal_show', {slug: portal.slug})">{{ portal.title }}</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </card>
        </div>
        <div class="col-md-3">
            <card bodyClass="card-show">
                <template #title>Méta données</template>
                <template #content>
                    <table class="table table-striped mb-0">
                        <tbody>
                            <tr>
                                <th>Slug</th>
                                <td>{{ timelineStore.timeline.slug }}</td>
                            </tr>
                            <tr>
                                <th>Création</th>
                                <td>{{ formatDatetime(timelineStore.timeline.createdAt) }}</td>
                            </tr>
                            <tr>
                                <th>Mise à jour</th>
                                <td>{{ formatDatetime(timelineStore.timeline.updatedAt) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </card>
        </div>
        <div class="col-md-12">
            <card>
                <template #actions>
                    <button htype="button" class="btn btn-success btn-sm" @click="openAddModal = true">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                    </button>
                    <event-modal v-if="openAddModal === true" @on-close="openAddModal = false" />
                </template>
                <template #title>Evénements</template>
                <template #content>
                    <div class="row g-3">
                        <div class="col-md-4" v-for="event in timelineStore.timeline.events" :key="event.id">
                            <event-card :event="event" />
                        </div>
                    </div>
                </template>
            </card>
        </div>
    </div>
    <loading v-if="loading" />
</template>

<script>
import { mapStores } from "pinia";
import { useTimelineStore } from '@store/timeline.js';
import { createToastify } from '@functions/toastify.js';
import { convertDateFormatToJsFormat } from '@functions/dateHelper.js';
import Card from '@components/admin/fragments/Card.vue';
import EventCard from '@components/admin/timeline/EventCard.vue';
import EventModal from '@components/admin/timeline/EventModal.vue';
import Loading from '@components/fragments/Loading.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/fr';

dayjs.locale('fr');

export default {
    name: 'TimelineShow',

    components: {
        'card': Card,
        'event-card': EventCard,
        'loading': Loading,
        'event-modal': EventModal
    },

    props: {
        id: Number,
        dateFormat: String
    },

    data() {
        return {
            loading: false,
            openAddModal: false
        }
    },

    computed: {
        ...mapStores(useTimelineStore),
    },

    async mounted () {
        this.loading = true;
        const status = await this.timelineStore.getTimeline(this.id);
        if (!status) {
            createToastify("Cet élément n'existe pas.", 'error');
        } 
        this.loading = false;
    },

    methods: {
        path(route, params) {
            return Routing.generate(route, params);
        },

        formatDatetime(datetime) {
            if (!datetime) {
                return '';
            }

            return dayjs(datetime).format(convertDateFormatToJsFormat(this.dateFormat));
        },

        
    },
}
</script>

<style scoped>
.nl2br {
    white-space: pre-wrap
}
</style>