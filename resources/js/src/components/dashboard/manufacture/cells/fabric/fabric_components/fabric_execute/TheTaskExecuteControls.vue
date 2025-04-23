<template>

    <div class="flex">

        <!-- attract: Начать выполнение -->
        <AppLabelMultiLine
            :text="['Начать', 'выполнение']"
            :type="startButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="startExecuteRoll()"
        />

        <!-- attract: Приостановить выполнение -->
        <AppLabelMultiLine
            :text="['Приостановить', 'выполнение']"
            :type="pauseButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="pauseExecuteRoll()"
        />

        <!-- attract: Продолжить выполнение -->
        <AppLabelMultiLine
            :text="['Продолжить', 'выполнение']"
            :type="resumeButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="resumeExecuteRoll()"
        />

        <!-- attract: Закончить выполнение -->
        <AppLabelMultiLine
            :text="['Закончить', 'выполнение']"
            :type="endButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="finishExecuteRoll()"
        />

        <!-- attract: Редактировать -->
        <AppLabelMultiLine
            :text="['Редактировать', 'сохранить']"
            align="center"
            type="warning"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="toggleInformation()"
        />

        <!-- attract: Не выполнено -->
        <AppLabelMultiLine
            :text="['Не', 'выполнено']"
            align="center"
            type="danger"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="toggleInformation()"
        />

        <!-- attract: Отметить как переходящий -->
        <AppLabelMultiLine
            :text="rollingMarkLabelText"
            :type="rollingButtonDisabledFlag ? 'dark' : 'orange'"
            align="center"
            type="orange"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="toggleRollingMark()"
        />

        <!-- attract: Больше/Меньше -->
        <AppLabelMultiLine
            :text="extendInfoLabelText"
            align="center"
            type="primary"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="toggleInformation()"
        />

    </div>

    <!-- attract: Модальное окно -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

</template>

<script setup>
import {ref, defineEmits, defineProps, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES, FABRIC_ROLL_STATUS, FABRIC_TASK_STATUS} from '/resources/js/src/app/constants/fabrics.js'

import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '/resources/js/src/components/ui/modals/AppModalAsyncMultiline.vue'

const props = defineProps({
    rolls: {                        // для расчета условий рендеринга
        type: Array,
        required: false,
        default: () => []
    },
    machine: {
        type: Object,
        required: false,
        default: () => FABRIC_MACHINES.AMERICAN,
    }
})

const emits = defineEmits([
    'start-execute-roll', 'pause-execute-roll', 'resume-execute-roll', 'finish-execute-roll',
])

// attract: Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()


// info: Функции-хелперы (логика) для работы с кнопками управления выполнением
// attract: Возвращает true, если в задании есть активное выполнение
const isExecuteRollPresent = () => {
    let isFind = false
    props.rolls.forEach((roll) => {
        isFind ||= roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.RUNNING.CODE)
    })
    return isFind
}

// attract: Возвращает true, если в задании есть приостановленное выполнение
const isPausedRollPresent = () => {
    let isFind = false
    props.rolls.forEach((roll) => {
        isFind ||= roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.PAUSED.CODE)
    })
    return isFind
}

// attract: Возвращает true, если в задании есть переходящий рулон
const isRollingRollPresent = () => {
    let isFind = false
    props.rolls.forEach((roll) => {
        isFind ||= roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.ROLLING.CODE)
    })
    return isFind
}

// attract: Возвращает статус кнопки "Начать выполнение"
const isStartButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.DONE.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) return true
    if (isExecuteRollPresent()) return true
    if (isPausedRollPresent()) return true
    if (isRollingRollPresent()) return true
    return false
}

// attract: Возвращает статус кнопки "Приостановить выполнение"
const isPauseButtonDisabled = () => {
    // if (isExecuteRollPresent()) return false
    // if (isPausedRollPresent()) return true
    // return true

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    return fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.RUNNING.CODE
}

// attract: Возвращает статус кнопки "Возобновить выполнение"
const isResumeButtonDisabled = () => {
    // if (isExecuteRollPresent()) return false
    // if (isPausedRollPresent()) return true
    // return true

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    return fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.PAUSED.CODE
}

// attract: Возвращает статус кнопки "Закончить выполнение"
const isEndButtonDisabled = () => {
    // if (isExecuteRollPresent()) return false
    // if (isPausedRollPresent()) return true
    // return true

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    return fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.RUNNING.CODE
}

// attract: Возвращает статус кнопки "Переходящий рулон"
const isRollingButtonDisabled = () => {
    // if (!isPausedRollPresent()) return true         // если нет рулона на паузе, то не не активируем
    // if (isRollingRollPresent()) return false
    // return true

    console.log('rolling: ', fabricsStore.globalActiveRolls[props.machine.TITLE])

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.PAUSED.CODE) return true
}


