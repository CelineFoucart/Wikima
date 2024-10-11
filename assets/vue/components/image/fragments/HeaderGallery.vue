<template>
    <header class="bg-light border rounded p-2">
        <div class="row align-items-center">
            <div class="col-8">
                <h3 class="h6 mb-0">
                    {{ mediaStore.pagination.firstItemNumber }} à {{  mediaStore.pagination.lastItemNumber }} sur
                    {{ mediaStore.pagination.totalCount }} image{{ mediaStore.pagination.totalCount > 1 ? 's' : '' }}
                </h3>
            </div>
            <div class="col-4 text-end">
                <a :href="uploadRoute" class="btn btn-success btn-sm">
                    <i class="fas fa-plus-circle fa-fw" aria-hidden="true"></i>
                    <span class="ps-1 d-none d-md-inline-block">Ajouter</span>
                </a>
            </div>
        </div>
    </header>
</template>

<script>
import { mapStores } from "pinia";
import { useMediaStore } from '@store/media.js';

export default {
    name: 'HeaderGallery',

    computed: {
        ...mapStores(useMediaStore),

        uploadRoute() {
            return Routing.generate('admin_app_image_create');
        }
    },
}
</script>