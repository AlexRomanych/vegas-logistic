<template>
    <div class="bordered-div">
        <span class="border-text">Надпись</span>
        Содержимое div...
    </div>
</template>

<script setup>

</script>

<style scoped>

.bordered-div {
    position: relative;
    width: 300px;
    padding: 20px;
    border: 2px solid #333;
    text-align: center;
}

.border-text {
    position: absolute;
    top: -12px; /* Половина высоты бордера и половина font-size */
    left: 50%;
    transform: translateX(-50%);
    background-color: white; /* Цвет фона должен совпадать с фоном div */
    padding: 0 10px;
    font-size: 16px;
    color: #333;
}
</style>
