<template>

    <div v-for="(roll, index) in rolls" :key="index">


        <div v-for="(roll_exec) in roll.rolls_exec" :key="roll_exec.id">
            <div class="flex">

                <!-- attract: Номер рулона (id записи) -->
                <AppLabel
                    :text="roll_exec.id.toString()"
                    align="center"
                    text-size="mini"
                    width="w-[60px]"
                />

                <!-- attract: Название ПС -->
                <AppLabel
                    :text="getFabricById(roll_exec.fabric_id).display_name"
                    text-size="mini"
                    width="w-[270px]"
                />

                <!-- attract: Количество рулонов (всегда = 1) -->
                <AppLabel
                    align="center"
                    text="1"
                    text-size="mini"
                    width="w-[50px]"
                />

                <!-- attract: Количество рулонов (всегда = 1) -->
                <AppLabel
                    align="center"
                    :text="roll_exec.textile_length"
                    text-size="mini"
                    width="w-[70px]"
                />

            </div>
        </div>


    </div>
</template>

<script setup>
import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'

const props = defineProps({
    rolls: {
        type: Array,
        required: false,
        default: []
    }
})

// attract: Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory

// Получаем ПС по ID
const getFabricById = (fabric_id) => fabrics.find((fabric) => fabric.id === fabric_id)

</script>

<style scoped>

</style>
