<template>
<div>I am Menu {{$route.params.groupId}}</div>
    <NavItemCard :groupId="$route.params.groupId"/>
</template>

<!--<script setup>-->
<!--import {useMenuStore} from "@/src/stores/MenuStore.js"-->
<!--import {useRouter, useRoute} from "vue-router";-->
<!--import {ref, reactive} from "vue";-->

<!--const menu = useMenuStore()-->
<!--const router = useRouter()-->
<!--const menuItem = reactive(router.currentRoute.value.params)-->

<!--const route = useRoute()-->


<!--console.log(menuItem)-->
<!--console.log(route)-->


<!--</script>-->

<script>
import NavItemMenu from "@/src/components/dashboard/nav/NavItemMenu.vue"
import NavItemCard from "@/src/components/dashboard/nav/NavItemCard.vue"
export default {
    data() {
        return {
            groupId: this.$route.params.groupId
        }
    },

    components: {
        NavItemCard
    }

}

</script>

<style scoped>

</style>
