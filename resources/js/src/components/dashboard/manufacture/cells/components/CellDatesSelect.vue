<template>
    <div class="flex justify-start items-end ml-2">

        <div>
            <AppLabel
                :bold="true"
                align="center"
                height="h-[35px]"
                text="Архив: "
                width="w-[100px]"
            />
        </div>

        <div>
            <AppInputDate
                id="start"
                label="Начало"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>

        <div>
            <AppInputDate
                id="end"
                label="Окончание"
                type="light"
                @getInputDate="setPeriod"
            />
        </div>
        <div>
            <AppButton
                id="apply"
                title="Показать"
                type="dark"
                width="w-[150px]"
                @buttonClick="clickApply"
            />
        </div>
    </div>
</template>

<script setup>
// import {ref} from 'vue'
import {compareDatesLogic} from "/resources/js/src/app/helpers/helpers_date.js"
// import {isResponseWithError} from "/resources/js/src/app/helpers/helpers_checks.js"

// import {CELL_TYPES} from '/resources/js/src/app/constants/sewingTypes.js'

import AppInputDate from '/resources/js/src/components/ui/inputs/AppInputDate.vue'
import AppButton from '/resources/js/src/components/ui/buttons/AppButton.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'

// const props = defineProps({
//     sewingType: {
//         type: String,
//         required: true,
//         validator: (sewingType) => CELL_TYPES.includes(sewingType)
//     },
//
// })

const emits = defineEmits(['clickApply'])

// интервал дат в виде объекта
const dateInterval = {}

// attract: Устанавливаем интервал
const setPeriod = (pointDate) => {
    // console.log(pointDate.id, pointDate.value)

    if (pointDate.id === 'start') {
        dateInterval.start = pointDate.value
    } else if (pointDate.id === 'end') {
        dateInterval.end = pointDate.value
    }
}

// attract: Нажимаем Показать
const clickApply = async (id) => {
    // подготавливаем данные
    const formattedDate = new Date().toISOString().slice(0, 10)  // дата в формате YYYY-MM-DD
    dateInterval.start = dateInterval.start ?? formattedDate
    dateInterval.end = dateInterval.end ?? formattedDate

    // инвертируем, если дата начала больше даты окончания
    if (!compareDatesLogic(dateInterval.start, dateInterval.end)) {
        const start = dateInterval.start
        dateInterval.start = dateInterval.end
        dateInterval.end = start
    }

    emits('clickApply', dateInterval)
}

</script>

<style scoped>

</style>
