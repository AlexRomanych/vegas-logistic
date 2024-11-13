<template>
    <Suspense>
        <main class="content">
            <router-view></router-view>
        </main>
    </Suspense>
    <!--    <main v-if="isAuthenticated" class="content">-->
    <!--        <router-view></router-view>-->
    <!--    </main>-->
</template>


<script setup>
import {computed, reactive} from 'vue'
import {useRoute, useRouter} from 'vue-router'
//
//
const router = useRouter()
const route = useRoute()
//
const name = computed(() => route.name)
const name_ = computed(() => router.currentRoute.value.name)

// const rt = reactive(router)
// console.log('rt', rt.currentRoute)


// console.log(router.currentRoute.value)
// const name = route.name


// console.log(name.value)
// console.log(name_.value)
// console.log(route)


// const router = useRouter()
// console.log(router)
// const isAuth = router.currentRoute.value
// console.log(isAuth)

// const props = defineProps({
//     isAuthenticated: {
//         type: Boolean,
//         required: true,
//         default: false
//     },
//     source: {
//         type: String,
//         required: true
//     }
// })

// console.log(props.isAuthenticated)

// console.log(props.source)
//
// const obj = {
//     component: () => import('@/src/components/dashboard/TheDashboard.vue')
// }
//
// console.log(obj.component())
//
//
// const dashComponent = async () => import('@/src/components/dashboard/TheDashboard.vue')
// await data = dashComponent()
//
// console.log(dashComponent())
// console.log(data)

</script>


<style scoped>
.content {
    flex: 1;
    overflow-y: auto;
    margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);
    /*   position: relative; */
}
</style>
