<template>
    <div class="loading">
        <i class="fa-solid fa-spinner fa-spin fa-7x text-primary"></i>
    </div>
</template>

<script>
    export default {
        name: 'Loading'
    }
</script>

<style lang="css" scoped>
    .loading {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9000;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, .15);
        padding: 5px;
    }
</style>
