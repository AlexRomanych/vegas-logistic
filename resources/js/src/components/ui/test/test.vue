<template>
    <div ref="element" v-if="show">Это элемент</div>
</template>

<script setup>
import { ref, onMounted, useTransition } from 'vue';

const element = ref(null);
const show = ref(true);

const { isEnter, isLeave } = useTransition(show, {
    onLeave(el) {
        gsap.to(el, { opacity: 0, duration: 0.5 });
    },
});
</script>
