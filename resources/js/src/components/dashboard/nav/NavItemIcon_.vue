<template>
    <component :is="page" class="size-6 text-blue-500" />
    <div>{{ componentInstance }}</div>
    <!--    <UserGroupIcon />-->
</template>

<script setup>
// Todo Переделать на динамический импорт
import { UserGroupIcon } from '@heroicons/vue/24/solid'
import { BookOpenIcon } from '@heroicons/vue/24/solid'

import { computed, ref } from 'vue'

// const props = defineProps({
//     icon:
//         {
//             type: String,
//             required: true,
//             validator: (value) => {
//                 ['BookOpenIcon', 'UserGroupIcon'].includes(value)
//             },
//         },
// })

const props = defineProps(['icon'])

const page = ref('BookOpenIcon')
// const page = props.icon
// const page = ref(props.icon + '.vue')

const componentInstance = computed(() => {
    console.log(props.icon)
    return ref(props.icon)
})

// import { defineAsyncComponent } from 'vue'
/*
export default {
    props: {
        icon:
            {
                type: String,
                required: true,
            },
    },
    computed: {
        componentInstance() {
            console.log('icon', this.icon)
            // const a = import(`@heroicons/vue/24/solid/${this.icon}.js`)
            const b = defineAsyncComponent((sd) => import('./Dynamic.vue').then(data => console.log('data', data)))
            // console.log('a', a)
            console.log('b', b)

            // return () => import(`@heroicons/vue/24/solid/${this.icon}.js`)
            return () => import(`./Dynamic.vue`)
        },
        loadComponent() {

        }
    }
}
*/
</script>

<style scoped></style>
