<template>

    <!--    <div class="container">-->

    <!--        <TheNav/>-->
    <!--        <TheHeader/>-->
    <!--        <TheFooter/>-->
    <!--        <TheMain/>-->

    <!--    </div>-->


    <!-- Показываем страницу, если пользователь авторизован -->
    <div v-if="isAuthenticated" class="container">
<!--    <div class="container">-->

        <TheNav/>
        <TheHeader/>
        <TheFooter/>
        <!--        <router-view></router-view>-->
        <!--        <TheMain-->
        <!--            :isAuthenticated="isAuthenticated"-->
        <!--            :source="source"-->
        <!--        />-->
        <TheMain/>
    </div>

    <div v-else>
        <TheMain :auth="true"/>
    </div>

    <!--    <div v-else>-->
    <!--        <router-view></router-view>-->
    <!--    </div>-->

</template>

<script setup>
import {ref, computed} from 'vue'
// import TheHeader from "@/src/components/structure/TheHeader.vue"
import TheHeader from "/resources/js/src/components/dashboard/TheHeader.vue"
import TheFooter from "/resources/js/src/components/dashboard/TheFooter.vue";
import TheNav from "/resources/js/src/components/dashboard/TheNav.vue";
import TheMain from "/resources/js/src/components/dashboard/TheMain.vue";

import {useUserStore} from "/resources/js/src/stores/UserStore";
// import {useRouter} from "vue-router";


const user = useUserStore()
// const isAuth = await user.isAuthenticated()
const isAuthenticated = computed(() => user.isAuth)
// console.log('isAuth', isAuth)
// const isAuthenticated = ref(isAuth)

// window.location.reload()

// export default {
//
//
//     async setup() {
//         const user = useUserStore()
//         const isAuth = await user.isAuthenticated()
//
//         // console.log('is_auth', isAuth)
//
//         const isAuthenticated = ref(isAuth)
//         debugger
//         // console.log('auth', isAuthenticated.value)
//         // debugger
//
//         // console.log('user', user.getUser())
//         // console.log(user.isAuthenticated())
//
//         // const router = useRouter()
//         // console.log(router.currentRoute)
//
//         // const source = router.currentRoute.value.meta.source
//         // console.log(source)
//
//         // const menuStore = useMenuStore()
//
//         // console.log(menuStore.menuList)
//         // const title = ref('This is Main Layout')
//
//         return {
//             isAuthenticated,
//             // source,
//         }
//     },
//
//     components: {TheNav, TheFooter, TheHeader, TheMain},
// }


</script>

<style scoped>

.container {
    display: flex;
    min-height: 100vh; /* или высота в пикселях */
    min-width: 100vw; /* или ширина в пикселях */
    width: auto;
}


</style>
