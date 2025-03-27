<template>
    <div v-if="isVisible" class="modal-overlay">
        <div class="modal">
            <slot></slot>
            <div class="modal-buttons">
<!--                <button @click="resolve(true)">Подтвердить</button>-->
<!--                <button @click="resolve(false)">Отмена</button>                -->

                <button @click="select(true)">Подтвердить</button>
                <button @click="select(false)">Отмена</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const isVisible = ref(false);
let resolvePromise;

// const show = () => {
//     isVisible.value = true;
//     return new Promise((resolve) => {
//         resolvePromise = resolve;
//     });
// };

const show = () => {
    isVisible.value = true;
    return new Promise((select) => {
        resolvePromise = select;
        console.log(resolvePromise);
    });
};

// const resolve = (value) => {
//     if (resolvePromise) {
//         resolvePromise(value)
//         isVisible.value = false
//         resolvePromise = null
//     }
// }

const select = (value) => {
    if (resolvePromise) {
        resolvePromise(value)
        isVisible.value = false
        resolvePromise = null
    }
}

console.log(select)

defineExpose({
    show,
})

</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
}

.modal-buttons {
    margin-top: 20px;
    display: flex;
    justify-content: space-around;
}
</style>
