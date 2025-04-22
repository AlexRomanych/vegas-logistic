<template>

    <div class="flex">

        <!-- attract: Начать выполнение -->
        <AppLabelMultiLine
            :disabled="isStartButtonDisabled()"
            :text="['Начать', 'выполнение']"
            :type="isStartButtonDisabled() ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="startRollExecution()"
        />

        <!-- attract: Приостановить выполнение -->
        <AppLabelMultiLine
            :text="['Приостановить', 'выполнение']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- attract: Продолжить выполнение -->
        <AppLabelMultiLine
            :text="['Продолжить', 'выполнение']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- attract: Закончить выполнение -->
        <AppLabelMultiLine
            :disabled="isEndButtonDisabled()"
            :type="isEndButtonDisabled() ? 'dark' : 'success'"
            :text="['Закончить', 'выполнение']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- attract: Больше/Меньше -->
        <AppLabelMultiLine
            :text="extendInfoLabelText"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="toggleInformation()"
        />

    </div>

</template>

<script setup>
import {ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_ROLL_STATUS} from '/resources/js/src/app/constants/fabrics.js'

import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'

const props = defineProps({
    rolls: {                        // для расчета условий рендеринга
        type: Array,
        required: false,
        default: () => []
    }
})

const emits = defineEmits(['start-roll-execution'])

// attract: Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()

// attract: больше/меньше инфы
const extendInfoLabelText = ref(['Меньше', ''])
const toggleInformation = () => {
    fabricsStore.globalExecuteRollsInfo = !fabricsStore.globalExecuteRollsInfo
    if (fabricsStore.globalExecuteRollsInfo) {
        extendInfoLabelText.value = ['Меньше', '']
        return
    }
    extendInfoLabelText.value = ['Больше', '']
}

// attract: Возвращает true, если в задании есть активное выполнение
const isExecuteRollPresent = () => {
    props.rolls.forEach((roll) => {
        return roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.RUNNING.CODE)
    })
}

// attract: Возвращает true, если в задании есть приостановленное выполнение
const isPausedRollPresent = () => {
    props.rolls.forEach((roll) => {
        return roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.PAUSED.CODE)
    })
}

// attract: Возвращает статус кнопки "Начать выполнение"
const isStartButtonDisabled = () => {
    // noinspection JSVoidFunctionReturnValueUsed
    if (isExecuteRollPresent()) return true
    // noinspection JSVoidFunctionReturnValueUsed,RedundantIfStatementJS
    if (isPausedRollPresent()) return true
    return false
}

// attract: Возвращает статус кнопки "Закончить выполнение"
const isEndButtonDisabled = () => {
    // noinspection JSVoidFunctionReturnValueUsed
    if (isExecuteRollPresent()) return false
    // noinspection JSVoidFunctionReturnValueUsed,RedundantIfStatementJS
    if (isPausedRollPresent()) return true
    return true
}


// attract: Начать выполнение
const startRollExecution = () => {
    emits('start-roll-execution')
}

</script>

<style scoped>

</style>
