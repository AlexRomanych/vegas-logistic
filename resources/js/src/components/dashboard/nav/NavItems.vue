<template>
    <div>
        <ul v-for="menu in menus" :key="menu.groupID" class="flex space-x-4">
            <li>
                <a href="#" class="nav-item">
                    <NavItemIcon :icon="menu.icon"/>
                    <span><NavItemName :groupName="menu.groupName"/></span>
                </a>




            </li>

        </ul>
    </div>
</template>


<script setup>


import NavItemIcon from "@/src/components/dashboard/nav/NavItemIcon.vue";
import NavItemName from "@/src/components/dashboard/nav/NavItemName.vue";

const defineProps = defineProps({
    menus: {
        type: Array,
        default: () => [],
        required: true,
    }
})

// console.log(defineProps)

</script>


<style scoped>
.nav-item {
    @apply
        flex
        items-center
        px-4 py-2
        text-gray-700
        hover:bg-gray-300
        hover:text-blue-500
        ml-1
        border
        rounded-xl
        border-gray-100;
    width: calc(var(--sidebar-width) - 10px);
    height: 100%;
}
</style>
