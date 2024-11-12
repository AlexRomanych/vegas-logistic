<template>

    <!--    <div class="container">-->

    <!--        <TheNav/>-->
    <!--        <TheHeader/>-->
    <!--        <TheFooter/>-->
    <!--        <TheMain/>-->

    <!--    </div>-->


    <!-- Показываем страицу, если пользователь авторизован -->
    <div v-if="isAuthenticated" class="container">

        <TheNav/>
        <TheHeader/>
        <TheFooter/>
        <TheMain
            :isAuthenticated="isAuthenticated"
        />

    </div>

    <div v-else>
        <router-view></router-view>
    </div>

</template>

<script>
import {ref} from 'vue'
// import TheHeader from "@/src/components/structure/TheHeader.vue"
import TheHeader from "@/src/components/dashboard/TheHeader.vue"
import TheFooter from "@/src/components/dashboard/TheFooter.vue";
import TheNav from "@/src/components/dashboard/TheNav.vue";
import TheMain from "@/src/views/TheMain.vue";

import {useUserStore} from "@/src/stores/UserStore";


export default {


    setup() {
        const user = useUserStore()
        const isAuthenticated = ref(user.isAuthenticated())

        console.log('user', user.getUser())
        console.log(user.isAuthenticated())

        // const menuStore = useMenuStore()

        // console.log(menuStore.menuList)
        // const title = ref('This is Main Layout')

        return {
            isAuthenticated,
        }
    },

    components: {TheNav, TheFooter, TheHeader, TheMain},
}


</script>

<style scoped>

.container {
    display: flex;
    min-height: 100vh; /* или высота в пикселях */
    min-width: 100vw; /* или ширина в пикселях */
}


</style>
