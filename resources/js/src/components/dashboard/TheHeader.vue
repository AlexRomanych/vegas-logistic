<template>
    <Suspense> <!--В компоненте Thelogo - асинхронная загрузка User-->
        <header class="header">
            <div>
                <TheLogo/>
            </div>

        </header>
    </Suspense>
</template>

<script setup>

import TheLogo from "/resources/js/src/components/dashboard/TheLogo.vue";

</script>


<style scoped>
.header {
    position: fixed;
    top: 0;
    height: var(--header-height);
    width: 100%;
    background-color: var(--main-bg-color);
    border-bottom: 2px solid var(--main-border-color);
    z-index: 100;
}
</style>
