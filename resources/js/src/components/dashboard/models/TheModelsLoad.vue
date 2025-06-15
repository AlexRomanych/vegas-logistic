<template>
    <div class="ml-5 pt-3">
        <AppInputButton
            id="load"
            :bold="true"
            func="button"
            textSize="huge"
            title="Загрузить из хранилища в БД"
            type="dark"
            width="w-[400px]"
            @buttonClick="loadFromStorage"
        />
    </div>

    <AppCallout v-if="opStatus" :text="opText" :type="opType" />
</template>

<script setup>
import { ref } from 'vue'
import { useModelsStore } from '@/stores/ModelsStore'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'

const opStatus = ref(false)
const opText = ref('')
const opType = ref('')

const loadFromStorage = async () => {
    const modelsStore = useModelsStore()
    const res = await modelsStore.modelsLoad()

    console.log(res)

    opStatus.value = true
    if (!res) {
        opText.value = 'Данные успешно загружены'
        opType.value = 'success'
    } else {
        opText.value = 'Упс, что-то пошло не так'
        opType.value = 'danger'
    }
    setTimeout(() => {
        opStatus.value = false
    }, 5000)
    // console.log(res)
}
</script>

<style scoped></style>
