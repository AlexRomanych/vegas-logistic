<template>
    <Suspense>
        <main
            :class="
                $route.name === 'login' || $route.name === 'register' || $route.name === 'error.404'
                    ? 'container-auth upper-layer'
                    : 'wrapper'
            "
        >
            <div
                :class="
                    $route.name === 'login' ||
                    $route.name === 'register' ||
                    $route.name === 'error.404' ||
                    $route.meta.hideNavbar
                        ? ''
                        : 'content'
                "
            >
                <!--                <p>Текущий маршрут: {{ currentRouteName }}</p>-->
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

<script lang="ts" setup>
import { watch, ref} from 'vue'
import { useMenuStore } from '@/stores/MenuStore.ts'
import { storeToRefs } from 'pinia'

const menuStore = useMenuStore()

const { expandSidebar, sidebarWidthExpanded, sidebarWidthCollapsed } = storeToRefs(menuStore)

const sidebarWidth_ = ref('50px')

watch(() => expandSidebar.value, () => {
    sidebarWidth_.value = expandSidebar.value ? sidebarWidthExpanded.value : sidebarWidthCollapsed.value
    document.documentElement.style.setProperty('--sidebar-width', sidebarWidth_.value)
}, {deep: true})


// import { computed, /*onMounted, reactive, ref, unref, watch*/ } from 'vue'
// import { useRoute, useRouter } from 'vue-router'
// import {getColorClassByType, getTextColorClassByType} from '@/app/helpers/helpers.js'
//
//
// const router = useRouter()
// const route = useRoute()
//
// const name = computed(() => route.name)
// const name_ = computed(() => router.currentRoute.value.name)

defineProps({
    auth: {
        type: Boolean,
        required: false,
        default: false,
    },
})





// attract Работает!!!
// warning Работает!!!
// const currentRouteName = ref(route.name);
//
// watch(
//     () => route.name,
//     (newRouteName) => {
//         currentRouteName.value = newRouteName;
//         console.log('Маршрут изменился на:', newRouteName);
//     }
// );
// warning Работает!!!
// attract Работает!!!

// console.log(await route)

// onMounted(() => {
//     console.log(route.fullPath)
// })
//
//
// watch((route) => {
//     console.log(route.fullPath)})

// watch((route) => props.type, (type) => {
//     currentTextColor.value = getTextColorClassByType(props.type)
//     backgroundColor.value = getColorClassByType(props.type, 'bg', currentColorIndex)
//     borderColor.value = getColorClassByType(props.type, 'border', currentColorIndex)
// })

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
.wrapper {
    flex: 1;
    position: relative;
}

.container-auth {
    flex: 1;
}

.content {
   /* --sidebar-width: v-bind(sidebarWidth_.value);*/
    position: absolute;
    min-width: calc(
        100vw - var(--sidebar-width)
    );
    /* или ширина в пикселях *
       /* //width: calc(100% - var(--sidebar-width)); // Если раскомментируем - получим фиксированную ширину страницы */
    height: calc(100% - var(--header-height) - var(--footer-height));

    top: var(--header-height);
    left: var(--sidebar-width);
    overflow-y: auto;
    overflow-x: auto;

    transition: width 0.3s ease; /* Добавь плавности для красоты */
}
</style>
