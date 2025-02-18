<template>

    <div class="ml-2 mt-2">

        <div v-if="workers.length">

            <div v-for="worker in workers" :key="worker.id" class="flex">

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


</template>

<script setup>
import {ref, reactive} from 'vue'
import {useWorkersStore} from '/resources/js/src/stores/WorkersStore.js'
// import TheWorkerEditForm from '/resources/js/src/components/dashboard/workers/TheWorkerEditForm.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'
// import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'

const workersStore = useWorkersStore()

let workers = reactive(await workersStore.getWorkers())

// console.log(workers)

// const editMode = ref(false)             // режим отображения - либо показываем список, либо редактируем

const modalShow = ref(false)
const modalAnswer = ref(false)
let deleteWorkerId = 0
let worker = ref(null)

// Удаляем работника
const workerDelete = (id) => {
    modalShow.value = true
    deleteWorkerId = id
    // console.log(id)
    // console.log(modalShow.value)
}

// Удаляем рабочего после модального окна
const closeModal = async (answer) => {
    modalAnswer.value = answer
    modalShow.value = false
    // console.log('answer', modalAnswer.value)

    if (answer) {
        workers = workers.filter(worker => worker.id !== deleteWorkerId)
        const result = await workersStore.deleteWorker(deleteWorkerId)
        // workers = await workersStore.getWorkers()
        // console.log(result)
    }
}



</script>

<style scoped>

</style>
