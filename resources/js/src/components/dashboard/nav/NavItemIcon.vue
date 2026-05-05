<template>
    <component
        :is="componentInstance"
        class="nav-item-icon"
    />
</template>

<script lang="ts" setup>
import { computed } from 'vue'

// Todo Переделать на динамический импорт
import { UserGroupIcon } from '@heroicons/vue/24/solid'
import { BookOpenIcon } from '@heroicons/vue/24/solid'
import { HomeModernIcon } from '@heroicons/vue/24/solid'
import { ShoppingCartIcon } from '@heroicons/vue/24/solid'
import { BuildingStorefrontIcon } from '@heroicons/vue/24/solid'
import { UsersIcon } from '@heroicons/vue/24/solid'
import { ClipboardDocumentIcon } from '@heroicons/vue/24/solid'
import { Cog8ToothIcon } from '@heroicons/vue/24/solid'
import { WrenchScrewdriverIcon } from '@heroicons/vue/24/solid'
import { UserPlusIcon } from '@heroicons/vue/24/solid'
import { PuzzlePieceIcon } from '@heroicons/vue/24/solid'

interface IProps {
    icon: string
}

const props = defineProps<IProps>()

const icons = {
    BookOpenIcon,
    UserGroupIcon,
    HomeModernIcon,
    ShoppingCartIcon,
    BuildingStorefrontIcon,
    UsersIcon,
    ClipboardDocumentIcon,
    Cog8ToothIcon,
    WrenchScrewdriverIcon,
    UserPlusIcon,
    PuzzlePieceIcon
}

const componentInstance = computed(() => icons[props.icon])

</script>

<style scoped>
.nav-item-icon {
    @apply size-6 m-1.5
}
</style>
