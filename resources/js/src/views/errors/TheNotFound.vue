<template>
    <div class="main-container upper-layer">
        <div><span class="error-code">Ошибка 404</span></div>
        <div><span class="error-text">Запрашиваемый ресурс не найден</span></div>
        <div class="mt-8">
            <span class="back-to-main">
                <router-link to="/">Вернуться на главную</router-link>
            </span>
        </div>
    </div>
</template>

<script setup>

</script>

<style scoped>
.main-container {
    @apply w-screen h-screen flex flex-col justify-center items-center bg-slate-200
}

.error-code {
    @apply text-4xl text-red-500
}

.error-text {
    @apply text-2xl text-slate-500
}

.back-to-main {
    @apply text-xl text-slate-800 cursor-pointer hover:text-slate-400
}
</style>
