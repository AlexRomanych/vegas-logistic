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

        <!-- attract: Отменено -->
        <AppLabelMultiLine
            :text="cancelLabelText"
            :type="cancelButtonDisabledFlag ? 'dark' : 'stone'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="danger"
            width="w-[100px]"
            @click="toggleCancel()"
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

        <!-- attract: Изменить метраж ткани -->
        <AppLabelMultiLine
            :text="['Изменить', 'метраж ткани']"
            :type="changeTextileLengthButtonDisabledFlag ? 'dark' : 'warning'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
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

        <!-- attract: Добавить рулон -->
        <AppLabelMultiLine
            :text="['Добавить', 'рулон']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="indigo"
            width="w-[100px]"
            @click="addRoll()"
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
        :mode="modalConfirm"
        :text="modalText"
        :type="modalType"
    />

    <!-- attract: Модальное окно для ввода текстовых данных -->
    <AppModalAsyncArea
        ref="appModalAsyncArea"
        :text="modalTextArea"
        :type="modalTypeArea"
        :value="modalInitValueArea"
        mode="confirm"
    />

    <!-- attract: Модальное окно для ввода числовых данных -->
    <AppModalAsyncNumber
        ref="appModalAsyncNumber"
        :max=300
        :max-value=300
        :text="modalTextNumber"
        :type="modalTypeNumber"
        :value="modalInitValueNumber"
        mode="confirm"
        step="0.01"
    />

    <!-- attract: Модальное (асинхронное) окно для ввода данных для переходящего рулона -->
    <TheTaskExecuteRollRollingData
        ref="theTaskExecuteRollRollingData"
        :mode="rollingRollConfirm"
        :text="rollingRollText"
        :type="rollingRollType"
    />

    <!-- attract: Добавляем рулон (асинхронно) -->
    <TheTaskExecuteRollAdd
        ref="theTaskExecuteRollAdd"
        :fabrics="fabricsData"
        :text="addingRollText"
        :type="addingRollType"
    />


</template>

<script setup>
import {ref, watch} from 'vue'

import {useFabricsStore} from '@/stores/FabricsStore.js'

import {FABRIC_MACHINES, FABRIC_ROLL_STATUS, FABRIC_TASK_STATUS} from '@/app/constants/fabrics.js'

import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppModalAsyncArea from '@/components/ui/modals/AppModalAsyncArea.vue'
import AppModalAsyncNumber from '@/components/ui/modals/AppModalAsyncNumber.vue'
import TheTaskExecuteRollRollingData
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRollRollingData.vue'
import TheTaskExecuteRollAdd
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRollAdd.vue'

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
    'cancel-execute-roll',
    'add-execute-roll',
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
    // console.log('start: ', fabricsStore.globalActiveRolls[props.machine.TITLE]?.status)

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.DONE.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE) return true
    if (isExecuteRollPresent()) return true
    if (isPausedRollPresent()) return true
    if (isRollingRollPresent()) return true

    // __ Определяем, есть ли в задании рулоны, со статусом "Создано" до текущего (по номеру рулона)
    let isFind = false
    const currentRollOrder = fabricsStore.globalActiveRolls[props.machine.TITLE].position
    props.rolls.forEach((roll) => {
        roll.rolls_exec.forEach(roll_exec => {
            isFind ||= (roll_exec.position < currentRollOrder) && (roll_exec.status === FABRIC_ROLL_STATUS.CREATED.CODE)
        })
    })
    if (isFind) return true
    // console.log('currentRollOrder: ', currentRollOrder)
    // console.log('isFind: ', isFind)

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
        fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE ? ['Снять отметку', 'Не выполнено'] : ['Не', 'выполнено']

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CREATED.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) return false
    // if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE) return false

    return true
}


// attract: Возвращает статус кнопки "Отменено"
const cancelLabelText = ref(['Отменено', ''])
const isCancelButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст

    cancelLabelText.value =
        fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE ? ['Снять отметку', 'Отменено'] : ['Отменено', '']

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CREATED.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE) return false
    // if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) return false

    return true
}


// attract: Возвращает статус кнопки "Изменить метраж ткани"
const isChangeTextileLengthButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.DONE.CODE) return true

    return false
}


// attract: Реактивные переменные для работы кнопок управления выполнением
const startButtonDisabledFlag = ref(isStartButtonDisabled())    // нужно для реактивности
const pauseButtonDisabledFlag = ref(isPauseButtonDisabled())    // нужно для реактивности
const resumeButtonDisabledFlag = ref(isResumeButtonDisabled())  // нужно для реактивности
const endButtonDisabledFlag = ref(isEndButtonDisabled())        // нужно для реактивности

