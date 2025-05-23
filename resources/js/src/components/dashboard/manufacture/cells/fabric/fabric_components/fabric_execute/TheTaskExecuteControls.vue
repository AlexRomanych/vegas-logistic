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

        <!-- attract: Не выполнено -->
        <AppLabelMultiLine
            :text="falseLabelText"
            :type="falseButtonDisabledFlag ? 'dark' : 'danger'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="danger"
            width="w-[100px]"
            @click="toggleFalse()"
        />

        <!-- attract: Отметить как переходящий -->
        <AppLabelMultiLine
            :text="rollingMarkLabelText"
            :type="rollingButtonDisabledFlag ? 'dark' : 'orange'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="orange"
            width="w-[100px]"
            @click="toggleRollingMark()"
        />

        <!-- attract: Изменить количество ткани -->
        <AppLabelMultiLine
            :text="['Изменить', 'метраж ткани']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="warning"
            width="w-[100px]"
            @click="changeTextileLength()"
        />

        <!-- attract: Изменить комментарий -->
        <AppLabelMultiLine
            :text="['Изменить', 'комментарий']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="warning"
            width="w-[100px]"
            @click="changeDescriptionText()"
        />

        <!-- attract: Больше/Меньше -->
        <AppLabelMultiLine
            :text="extendInfoLabelText"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="primary"
            width="w-[100px]"
            @click="toggleInformation()"
        />

    </div>

    <!-- attract: Модальное окно для подтверждений -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!-- attract: Модальное окно для ввода данных -->
    <AppModalAsyncArea
        ref="appModalAsyncArea"
        :text="modalTextArea"
        :type="modalTypeArea"
        :value="modalInitValueArea"
        :numeric="modalNumericArea"
        mode="confirm"
        placeholder="Введите текст..."
    />

<!--    modalInitValueArea-->
</template>

<script setup>
import {ref, defineEmits, defineProps, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_MACHINES, FABRIC_ROLL_STATUS, FABRIC_TASK_STATUS} from '/resources/js/src/app/constants/fabrics.js'

import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '/resources/js/src/components/ui/modals/AppModalAsyncMultiline.vue'
import AppModalAsyncArea from '/resources/js/src/components/ui/modals/AppModalAsyncArea.vue'

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
    'start-execute-roll',
    'pause-execute-roll',
    'resume-execute-roll',
    'finish-execute-roll',
    'rolling-execute-roll',
    'false-execute-roll',
    'change-description-execute-roll',
    'change-textile-length-execute-roll'
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
    console.log('start: ', fabricsStore.globalActiveRolls[props.machine.TITLE]?.status)

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.DONE.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) return true
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
const rollingMarkLabelText = ref(['Отметить', 'переходящим'])
const isRollingButtonDisabled = () => {
    // if (!isPausedRollPresent()) return true         // если нет рулона на паузе, то не не активируем
    // if (isRollingRollPresent()) return false
    // return true

    // console.log('rolling: ', fabricsStore.globalActiveRolls[props.machine.TITLE])

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст

    rollingMarkLabelText.value =
        fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE ? ['Снять отметку', 'переходящий'] : ['Отметить', 'переходящим']

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.PAUSED.CODE) return true
}

// attract: Возвращает статус кнопки "Не выполнено"
const falseLabelText = ref(['Не', 'выполнено'])
const isFalseButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст

    falseLabelText.value =
        fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE ? ['Снять отметку', 'не выполнено'] : ['Не', 'выполнено']

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CREATED.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) return false

    return true
}

// attract: Реактивные переменные для работы кнопок управления выполнением
const startButtonDisabledFlag = ref(isStartButtonDisabled())    // нужно для реактивности
const pauseButtonDisabledFlag = ref(isPauseButtonDisabled())    // нужно для реактивности
const resumeButtonDisabledFlag = ref(isResumeButtonDisabled())  // нужно для реактивности
const endButtonDisabledFlag = ref(isEndButtonDisabled())        // нужно для реактивности

const rollingButtonDisabledFlag = ref(isRollingButtonDisabled())
const falseButtonDisabledFlag = ref(isFalseButtonDisabled())

// info: ---------------------------------------------------------

//hr--------------------------------------------------------------

// attract: Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')

// attract: Получаем ссылку на модальное для ввода данных окно с асинхронной функцией
const appModalAsyncArea = ref(null)
const modalTextArea = ref([])
const modalTypeArea = ref('danger')
const modalInitValueArea = ref('')
const modalNumericArea = ref(false)

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

// globalExecuteRollChangeTextile, globalExecuteRollChangeDescription,
// attract: Изменить комментарий
const changeDescriptionText = async () => {

    modalTypeArea.value = 'warning'
    modalTextArea.value = ['Измените комментарий:', '']
    modalNumericArea.value = false
    modalInitValueArea.value = fabricsStore.globalActiveRolls[props.machine.TITLE].descr ?? ''

    const answer = await appModalAsyncArea.value.show(fabricsStore.globalActiveRolls[props.machine.TITLE].descr ?? '') // показываем модалку и ждем ответ
    if (answer) {
        if (!appModalAsyncArea.value.inputText.trim()) return                                     // если ничего нет, то выходим

        fabricsStore.globalExecuteRollChangeDescriptionText = appModalAsyncArea.value.inputText   // текст
        fabricsStore.globalExecuteRollChangeDescription = !fabricsStore.globalExecuteRollChangeDescription
        emits('change-description-execute-roll')
    }
}

