<template>
    <Suspense>

        <main :class="$route.name === 'login' || $route.name === 'register' || $route.name === 'error.404' ? 'container-auth' : 'container'">
            <div :class="$route.name === 'login' || $route.name === 'register' || $route.name === 'error.404' ? '' : 'content'">
                <router-view :key="$route.fullPath"></router-view>
                <!--            {{$route.fullPath}}-->
<!--                <span>Name__ {{$route.name}}</span>-->
            </div>
        </main>
    </Suspense>
    <!--    <main v-if="isAuthenticated" class="content">-->
    <!--        <router-view></router-view>-->
    <!--    </main>-->
</template>


<script setup>
import {computed, reactive, unref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
//
//
const router = useRouter()
const route = useRoute()
//
const name = computed(() => route.name)
const name_ = computed(() => router.currentRoute.value.name)

const props = defineProps({
    auth: {
        type: Boolean,
        required: false,
        default: false
    },
})


// const route = useRoute()
// console.log(router.currentRoute.value)
// console.log(unref(router).currentRoute.value    )
// console.log(JSON.stringify(router.currentRoute))
// console.log(router.currentRoute.name)

// const a = unref(router.currentRoute.value)
// console.log('a', a)


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
.container {
    flex: 1;
    position: relative;
    //overflow-x: hidden;
/*
    // min-width: 100vw; // или ширина в пикселях
    // width: auto;
    //
    // overflow-y: auto;
    // overflow-x: auto;
    // overflow-y: auto; //position: relative;
    // margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width); //margin: 0 0 var(--footer-height) var(--sidebar-width); //margin-top: var(--header-height);
    // position: relative;
 */

}

.container-auth {
    flex: 1;
    /*
    //overflow-y: auto;
    //overflow-x: auto;
    //margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);
    */
}


.content {
    position: absolute;
    min-width: calc(100vw - var(--sidebar-width)); /* или ширина в пикселях *
    /* //width: calc(100% - var(--sidebar-width)); // Если раскомментируем - получим фиксированную ширину страницы */
    height: calc(100% - var(--header-height) - var(--footer-height));

    //margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);

    top: var(--header-height);
    left: var(--sidebar-width);
    overflow-y: auto;
    overflow-x: auto;

    //overflow-x: auto;

    /*
    //min-width: 100vw; // или ширина в пикселях
    //width: auto;
    */
}

</style>