const rollingButtonDisabledFlag = ref(isRollingButtonDisabled())
const falseButtonDisabledFlag = ref(isFalseButtonDisabled())
const changeTextileLengthButtonDisabledFlag = ref(isChangeTextileLengthButtonDisabled())
const cancelButtonDisabledFlag = ref(isCancelButtonDisabled())


// info: ---------------------------------------------------------

//hr--------------------------------------------------------------

// attract: Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')
const modalConfirm = ref('confirm')

// attract: Получаем ссылку на модальное для ввода текстовых данных окно с асинхронной функцией
const appModalAsyncArea = ref(null)
const modalTextArea = ref([])
const modalTypeArea = ref('danger')
const modalInitValueArea = ref('')

// attract: Получаем ссылку на модальное для ввода числовых данных окно с асинхронной функцией
const appModalAsyncNumber = ref(null)
const modalTextNumber = ref([])
const modalTypeNumber = ref('danger')
const modalInitValueNumber = ref(0)

// attract: Получаем ссылку на модальное для ввода данных переходящего рулона
const theTaskExecuteRollRollingData = ref(null)
const rollingRollText = ref([])
const rollingRollType = ref('orange')
const rollingRollInitValue = ref(0)
const rollingRollConfirm = ref('confirm')
const rollingRoll = ref({})

// attract: Получаем ссылку на модальное для добавления рулона
const theTaskExecuteRollAdd = ref(null)
const addingRollText = ref([])
const addingRollType = ref('indigo')
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

    modalTypeNumber.value = 'warning'
    modalTextNumber.value = ['Измените длину в рулона ткани (м.п.):', '']
    modalInitValueNumber.value = fabricsStore.globalActiveRolls[props.machine.TITLE].textile_length

    const answer = await appModalAsyncNumber.value.show(fabricsStore.globalActiveRolls[props.machine.TITLE].textile_length) // показываем модалку и ждем ответ

    if (answer) {

        if (!appModalAsyncNumber.value.inputNumber) return                                    // если ничего нет, то выходим

        fabricsStore.globalExecuteRollChangeTextileLength = appModalAsyncNumber.value.inputNumber   // длина ткани
        fabricsStore.globalExecuteRollChangeTextile = !fabricsStore.globalExecuteRollChangeTextile
        emits('change-textile-length-execute-roll')
    }
}


// attract: Переходящий рулон/Снять отметку
const toggleRollingMark = async () => {

    if (rollingButtonDisabledFlag.value) return

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) {   // если уже есть отметка, то удаляем ее

        modalText.value = ['Будет сброшен статус "Переходящий".', 'Продолжить?']
        modalConfirm.value = 'confirm'
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
    modalInitValueArea.value = fabricsStore.globalActiveRolls[props.machine.TITLE].false_reason ?? ''

    const answer = await appModalAsyncArea.value.show(fabricsStore.globalActiveRolls[props.machine.TITLE].false_reason ?? '') // показываем модалку и ждем ответ
    if (answer) {

        if (!appModalAsyncArea.value.inputText.trim()) return                                   // если ничего нет, то выходим


        rollingRollText.value = ['Оцените трудозатраты, которые', 'были уже затрачены на', 'выполнение данного рулона.']
        rollingRollInitValue.value = 150
        rollingRoll.value = fabricsStore.globalActiveRolls[props.machine.TITLE]


        const rollingRollLength = await theTaskExecuteRollRollingData.value.show(rollingRoll) // показываем модалку и ждем ответ

        // console.log('123: ', theTaskExecuteRollRollingData.value.rollingLength)

        // console.log('Debug')
        // return

        const rollingLength = ` (Выполнено ${theTaskExecuteRollRollingData.value.rollingLength} м.п.)`

        fabricsStore.globalExecuteMarkRollFalseReason = appModalAsyncArea.value.inputText + rollingLength      // текст
        // fabricsStore.globalExecuteMarkRollFalseReason = appModalAsyncArea.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollRolling = !fabricsStore.globalExecuteMarkRollRolling
        emits('rolling-execute-roll')
    }
}


// attract: Не выполнено/Снять отметку
const toggleFalse = async () => {

    if (falseButtonDisabledFlag.value) return   // если кнопка неактивна, то выходим

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) {   // если уже есть отметка, то удаляем ее

        modalText.value = ['Будет изменен статус рулона на "Создано".', 'Продолжить?']
        modalConfirm.value = 'confirm'
        modalType.value = 'danger'
        modalInitValueArea.value = ''

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
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

    const answer = await appModalAsyncArea.value.show() // показываем модалку и ждем ответ
    if (answer) {

        if (!appModalAsyncArea.value.inputText.trim()) return                                   // если ничего нет, то выходим

        fabricsStore.globalExecuteMarkRollFalseReason = appModalAsyncArea.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollFalse = !fabricsStore.globalExecuteMarkRollFalse
        emits('false-execute-roll')
    }
}


