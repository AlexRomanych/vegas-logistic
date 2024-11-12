<template>
    <main v-if="isAuthenticated" class="content">
        <router-view></router-view>
    </main>
</template>


<script setup>
    const props = defineProps({
        isAuthenticated: {
            type: Boolean,
            default: false
        }
    })

    console.log(props.isAuthenticated)

</script>


<style scoped>
.content {
    flex: 1;
    overflow-y: auto;
    margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);
    /*   position: relative; */
}
</style>
