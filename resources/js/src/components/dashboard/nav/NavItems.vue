<template>
    <div>
        <ul v-for="menuItem in menu" :key="menuItem.group.id" class="flex space-x-4">
            <li v-if="menuItem.group.shown">
                <!--                <a href="#" class="nav-item">-->
                <!--                    <NavItemIcon :icon="menu.icon"/>-->
                <!--                    <span><NavItemName :groupName="menu.groupName"/></span>-->
                <!--                </a>-->

                <div
                    :class="[
                        'nav-item',
                        menuItem.group.isActive
                            ? 'nav-item-hover nav-item-text-active'
                            : 'nav-item-text',
                    ]"
                    :data-group-id="menuItem.group.id"
                    @click.stop="selectMenu(menuItem.group.id, $event)"
                >
                    <NavItemIcon :icon="menuItem.group.icon" />
                    <span><NavItemName :groupName="menuItem.group.name" /></span>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import NavItemIcon from '@/components/dashboard/nav/NavItemIcon.vue'
import NavItemName from '@/components/dashboard/nav/NavItemName.vue'
import { useRouter } from 'vue-router'

const props = defineProps({
    menu: {
        type: Array,
        default: () => [],
        required: true,
    },
})

const emit = defineEmits(['selectMenu'])

// Возвращаем id группы
const router = useRouter()
const selectMenu = (groupId, e) => {
    emit('selectMenu', groupId)

    // перекидываем в случае, если менюшка активна
    const menuItem = props.menu.find((item) => item.group.id === groupId)

    if (menuItem.group.isActive) {
        router.push({
            name: 'menu',
            params: {
                groupId: groupId,
            },
        })
    }
}

// console.log(props.menu[0])
</script>

<style scoped>
.nav-item {
    @apply flex
    items-center
    px-4 py-1
    ml-1
    border rounded-xl border-slate-100;
    width: calc(var(--sidebar-width) - 10px);
    height: 100%;
}

.nav-item-hover {
    @apply hover:bg-slate-300
    hover:text-slate-800
    hover:border-slate-500
    cursor-pointer;
}

.nav-item-text {
    @apply text-slate-400;
}

.nav-item-text-active {
    @apply text-slate-500;
}
</style>
