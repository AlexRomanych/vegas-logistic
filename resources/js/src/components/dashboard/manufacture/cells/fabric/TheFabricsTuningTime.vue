<template>
    <div class="m-4">
        <div class="flex ml-16">
            <div v-for="(time, index) of tuningTimes" :key="index">
                <div v-if="time.pic.id !== 0">

                    <AppLabel
                        :text="time.pic.name"
                        align="center"
                        height="h-[30px]"
                        text-size="mini"
                        type="primary"
                        width="w-[50px]"
                    />

                </div>
            </div>
        </div>

        <div>
            <div v-for="(time, index) of tuningTimes" :key="index">
                <div v-if="time.pic.id !== 0">

                    <div class="flex">
                        <div class="mr-[10px]">
                            <AppLabel
                                :text="time.pic.name"
                                align="center"
                                height="h-[30px]"
                                text-size="mini"
                                type="primary"
                                width="w-[50px]"
                            />
                        </div>

                        <div v-for="(timeValue, idx) of time.times" :key="idx">

                            <div v-if="tuningTimes[idx].pic.id !== 0">

                                <AppLabel
                                    :text="index !== idx ? timeValue.toString() : ''"
                                    :type="index === idx ? 'light' : timeValue === 0 ? 'dark' : 'success'"
                                    align="center"
                                    height="h-[30px]"
                                    text-size="micro"
                                    width="w-[50px]"
                                />

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>


    </div>
</template>

<script lang="ts" setup>

import { onMounted, ref } from 'vue'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import type { ITimeItem } from '@/types'


const fabricsStore = useFabricsStore()

// __ Подготавливаем переменные
const tuningTimes = ref<ITimeItem[]>([])


// __ Получаем время переналадки
const getTuningTime = async () => {
    tuningTimes.value = await fabricsStore.getFabricsPicturesTuningTime()
}


onMounted(async () => {
    await getTuningTime()
    console.log('tuningTime:', tuningTimes.value)

})

</script>

<style scoped>

</style>
