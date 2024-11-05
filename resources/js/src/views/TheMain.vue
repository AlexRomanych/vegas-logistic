<template>
    <main class="content">
<!--        <div>The Main</div>-->
<router-view></router-view>



    </main>
</template>


<script setup>


</script>


<style scoped>
.content {
    flex: 1;
    overflow-y: auto;
    margin: var(--header-height) 0 var(--footer-height) var(--sidebar-width);
    /*   position: relative; */
}
</style>
