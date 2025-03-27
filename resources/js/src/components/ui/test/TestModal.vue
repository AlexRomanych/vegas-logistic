<template>
    <div>
        <button @click="showConfirmation">Показать модальное окно</button>
    </div>
    <Modal
        ref="modalRef"
    />
</template>

<script setup>
import { ref } from 'vue';
import Modal from './TestModalAsync.vue';

const modalRef = ref(null);

const showConfirmation = async () => {
    const result = await modalRef.value.show();
    console.log('Результат:', result);
}
</script>
