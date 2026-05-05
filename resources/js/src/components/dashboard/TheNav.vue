<template>
    <nav class="sidebar"
         @mouseenter="expandSidebar = true"
         @mouseleave="expandSidebar = false"
    >
        <aside class="sidebar-menu">
            <NavItems :menu="menu"/>
        </aside>
    </nav>
</template>

<script lang="ts" setup>
import { watch, ref} from 'vue'
import { useMenuStore } from '@/stores/MenuStore.ts'
import { storeToRefs } from 'pinia'

import NavItems from '@/components/dashboard/nav/NavItems.vue'


const menuStore = useMenuStore()
const menu      = menuStore.menu

const { expandSidebar, sidebarWidthExpanded, sidebarWidthCollapsed } = storeToRefs(menuStore)

const sidebarWidth_ = ref('50px')

watch(() => expandSidebar.value, () => {
    sidebarWidth_.value = expandSidebar.value ? sidebarWidthExpanded.value : sidebarWidthCollapsed.value
    document.documentElement.style.setProperty('--sidebar-width', sidebarWidth_)
}, {deep: true})

</script>

<style scoped>
.sidebar {
   /* @apply transition-all duration-500;*/
    --sidebar-width: v-bind(sidebarWidth_);
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
    /*transition: width 0.3s ease; !* Добавь плавности для красоты *!*/
}

.sidebar-menu {
    @apply mt-1.5;
}
</style>
