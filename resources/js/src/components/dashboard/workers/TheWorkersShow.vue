<template>
    <div class="ml-2 mt-2">

        <div class="flex flex-col">

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

                <AppLabel
                    class="cursor-pointer"
                    text="Ред."
                    width="w-[50px]"
                    align="center"
                    type="primary"
                />

                <AppLabel
                    class="cursor-pointer"
                    text="Удалить"
                    width="w-[80px]"
                    align="center"
                    type="danger"
                    @click="workerDelete(worker.id)"
                />


            </div>

        </div>

    </div>

    <AppModal
        :show="modalShow"
        text="Сотрудник будет удален. Продолжить?"
        type="danger"
        mode="confirm"

    />
</template>

<script setup>
import {ref} from 'vue'
import {useWorkersStore} from '/resources/js/src/stores/WorkersStore.js'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'

const workersStore = useWorkersStore()

const workers = await workersStore.getWorkers()

console.log(workers)

// Удаляем работника
const modalShow = ref(true)
const workerDelete = (id) => {


    console.log(id)
}


</script>

<style scoped>

</style>
