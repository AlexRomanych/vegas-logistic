<template>

    <div class="container">

        <router-view></router-view>

        <TheNav/>
        <TheHeader/>
        <TheFooter/>

    </div>

</template>

<script>
import {ref} from 'vue'
// import TheHeader from "@/src/components/structure/TheHeader.vue"
import TheHeader from "@/src/components/dashboard/TheHeader.vue"
import TheFooter from "@/src/components/dashboard/TheFooter.vue";
import TheNav from "@/src/components/dashboard/TheNav.vue";



export default {


    setup() {

        // const menuStore = useMenuStore()

        // console.log(menuStore.menuList)
        // const title = ref('This is Main Layout')

        return {
            // title,
        }
    },

    components: {TheNav, TheFooter, TheHeader},
}


</script>

<style scoped>

.container {
    display: flex;
    min-height: 100vh; /* или высота в пикселях */
    min-width: 100vw; /* или ширина в пикселях */
}




</style>
