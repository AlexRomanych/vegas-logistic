<template>

    <div class="ml-2 mt-2">

        <div v-if="workers.length">

            <div v-for="worker in workers" :key="worker.id">

                <div v-if="worker.id" class="flex">
                    <AppLabel
                        :text="worker.surname"
                    />

                    <AppLabel
                        :text="worker.name"
                    />

                    <AppLabel
                        :text="worker.patronymic"
                    />

                    <AppLabel
                        :text="worker.cell_item_name"
                    />

                    <router-link :to="{name: 'worker.edit', params: {id: worker.id}}">
                        <AppLabel
                            align="center"
                            class="cursor-pointer"
                            text="Ред."
                            type="primary"
                            width="w-[50px]"
                        />
                    </router-link>

                    <AppLabel
                        align="center"
                        class="cursor-pointer"
                        text="Удалить"
                        type="danger"
                        width="w-[80px]"
                        @click="workerDelete(worker.id)"
                    />
                </div>
            </div>

        </div>

        <div v-else>
            <AppLabel
                text="Список пуст"
                type="info"
            />
        </div>

        <div class="mt-8">
            <router-link :to="{name: 'workers.add'}">
                <AppLabel
                    align="center"
                    class="cursor-pointer"
                    height="h-[50px]"
                    text="Создать"
                    textSize="large"
                    type="primary"
                    width="w-[200px]"
                />
            </router-link>
        </div>

    </div>


    <AppModal
        :show="modalShow"
        mode="confirm"
        text="Сотрудник будет удален. Продолжить?"
        type="danger"
        @closeModal="closeModal"

    />






    <div class="relative flex h-80 w-full items-center justify-center overflow-hidden rounded-xl bg-gray-900">

        <div class="absolute h-40 w-40 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 blur-3xl"></div>
        <div class="absolute bottom-10 right-20 h-32 w-32 rounded-full bg-blue-500 blur-2xl"></div>

        <div class="relative z-10 w-72 rounded-2xl border border-white/20 bg-white/10 p-6 shadow-2xl backdrop-blur-md">
            <h3 class="text-xl font-bold text-white">Glassmorphism</h3>
            <p class="mt-2 text-sm text-gray-200">
                Я нахожусь поверх всех элементов, размывая то, что находится подо мной.
            </p>
            <button class="mt-4 w-full rounded-lg bg-white/20 py-2 text-sm font-medium text-white hover:bg-white/30 transition-colors">
                Понятно
            </button>
        </div>

    </div>


</template>

<script setup lang="ts">

import { ref } from 'vue'
import { useWorkersStore } from '@/stores/WorkersStore.ts'

// import TheWorkerEditForm from '/resources/js/src/components/dashboard/workers/TheWorkerEditForm.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'
// import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'

const workersStore = useWorkersStore()

const workers = ref([])

const getWorkers = async () => {
    workers.value = await workersStore.getWorkers()
    console.log(workers.value)


}

await getWorkers()


// let workers = reactive(await workersStore.getWorkers())

// console.log(workers)

// const editMode = ref(false)             // режим отображения - либо показываем список, либо редактируем

const modalShow    = ref(false)
const modalAnswer  = ref(false)
let deleteWorkerId = 0
let worker         = ref(null)

// Удаляем работника
const workerDelete = (id) => {
    modalShow.value = true
    deleteWorkerId  = id
    // console.log(id)
    // console.log(modalShow.value)
}

// Удаляем рабочего после модального окна
const closeModal = async (answer) => {
    modalAnswer.value = answer
    modalShow.value   = false
    // console.log('answer', modalAnswer.value)

    if (answer) {
        workers      = workers.filter(worker => worker.id !== deleteWorkerId)
        const result = await workersStore.deleteWorker(deleteWorkerId)
        // workers = await workersStore.getWorkers()
        // console.log(result)
    }
}


</script>

<style scoped>

</style>
