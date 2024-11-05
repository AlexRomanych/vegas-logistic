<template>

    <div class="container">




        <TheNav/>
        <TheHeader/>


        <TheFooter/>
        <TheMain/>

<!--        <router-view></router-view>     &lt;!&ndash; вывод всех компонентов &ndash;&gt;-->

    </div>

</template>

<script>
import {ref} from 'vue'
// import TheHeader from "@/src/components/structure/TheHeader.vue"
import TheHeader from "@/src/components/dashboard/TheHeader.vue"
import TheFooter from "@/src/components/dashboard/TheFooter.vue";
import TheNav from "@/src/components/dashboard/TheNav.vue";
import TheMain from "@/src/views/TheMain.vue";



export default {


    setup() {

        // const menuStore = useMenuStore()

        // console.log(menuStore.menuList)
        // const title = ref('This is Main Layout')

        return {
            // title,
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
