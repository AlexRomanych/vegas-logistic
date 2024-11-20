<template>
    <component :is="componentInstance" class="nav-item-icon"/>
</template>


<script>
// Todo Переделать на динамический импорт
import {UserGroupIcon} from '@heroicons/vue/24/solid'
import {BookOpenIcon} from '@heroicons/vue/24/solid'
import {HomeModernIcon} from '@heroicons/vue/24/solid'
import {ShoppingCartIcon} from '@heroicons/vue/24/solid'
import {BuildingStorefrontIcon} from '@heroicons/vue/24/solid'
import {UsersIcon} from '@heroicons/vue/24/solid'
import {ClipboardDocumentIcon} from '@heroicons/vue/24/solid'
import {Cog8ToothIcon} from '@heroicons/vue/24/solid'

export default {
    components: {
        BookOpenIcon,
        UserGroupIcon,
        HomeModernIcon,
        ShoppingCartIcon,
        BuildingStorefrontIcon,
        UsersIcon,
        ClipboardDocumentIcon,
        Cog8ToothIcon
    },
    props: {
        icon: {
            type: String,
            required: true,
            // validator: (value) => ['BookOpenIcon', 'UserGroupIcon'].includes(value)

        },
    },
    data() {
        return {
            page: this.icon,
        }
    },
    computed: {
        componentInstance() {
            return this.icon
            // return () => import(`@heroicons/vue/24/solid/${this.icon}`).then(data => data)
        }
    }

}
</script>

<style scoped>
.nav-item-icon {
    @apply size-6 text-slate-500 m-1.5
}
</style>