// attract: Изменить длину ткани
const changeTextileLength = async () => {

    modalTypeArea.value = 'warning'
    modalTextArea.value = ['Измените длину рулона ткани:', '']
    modalNumericArea.value = true
    modalInitValueArea.value = fabricsStore.globalActiveRolls[props.machine.TITLE].textile_length.toString()

    const answer = await appModalAsyncArea.value.show(fabricsStore.globalActiveRolls[props.machine.TITLE].textile_length.toString()) // показываем модалку и ждем ответ
    if (answer) {
        if (!appModalAsyncArea.value.inputNumber) return                                    // если ничего нет, то выходим

        fabricsStore.globalExecuteRollChangeTextileLength = appModalAsyncArea.value.inputNumber   // длина ткани
        fabricsStore.globalExecuteRollChangeTextile = !fabricsStore.globalExecuteRollChangeTextile
        emits('change-textile-length-execute-roll')
    }
}

// attract: Переходящий рулон/Снять отметку
const toggleRollingMark = async () => {

    if (rollingButtonDisabledFlag.value) return

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) {   // если уже есть отметка, то удаляем ее

        modalText.value = ['Будет сброшен статус "Переходящий".', 'Продолжить?']
        modalType.value = 'orange'

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            fabricsStore.globalExecuteMarkRollFalseReason = ''
            fabricsStore.globalExecuteMarkRollRolling = !fabricsStore.globalExecuteMarkRollRolling
            emits('rolling-execute-roll')
        }

        return
    }

    modalTypeArea.value = 'orange'
    modalTextArea.value = ['Статус рулона будет изменен на "Переходящий".', 'Укажите, пожалуйста, причину.']
    modalNumericArea.value = false
    modalInitValueArea.value = fabricsStore.globalActiveRolls[props.machine.TITLE].false_reason ?? ''

    const answer = await appModalAsyncArea.value.show(fabricsStore.globalActiveRolls[props.machine.TITLE].false_reason ?? '') // показываем модалку и ждем ответ
    if (answer) {

        if (!appModalAsyncArea.value.inputText.trim()) return                                   // если ничего нет, то выходим

        fabricsStore.globalExecuteMarkRollFalseReason = appModalAsyncArea.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollRolling = !fabricsStore.globalExecuteMarkRollRolling
        emits('rolling-execute-roll')
    }
}

// attract: Не выполнено/Снять отметку
const toggleFalse = async () => {

    if (falseButtonDisabledFlag.value) return   // если кнопка неактивна, то выходим

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) {   // если уже есть отметка, то удаляем ее

        modalText.value = ['Будет изменен статус "Не выполнено".', 'Продолжить?']
        modalType.value = 'danger'
        modalNumericArea.value = false
        modalInitValueArea.value = ''

        const answer = await appModalAsync.value.show('') // показываем модалку и ждем ответ
        if (answer) {
            fabricsStore.globalExecuteMarkRollFalseReason = ''
            fabricsStore.globalExecuteMarkRollFalse = !fabricsStore.globalExecuteMarkRollFalse
            emits('false-execute-roll')
        }

        return
    }

    modalTypeArea.value = 'danger'
    modalTextArea.value = ['Статус рулона будет изменен на "Не выполнено".', 'Укажите, пожалуйста, причину.']
    modalInitValueArea.value = ''

    const answer = await appModalAsyncArea.value.show('') // показываем модалку и ждем ответ
    if (answer) {

        if (!appModalAsyncArea.value.inputText.trim()) return                                   // если ничего нет, то выходим

        fabricsStore.globalExecuteMarkRollFalseReason = appModalAsyncArea.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollFalse = !fabricsStore.globalExecuteMarkRollFalse
        emits('false-execute-roll')
    }
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

    // console.log('change state')
    // console.log(fabricsStore.globalActiveRolls)

    // descr: Пересчитываем состояние кнопок управления выполнением
    startButtonDisabledFlag.value = isStartButtonDisabled()     // нужно для реактивности
    pauseButtonDisabledFlag.value = isPauseButtonDisabled()     // нужно для реактивности
    resumeButtonDisabledFlag.value = isResumeButtonDisabled()   // нужно для реактивности
    endButtonDisabledFlag.value = isEndButtonDisabled()         // нужно для реактивности
    rollingButtonDisabledFlag.value = isRollingButtonDisabled()
    falseButtonDisabledFlag.value = isFalseButtonDisabled()
    // console.log(startButtonDisabledFlag.value)
    // console.log(endButtonDisabledFlag.value)

    // console.log('isExecuteRollPresent(): ', isExecuteRollPresent())

}, {deep: true, immediate: true})


</script>

<style scoped>

</style>
