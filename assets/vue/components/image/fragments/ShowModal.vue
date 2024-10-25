<template>
    <modal title="Image" dialogClass="modal-lg" contentClass="border-top-primary" @on-close="$emit('on-close')">
        <template #body>
            <table class="table table-striped mb-0">
                <tbody>
                    <tr>
                        <th>Titre</th>
                        <td>
                            <a :href="publicShowRoute">
                                {{ image.title }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td>{{ image.slug }}</td>
                    </tr>
                    <tr>
                        <th>Mots clés</th>
                        <td>{{ image.keywords }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td class="nl2br">{{ image.description }}</td>
                    </tr>
                    <tr>
                        <th>Types</th>
                        <td>
                            <span class="badge text-bg-secondary me-1" v-for="tag in image.tags" :key="tag.id">
                                {{ tag.title }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Catégories</th>
                        <td>
                            <a :href="categoryRoute(category)" class="badge text-bg-secondary me-1 text-decoration-underline" v-for="category in image.categories" :key="category.id">
                                {{ category.title }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Portails</th>
                        <td>
                            <a :href="portalRoute(portal)" class="badge text-bg-secondary text-decoration-underline me-1" v-for="portal in image.portals" :key="portal.id">
                                {{ portal.title }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Cartes</th>
                        <td>
                            <ul class="mb-0" v-if="image.maps.length > 0">
                                <li v-for="map in image.maps" :key="map.id">
                                    <a :href="mapRoute(map)">{{ map.title }}</a>
                                </li>
                            </ul>
                            <span class="fst-italic" v-else>Aucune carte</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Mise à jour</th>
                        <td>{{ image.updatedAt ? formatDatetime(image.updatedAt) : '' }}</td>
                    </tr>
                    <tr>
                        <th>Fichier</th>
                        <td><img :src="thumbRoute" :alt="image.title" class="img-fluid"></td>
                    </tr>
                </tbody>
            </table>
        </template>
    </modal>
</template>

<script>
import BootstrapModal from '@components/fragments/BootstrapModal.vue';
import { convertDateFormatToJsFormat } from '@functions/dateHelper.js';
import dayjs from 'dayjs';
import 'dayjs/locale/fr';

dayjs.locale('fr');

export default {
    name: 'ShowModal',

    components: {
        'modal': BootstrapModal,
    },

    props: {
        image: Object,
        dateFormat: String
    },

    computed: {
        publicShowRoute() {
            return Routing.generate('app_image_show', {slug: this.image.slug});
        },

        thumbRoute() {
            return Routing.generate('file_medium', {id: this.image.id});
        },
    },

    methods: {
        categoryRoute(category) {
            return Routing.generate('app_category_show', {slug: category.slug})
        },

        portalRoute(portal) {
            return Routing.generate('app_portal_show', {slug: portal.slug})
        },

        mapRoute(map) {
            return Routing.generate('app_map_show', {id: map.id})
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