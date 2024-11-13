<template>
    <footer class="footer">
        <div class="footer-container">
            Vegas {{ new Date().getFullYear() }}
        </div>
    </footer>
</template>

<script setup>

</script>


<style scoped>
.footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    height: var(--footer-height);
    background-color: var(--main-bg-color);
    border-top: 2px solid var(--main-border-color);
}

.footer-container {
    @apply flex justify-center items-center w-full font-semibold text-slate-600;
    height: var(--footer-height);
}
</style>
