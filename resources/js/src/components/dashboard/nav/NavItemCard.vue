<template>
    <div v-if="groupMenuItem.shown" class="m-1.5 p-1.5">
        <router-link
            :to="{name: link}"
            :class="['cursor-default', groupMenuItem.isActive ? '' : 'pointer-events-none']"

        >
            <div :class="['menu-item-card', groupMenuItem.isActive ? 'menu-item-card-hover menu-item-card-text-active' : 'menu-item-card-text ']">
                {{ groupMenuItem.name }}
            </div>
        </router-link>
    </div>

</template>

<script setup>

const props = defineProps({
    groupMenuItem: {
        type: Object,
        required: true,
    },
})

const link = props.groupMenuItem.path ? props.groupMenuItem.path : 'error.404'
// console.log(link)

</script>

<style scoped>
.menu-item-card {
    @apply
        w-[300px] h-[100px]
        m-2 p-2
        flex flex-col items-center justify-center
        border-2 rounded-lg border-slate-500
        bg-slate-200
        text-lg font-semibold
        shadow-xl shadow-slate-500
}

.menu-item-card-hover {
    @apply hover:cursor-pointer hover:bg-slate-300 hover:text-slate-800
}

.menu-item-card-text-active {
    @apply text-slate-500
}

.menu-item-card-text {
    @apply text-slate-400
    pointer-events-none
}

</style>
