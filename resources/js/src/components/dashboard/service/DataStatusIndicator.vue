<template>
    <!--    <teleport defer to="#data-status-indicator"> -->
    <teleport to="body">
        <div class="absolute top-0 left-0 w-[200px] h-[50px] z-50">
            <AppInputButton
                id="data-status-button"
                func="button"
                height="h-[40px]"
                title="Сохранить"
                type="danger"
                width="w-[190px]"
                @buttonClick="save"
            />
        </div>
    </teleport>
</template>

<script setup>
import {ref} from 'vue'
import AppInputButton from '@/src/components/ui/inputs/AppInputButton.vue'

const emit = defineEmits(['save'])
const save = (title, id = 0) => {
    emit('save', title, id)
}

</script>


<style scoped>

</style>

