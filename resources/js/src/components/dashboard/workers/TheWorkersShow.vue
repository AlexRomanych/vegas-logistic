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
                    text="Ред."
                    width="w-[50px]"
                    align="center"
                    type="primary"
                />

                <AppLabel
                    text="Удалить"
                    width="w-[80px]"
                    align="center"
                    type="danger"
                />


            </div>

        </div>

    </div>

</template>

<script setup>
import {ref} from 'vue'
import {useWorkersStore} from '/resources/js/src/stores/WorkersStore.js'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'


const workersStore = useWorkersStore()

const workers = await workersStore.getWorkers()

console.log(workers)


</script>

<style scoped>

</style>