// attract: Отменен/Снять отметку
const toggleCancel = async () => {

    if (cancelButtonDisabledFlag.value) return   // если кнопка неактивна, то выходим

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE) {   // если уже есть отметка, то удаляем ее

        modalText.value = ['Будет изменен статус рулона на "Создано".', 'Продолжить?']
        modalConfirm.value = 'confirm'
        modalType.value = 'danger'
        modalInitValueArea.value = ''

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            fabricsStore.globalExecuteMarkRollCancelReason = ''
            fabricsStore.globalExecuteMarkRollCancel = !fabricsStore.globalExecuteMarkRollCancel
            emits('cancel-execute-roll')
        }

        return
    }

    modalTypeArea.value = 'danger'
    modalTextArea.value = ['Статус рулона будет изменен на "Отменено".', 'Укажите, пожалуйста, причину.']
    modalInitValueArea.value = ''

    const answer = await appModalAsyncArea.value.show() // показываем модалку и ждем ответ
    if (answer) {

        if (!appModalAsyncArea.value.inputText.trim()) return                                   // если ничего нет, то выходим

        fabricsStore.globalExecuteMarkRollCancelReason = appModalAsyncArea.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollCancel = !fabricsStore.globalExecuteMarkRollCancel
        emits('cancel-execute-roll')
    }

}


// attract: Начать выполнение
const startExecuteRoll = async () => {

    if (startButtonDisabledFlag.value) return       // отменяем выполнение, если в задании уже есть активное выполнение

    await changeTextileLength()                     // warning: Защита от дурака. Меняем длину ткани, если это надо

    modalText.value = ['Будет начат учет стегания рулона.', 'Продолжить?']
    modalConfirm.value = 'confirm'
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
    modalConfirm.value = 'confirm'
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
    modalConfirm.value = 'confirm'
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

    // проверка на ответственного
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].finish_by === 0) {
        modalText.value = ['Не заполнен ответственный за выпуск рулона.', 'Заполните ответственного за выполнение.']
        modalConfirm.value = 'inform'
        modalType.value = 'danger'
        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        return
    }

    modalText.value = ['Будет прекращен учет стегания рулона.', 'Продолжить?']
    modalConfirm.value = 'confirm'
    modalType.value = 'danger'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalFinishExecuteRoll = true // attract: Переменная-флаг нажатия кнопки "Закончить выполнение" рулона
        emits('finish-execute-roll')
    }
    emits('finish-execute-roll')
}


// attract: Добавить рулон
const fabricsData = ref([])
const addRoll = async () => {

    modalTypeArea.value = 'warning'
    modalTextArea.value = ['Будет добавлен новый рулон.', 'Укажите, пожалуйста, причину.']
    modalInitValueArea.value = ''

    const answer = await appModalAsyncArea.value.show() // показываем модалку и ждем ответ
    if (answer) {


        if (!appModalAsyncArea.value.inputText.trim()) return                                   // если ничего нет, то выходим


        // Формируем данные для добавления рулона
        const fabrics = fabricsStore.fabricsMemory

        fabrics.forEach(fabric => {
            if (fabric.machines.some(machine => machine.id === props.machine.ID) &&
                fabric.id !== 0 &&
                fabric.active &&
                fabric.correct) {
                fabricsData.value.push(fabric)
            }
        })


        // console.log(fabricsData.value)

        addingRollText.value = ['Будет добавлен один новый рулон.', 'Выберите полотно стеганное для добавления рулона.']

        const answer = await theTaskExecuteRollAdd.value.show(fabricsData.value)
        if (answer) {

            const selectedFabricId = theTaskExecuteRollAdd.value.selectedFabric

            // console.log(selectedFabricId)

            fabricsStore.globalExecuteRollAddReason = appModalAsyncArea.value.inputText       // текст
            fabricsStore.globalExecuteRollAddData = {fabricId: selectedFabricId, machineId: props.machine.ID}
            fabricsStore.globalExecuteRollAdd = true
        }

        emits('add-execute-roll')
    }

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
    changeTextileLengthButtonDisabledFlag.value = isChangeTextileLengthButtonDisabled()
    cancelButtonDisabledFlag.value = isCancelButtonDisabled()

    // console.log(startButtonDisabledFlag.value)
    // console.log(endButtonDisabledFlag.value)

    // console.log('isExecuteRollPresent(): ', isExecuteRollPresent())

}, {deep: true, immediate: true})


</script>

<style scoped>

</style>
