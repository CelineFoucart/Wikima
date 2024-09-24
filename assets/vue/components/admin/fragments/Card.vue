<template>
    <section class="card border-top-primary">
        <header class="card-header bg-white">
            <div class="float-end" v-if="$slots.actions">
                <slot name="actions"></slot>
            </div>
            <h2 class="card-title h5 mb-0">
                <slot name="title"></slot>
            </h2>
        </header>
        <div class="card-body" :class="bodyClass">
            <slot name="content"></slot>
        </div>
    </section>
</template>

<script>
    export default {
        name: 'Card',

        props: {
            bodyClass: {
                type: String,
                default: '' 
            },
        },
    }
</script>

<style lang="scss" scoped>

</style>