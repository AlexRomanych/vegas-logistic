<template>
    <nav class="sidebar">
        <aside class="sidebar-menu">
            <NavItems :menu="menu" />
        </aside>
    </nav>
</template>

<script>
import { useMenuStore } from '@/stores/MenuStore.js'
import NavItems from '@/components/dashboard/nav/NavItems.vue'

export default {
    components: { NavItems },
    setup() {
        const menuStore = useMenuStore()
        const menu = menuStore.menu

        // console.log(menu)

        return {
            menu,
        }
    },
}
</script>

<style scoped>
.sidebar {
    border-right: 2px solid var(--main-border-color);
    flex: 0 0 var(--sidebar-width); /* фиксированная ширина */
    background-color: var(--main-bg-color);
    position: fixed;
    top: 0;
    width: var(--sidebar-width);
    /*   min-height: 100vh; */
    height: calc(100vh - var(--footer-height) - var(--header-height));
    margin: var(--header-height) 0 var(--footer-height) 0;
    z-index: 100;
}

.sidebar-menu {
    @apply mt-1.5;
}
</style>
