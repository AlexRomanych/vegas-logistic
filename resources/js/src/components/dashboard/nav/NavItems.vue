<template>
    <div
        @mouseenter="expandSidebar = true"
        @mouseleave="expandSidebar = false"
    >
        <ul v-for="menuItem in menu" :key="menuItem.group.id" class="flex space-x-4">

            <li v-if="menuItem.group.shown"
                @click="selectMenu(menuItem.group.id)">

                <div :class="['nav-item', menuItem.group.isActive ? 'nav-item-hover nav-item-text-active' : 'nav-item-text']">
                    <NavItemIcon
                        :icon="menuItem.group.icon"
                        class="flex-shrink-0"
                    />

                    <template v-if="expandSidebar">
                        <NavItemName
                            :groupName="menuItem.group.name"
                        />
                    </template>
                </div>
            </li>

        </ul>
    </div>
</template>

<script lang="ts" setup>
import { useRouter } from 'vue-router'
import { useMenuStore } from '@/stores/MenuStore.ts'
import { storeToRefs } from 'pinia'

import NavItemIcon from '@/components/dashboard/nav/NavItemIcon.vue'
import NavItemName from '@/components/dashboard/nav/NavItemName.vue'
import { ref, watch } from 'vue'

interface IProps {
    menu?: IMenu,
}

const props = withDefaults(defineProps<IProps>(), {
    menu: () => [],
})

const menuStore                                                      = useMenuStore()
const { expandSidebar, sidebarWidthExpanded, sidebarWidthCollapsed } = storeToRefs(menuStore)

const sidebarWidth_ = ref('50px')

watch(() => expandSidebar.value, () => {
    sidebarWidth_.value = expandSidebar.value ? sidebarWidthExpanded.value : sidebarWidthCollapsed.value
    document.documentElement.style.setProperty('--sidebar-width', sidebarWidth_)
}, { deep: true })

// const emits = defineEmits<{
//     (e: 'selectMenu', groupId: number): void,
// }>()

// Возвращаем id группы
const router     = useRouter()
const selectMenu = async (groupId, /*e*/) => {
    // emits('selectMenu', groupId)

    // перекидываем в случае, если менюшка активна
    const menuItem = props.menu.find((item) => item.group.id === groupId)

    if (menuItem.group.isActive) {
        await router.push({ name: 'menu', params: { groupId: groupId, } })
    }
}


</script>

<style scoped>

.nav-item {
    @apply
    flex items-center justify-start gap-y-3
    ml-0.5 px-1 py-1 /* уменьшил padding, чтобы иконка не зажималась при 50px */
    cursor-pointer
    border rounded-xl border-transparent
    transition-all duration-500;  /* плавный переход */

    /* Используем глобальную переменную. -10px если нужен внутренний отступ от краев сайдбара */
    width: calc(var(--sidebar-width) - 8px);
    overflow: hidden; /* чтобы текст не вылезал за границы при узком баре */
}

.nav-item-hover {
    @apply
    hover:bg-slate-300
    hover:text-slate-800
    hover:border-slate-500;
}

.nav-item-text {
    @apply text-slate-400;
}

.nav-item-text-active {
    @apply text-slate-500;
}
</style>
