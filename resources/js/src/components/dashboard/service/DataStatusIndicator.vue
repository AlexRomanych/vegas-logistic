<template>
    <!--    <teleport defer to="#data-status-indicator"> -->
    <teleport to="body">
        <div class="absolute top-0 left-0 w-[200px] h-[50px] z-50">
            <AppInputButton
                id="data-status"
                :title="'Сохранить'"
                func="button"
                type="danger"
                width="w-[190px]"
                height="h-[40px]"

            />
        </div>
    </teleport>
</template>

<script setup>
import {ref} from 'vue'
import AppInputButton from '@/src/components/ui/inputs/AppInputButton.vue'

</script>


<style scoped>

</style>