// attract: Реактивные переменные для работы кнопок управления выполнением
const startButtonDisabledFlag = ref(isStartButtonDisabled())    // нужно для реактивности
const pauseButtonDisabledFlag = ref(isPauseButtonDisabled())    // нужно для реактивности
const resumeButtonDisabledFlag = ref(isResumeButtonDisabled())  // нужно для реактивности
const endButtonDisabledFlag = ref(isEndButtonDisabled())        // нужно для реактивности

const rollingButtonDisabledFlag = ref(isRollingButtonDisabled())

// info: ---------------------------------------------------------

//hr--------------------------------------------------------------

// attract: Получаем ссылку на модальное окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')


//hr--------------------------------------------------------------


// info: Обрабатываем нажатие кнопок управления выполнением + доп функционал
// attract: больше/меньше инфы
const extendInfoLabelText = ref(['Меньше', 'инфы'])
const toggleInformation = () => {
    fabricsStore.globalExecuteRollsInfo = !fabricsStore.globalExecuteRollsInfo
    if (fabricsStore.globalExecuteRollsInfo) {
        extendInfoLabelText.value = ['Меньше', 'инфы']
        return
    }
    extendInfoLabelText.value = ['Больше', 'инфы']
}

// attract: больше/меньше инфы
const rollingMarkLabelText = ref(['Отметить', 'переходящим'])
const toggleRollingMark = () => {

    if (rollingButtonDisabledFlag.value) return

    fabricsStore.globalExecuteMarkRollRolling = !fabricsStore.globalExecuteMarkRollRolling
    if (fabricsStore.globalExecuteMarkRollRolling) {
        rollingMarkLabelText.value = ['Снять', 'отметку']
        return
    }
    rollingMarkLabelText.value = ['Отметить', 'переходящим']
}









// attract: Начать выполнение
const startExecuteRoll = async () => {

    if (startButtonDisabledFlag.value) return       // отменяем выполнение, если в задании уже есть активное выполнение

    modalText.value = ['Будет начат учет стегания рулона.', 'Продолжить?']
    modalType.value = 'warning'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalStartExecuteRoll = true  // attract: Переменная-флаг нажатия кнопки "Начать выполнение" рулона    }
        emits('start-execute-roll')
    }
}

// attract: Приостановить выполнение
const pauseExecuteRoll = async () => {

    if (pauseButtonDisabledFlag.value) return     // отменяем выполнение, если в задании уже есть активное выполнение

    modalText.value = ['Будет приостановлен учет стегания рулона.', 'Продолжить?']
    modalType.value = 'warning'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalPauseExecuteRoll = true  // attract: Переменная-флаг нажатия кнопки "Приостановить выполнение" рулона
        emits('pause-execute-roll')
    }
}

// attract: Возобновить выполнение
const resumeExecuteRoll = async () => {

    if (resumeButtonDisabledFlag.value) return      // отменяем выполнение, если нет приостановки

    modalText.value = ['Будет возобновлен учет стегания рулона.', 'Продолжить?']
    modalType.value = 'warning'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalResumeExecuteRoll = true // attract: Переменная-флаг нажатия кнопки "Возобновить выполнение" рулона
        emits('resume-execute-roll')
    }
}

// attract: Закончить выполнение
const finishExecuteRoll = async () => {

    if (endButtonDisabledFlag.value) return         // отменяем выполнение, если в задании нет выполнения рулона

    modalText.value = ['Будет прекращен учет стегания рулона.', 'Продолжить?']
    modalType.value = 'danger'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalFinishExecuteRoll = true // attract: Переменная-флаг нажатия кнопки "Закончить выполнение" рулона
        emits('finish-execute-roll')
    }
    emits('finish-execute-roll')
}

// info: ---------------------------------------------------------------------

watch([() => props.rolls, () => fabricsStore.globalActiveRolls], () => {

    console.log('change state')

    console.log(fabricsStore.globalActiveRolls)

    // descr: Пересчитываем состояние кнопок управления выполнением
    startButtonDisabledFlag.value = isStartButtonDisabled()     // нужно для реактивности
    pauseButtonDisabledFlag.value = isPauseButtonDisabled()     // нужно для реактивности
    resumeButtonDisabledFlag.value = isResumeButtonDisabled()   // нужно для реактивности
    endButtonDisabledFlag.value = isEndButtonDisabled()         // нужно для реактивности
    rollingButtonDisabledFlag.value = isRollingButtonDisabled()
    // console.log(startButtonDisabledFlag.value)
    // console.log(endButtonDisabledFlag.value)

    // console.log('isExecuteRollPresent(): ', isExecuteRollPresent())

}, {deep: true, immediate: true})


</script>

<style scoped>

</style>
