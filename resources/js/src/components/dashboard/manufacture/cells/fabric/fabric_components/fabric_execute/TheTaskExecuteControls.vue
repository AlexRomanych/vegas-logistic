<template>

    <div class="flex">

        <!-- __ Начать выполнение -->
        <AppLabelMultiLine
            :text="['Начать', 'выполнение']"
            :type="startButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="startExecuteRoll()"
        />

        <!-- __ Приостановить выполнение -->
        <AppLabelMultiLine
            :text="['Приостановить', 'выполнение']"
            :type="pauseButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="pauseExecuteRoll()"
        />

        <!-- __ Продолжить выполнение -->
        <AppLabelMultiLine
            :text="['Продолжить', 'выполнение']"
            :type="resumeButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="resumeExecuteRoll()"
        />

        <!-- __ Закончить выполнение -->
        <AppLabelMultiLine
            :text="['Закончить', 'выполнение']"
            :type="endButtonDisabledFlag ? 'dark' : 'success'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="finishExecuteRoll()"
        />

        <!-- __ Не выполнено -->
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

        <!-- __ Отменено -->
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

        <!-- __ Отметить как переходящий -->
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

        <!-- __ Изменить метраж ткани -->
        <AppLabelMultiLine
            :text="['Изменить', 'метраж ткани']"
            :type="changeTextileLengthButtonDisabledFlag ? 'dark' : 'warning'"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            width="w-[100px]"
            @click="changeTextileLength()"
        />

        <!-- __ Изменить комментарий -->
        <AppLabelMultiLine
            :text="['Изменить', 'комментарий']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="warning"
            width="w-[100px]"
            @click="changeDescriptionText()"
        />

        <!-- __ Добавить рулон -->
        <AppLabelMultiLine
            :text="['Добавить', 'рулон']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="indigo"
            width="w-[100px]"
            @click="addRoll()"
        />

        <!-- __ Больше/Меньше -->
        <AppLabelMultiLine
            :text="extendInfoLabelText"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="primary"
            width="w-[100px]"
            @click="toggleInformation()"
        />

        <!-- __ Изменить порядок рулонов -->
        <AppLabelMultiLine
            v-if="!changeRollsOrderFlag"
            :text="['Изменить', 'порядок']"
            align="center"
            class="cursor-pointer"
            text-size="mini"
            type="danger"
            width="w-[100px]"
            @click="changeRollsOrder()"
        />


    </div>

    <!-- __ Модальное окно для подтверждений -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :mode="modalConfirm"
        :text="modalText"
        :type="modalType"
    />

    <!-- __ Модальное окно для ввода текстовых данных -->
    <AppModalAsyncArea
        ref="appModalAsyncArea"
        :text="modalTextArea"
        :type="modalTypeArea"
        :value="modalInitValueArea"
        mode="confirm"
    />


    <!-- __ Модальное окно для ввода причин изменения статуса -->
    <ReasonSelect
        v-if="groupId && categoryId"
        ref="reasonSelect"
        :category="categoryId"
        :group="groupId"
        :text="reasonSelectText"
        :type="reasonSelectType"
        :value="reasonSelectInitValue"
        mode="confirm"
    />

    <!-- __ Модальное окно для ввода числовых данных -->
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

    <!-- __ Модальное (асинхронное) окно для ввода данных для переходящего рулона -->
    <TheTaskExecuteRollRollingData
        ref="theTaskExecuteRollRollingData"
        :mode="rollingRollConfirm"
        :text="rollingRollText"
        :type="rollingRollType"
    />

    <!-- __ Добавляем рулон (асинхронно) -->
    <TheTaskExecuteRollAdd
        ref="theTaskExecuteRollAdd"
        :fabrics="fabricsData"
        :text="addingRollText"
        :type="addingRollType"
    />


</template>

<script setup>
import { ref, watch, nextTick } from 'vue'
import { storeToRefs } from 'pinia'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import { FABRIC_MACHINES, FABRIC_ROLL_STATUS, FABRIC_TASK_STATUS } from '@/app/constants/fabrics.js'
// import { log } from '@/app/helpers/helpers.js'

import ReasonSelect from '@/components/dashboard/manufacture/cells/components/ReasonSelect.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppModalAsyncArea from '@/components/ui/modals/AppModalAsyncArea.vue'
import AppModalAsyncNumber from '@/components/ui/modals/AppModalAsyncNumber.vue'
import TheTaskExecuteRollRollingData
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRollRollingData.vue'
import TheTaskExecuteRollAdd
    from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteRollAdd.vue'
import { REASONS } from '@/app/constants/common.js'


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

// __ Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()

const {
    globalOrderExecuteChangeFlag: changeRollsOrderFlag,         // __ Флаг изменения порядка рулонов
    globalOrderExecuteChangeReason,                             // __ Причина изменения порядка рулонов
    globalExecuteRollsInfo                                      // __ Флаг изменения больше/меньше
} = storeToRefs(fabricsStore)

// info: Функции-хелперы (логика) для работы с кнопками управления выполнением
// __ Возвращает true, если в задании есть активное выполнение
const isExecuteRollPresent = () => {
    let isFind = false
    props.rolls.forEach((roll) => {
        isFind ||= roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.RUNNING.CODE)
    })
    return isFind
}

// __ Возвращает true, если в задании есть приостановленное выполнение
const isPausedRollPresent = () => {
    let isFind = false
    props.rolls.forEach((roll) => {
        isFind ||= roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.PAUSED.CODE)
    })
    return isFind
}

// __ Возвращает true, если в задании есть переходящий рулон
const isRollingRollPresent = () => {
    let isFind = false
    props.rolls.forEach((roll) => {
        isFind ||= roll.rolls_exec.some((roll_exec) => roll_exec.status === FABRIC_ROLL_STATUS.ROLLING.CODE)
    })
    return isFind
}

// __ Возвращает true, если в задании есть рулоны с статусом "Создано" до активного
const isCreatedRollPresent = () => {
    const rollsExec = []
    props.rolls.forEach((roll) => {
        rollsExec.push(...roll.rolls_exec)
    }) // или const rollsExec = props.rolls.map(roll => roll.rolls_exec).flat()

    rollsExec.sort((a, b) => a.position - b.position)

    let isFind = false
    const currentRollPosition = fabricsStore.globalActiveRolls[props.machine.TITLE].position
    rollsExec.forEach(roll => {
        isFind ||= (roll.position < currentRollPosition) && (roll.status === FABRIC_ROLL_STATUS.CREATED.CODE)
    })
    return isFind
}


// __ Возвращает статус кнопки "Начать выполнение"
const isStartButtonDisabled = () => {
    // log('start: ', fabricsStore.globalActiveRolls[props.machine.TITLE]?.status)

    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.DONE.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) return true
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE) return true
    if (isExecuteRollPresent()) return true
    if (isPausedRollPresent()) return true
    if (isRollingRollPresent()) return true
    if (isCreatedRollPresent()) return true

    return false
}

// __ Возвращает статус кнопки "Приостановить выполнение"
const isPauseButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    return fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.RUNNING.CODE
}

// __ Возвращает статус кнопки "Возобновить выполнение"
const isResumeButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    return fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.PAUSED.CODE
}

// __ Возвращает статус кнопки "Закончить выполнение"
const isEndButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    return fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.RUNNING.CODE
}

// __ Возвращает статус кнопки "Переходящий рулон"
const rollingMarkLabelText = ref(['Отметить', 'переходящим'])
const isRollingButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст

    rollingMarkLabelText.value =
        fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE ? ['Снять отметку', 'переходящий'] : ['Отметить', 'переходящим']

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.ROLLING.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status !== FABRIC_ROLL_STATUS.PAUSED.CODE) return true
}

// __ Возвращает статус кнопки "Не выполнено"
const falseLabelText = ref(['Не', 'выполнено'])
const isFalseButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст

    if (isCreatedRollPresent()) return true

    falseLabelText.value =
        fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE ? ['Снять отметку', 'Не выполнено'] : ['Не', 'выполнено']

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CREATED.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.FALSE.CODE) return false

    return true
}


// __ Возвращает статус кнопки "Отменено"
const cancelLabelText = ref(['Отменено', ''])
const isCancelButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст

    if (isCreatedRollPresent()) return true

    cancelLabelText.value =
        fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE ? ['Снять отметку', 'Отменено'] : ['Отменено', '']

    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CREATED.CODE) return false
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.CANCELLED.CODE) return false

    return true
}


// __ Возвращает статус кнопки "Изменить метраж ткани"
const isChangeTextileLengthButtonDisabled = () => {
    if (!fabricsStore.globalActiveRolls[props.machine.TITLE]) return true   // тут еще может быть не определен контекст
    if (fabricsStore.globalActiveRolls[props.machine.TITLE].status === FABRIC_ROLL_STATUS.DONE.CODE) return true

    return false
}


// __ Реактивные переменные для работы кнопок управления выполнением
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


// __ Получаем ссылку на модальное для ввода текстовых данных окно с асинхронной функцией (новый компонент)
const reasonSelect = ref(null)
const reasonSelectText = ref([])
const reasonSelectType = ref('danger')
const reasonSelectInitValue = ref('')
const groupId = ref(null)
const categoryId = ref(null)


// __ Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')
const modalConfirm = ref('confirm')

// __ Получаем ссылку на модальное для ввода текстовых данных окно с асинхронной функцией
const appModalAsyncArea = ref(null)
const modalTextArea = ref([])
const modalTypeArea = ref('danger')
const modalInitValueArea = ref('')

// __ Получаем ссылку на модальное для ввода числовых данных окно с асинхронной функцией
const appModalAsyncNumber = ref(null)
const modalTextNumber = ref([])
const modalTypeNumber = ref('danger')
const modalInitValueNumber = ref(0)

// __ Получаем ссылку на модальное для ввода данных переходящего рулона
const theTaskExecuteRollRollingData = ref(null)
const rollingRollText = ref([])
const rollingRollType = ref('orange')
const rollingRollInitValue = ref(0)
const rollingRollConfirm = ref('confirm')
const rollingRoll = ref({})

// __ Получаем ссылку на модальное для добавления рулона
const theTaskExecuteRollAdd = ref(null)
const addingRollText = ref([])
const addingRollType = ref('indigo')
//hr--------------------------------------------------------------


// line ---------------------------------------------------------------------
// line ------------------- Обработка кликов кнопок -------------------------
// line ---------------------------------------------------------------------
// info: Обрабатываем нажатие кнопок управления выполнением + доп функционал


// __ больше/меньше инфы
const extendInfoLabelText = ref(['Меньше', 'инфы'])
const toggleInformation = () => {
    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов
    globalExecuteRollsInfo.value = !globalExecuteRollsInfo.value
    if (globalExecuteRollsInfo.value) {
        extendInfoLabelText.value = ['Меньше', 'инфы']
        return
    }
    // fabricsStore.globalExecuteRollsInfo = !fabricsStore.globalExecuteRollsInfo
    // if (fabricsStore.globalExecuteRollsInfo) {
    //     extendInfoLabelText.value = ['Меньше', 'инфы']
    //     return
    // }
    extendInfoLabelText.value = ['Больше', 'инфы']
}


// __ Изменить комментарий
const changeDescriptionText = async () => {
    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

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


// __ Изменить длину ткани
const changeTextileLength = async () => {
    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

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


// __ Переходящий рулон/Снять отметку
const toggleRollingMark = async () => {

    if (rollingButtonDisabledFlag.value) return

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

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

    reasonSelectType.value = 'orange'
    reasonSelectText.value = ['Статус рулона будет изменен на "Переходящий".', 'Укажите, пожалуйста, причину.']
    reasonSelectInitValue.value = fabricsStore.globalActiveRolls[props.machine.TITLE].false_reason ?? ''

    groupId.value = REASONS.FABRIC.CELLS_GROUP.ID                   // ПЯ
    categoryId.value = REASONS.FABRIC.CATEGORY.ROLLING.ID           // Категория

    if (groupId.value && categoryId.value) await nextTick()

    reasonSelect.value.resetState()                                 // сбрасываем состояние
    const answer = await reasonSelect.value.show()                  // показываем модалку и ждем ответ
    if (answer) {
        if (!reasonSelect.value.inputText.trim()) return                                   // если ничего нет, то выходим

        rollingRollText.value = ['Оцените трудозатраты, которые', 'были уже затрачены на', 'выполнение данного рулона.']
        rollingRollInitValue.value = 150
        rollingRoll.value = fabricsStore.globalActiveRolls[props.machine.TITLE]

        const rollingRollLength = await theTaskExecuteRollRollingData.value.show(rollingRoll) // показываем модалку и ждем ответ

        const rollingLength = ` (Выполнено ${theTaskExecuteRollRollingData.value.rollingLength} м.п.)`

        fabricsStore.globalExecuteMarkRollFalseReason = reasonSelect.value.inputText + rollingLength      // текст
        // fabricsStore.globalExecuteMarkRollFalseReason = appModalAsyncArea.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollRolling = !fabricsStore.globalExecuteMarkRollRolling
        emits('rolling-execute-roll')

    }
}

// __ Не выполнено/Снять отметку
const toggleFalse = async () => {

    if (falseButtonDisabledFlag.value) return   // если кнопка неактивна, то выходим

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

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

    reasonSelectType.value = 'danger'
    reasonSelectText.value = ['Статус рулона будет изменен на "Не выполнено".', 'Укажите, пожалуйста, причину.']
    reasonSelectInitValue.value = ''

    groupId.value = REASONS.FABRIC.CELLS_GROUP.ID                       // ПЯ
    categoryId.value = REASONS.FABRIC.CATEGORY.FALSE.ID             // Категория

    if (groupId.value && categoryId.value) await nextTick()

    reasonSelect.value.resetState()                                 // сбрасываем состояние
    const answer = await reasonSelect.value.show()                  // показываем модалку и ждем ответ
    if (answer) {

        if (!reasonSelect.value.inputText.trim()) return                                   // если ничего нет, то выходим

        fabricsStore.globalExecuteMarkRollFalseReason = reasonSelect.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollFalse = !fabricsStore.globalExecuteMarkRollFalse
        emits('false-execute-roll')

    }
}


// __ Отменен/Снять отметку
const toggleCancel = async () => {

    if (cancelButtonDisabledFlag.value) return   // если кнопка неактивна, то выходим

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

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

    reasonSelectType.value = 'danger'
    reasonSelectText.value = ['Статус рулона будет изменен на "Отменено".', 'Укажите, пожалуйста, причину.']
    reasonSelectInitValue.value = ''
    groupId.value = REASONS.FABRIC.CELLS_GROUP.ID                       // ПЯ
    categoryId.value = REASONS.FABRIC.CATEGORY.CANCELLED.ID             // Категория

    if (groupId.value && categoryId.value) await nextTick()

    reasonSelect.value.resetState()                                 // сбрасываем состояние
    const answer = await reasonSelect.value.show()                  // показываем модалку и ждем ответ
    if (answer) {

        if (!reasonSelect.value.inputText.trim()) return                                   // если ничего нет, то выходим

        fabricsStore.globalExecuteMarkRollCancelReason = reasonSelect.value.inputText       // текст
        fabricsStore.globalExecuteMarkRollCancel = !fabricsStore.globalExecuteMarkRollCancel
        emits('cancel-execute-roll')
    }

}


// __ Начать выполнение
const startExecuteRoll = async () => {

    if (startButtonDisabledFlag.value) return       // отменяем выполнение, если в задании уже есть активное выполнение

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

    await changeTextileLength()                     // warning: Защита от дурака. Меняем длину ткани, если это надо

    modalText.value = ['Будет начат учет стегания рулона.', 'Продолжить?']
    modalConfirm.value = 'confirm'
    modalType.value = 'warning'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalStartExecuteRoll = true  // __ Переменная-флаг нажатия кнопки "Начать выполнение" рулона    }
        emits('start-execute-roll')
    }
}


// __ Приостановить выполнение
const pauseExecuteRoll = async () => {

    if (pauseButtonDisabledFlag.value) return     // отменяем выполнение, если в задании уже есть активное выполнение

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

    modalText.value = ['Будет приостановлен учет стегания рулона.', 'Продолжить?']
    modalConfirm.value = 'confirm'
    modalType.value = 'warning'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalPauseExecuteRoll = true  // __ Переменная-флаг нажатия кнопки "Приостановить выполнение" рулона
        emits('pause-execute-roll')
    }
}


// __ Возобновить выполнение
const resumeExecuteRoll = async () => {

    if (resumeButtonDisabledFlag.value) return      // отменяем выполнение, если нет приостановки

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

    modalText.value = ['Будет возобновлен учет стегания рулона.', 'Продолжить?']
    modalConfirm.value = 'confirm'
    modalType.value = 'warning'

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricsStore.globalResumeExecuteRoll = true // __ Переменная-флаг нажатия кнопки "Возобновить выполнение" рулона
        emits('resume-execute-roll')
    }
}


// __ Закончить выполнение
const finishExecuteRoll = async () => {

    if (endButtonDisabledFlag.value) return         // отменяем выполнение, если в задании нет выполнения рулона

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

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
        fabricsStore.globalFinishExecuteRoll = true // __ Переменная-флаг нажатия кнопки "Закончить выполнение" рулона
        emits('finish-execute-roll')
    }
    emits('finish-execute-roll')
}


// __ Добавить рулон
const fabricsData = ref([])
const addRoll = async () => {

    changeRollsOrderFlag.value = false // сбрасываем флаг изменения порядка рулонов

    reasonSelectType.value = 'warning'
    reasonSelectText.value = ['Будет добавлен новый рулон.', 'Укажите, пожалуйста, причину.']
    reasonSelectInitValue.value = ''

    groupId.value = REASONS.FABRIC.CELLS_GROUP.ID                   // ПЯ
    categoryId.value = REASONS.FABRIC.CATEGORY.ADDING.ID            // Категория

    if (groupId.value && categoryId.value) await nextTick()

    reasonSelect.value.resetState()                                 // сбрасываем состояние
    const answer = await reasonSelect.value.show()                  // показываем модалку и ждем ответ
    if (answer) {

        if (!reasonSelect.value.inputText.trim()) return                                   // если ничего нет, то выходим

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

        addingRollText.value = ['Будет добавлен один новый рулон.', 'Выберите полотно стеганное для добавления рулона.']

        const answer = await theTaskExecuteRollAdd.value.show(fabricsData.value)
        if (answer) {

            const selectedFabricId = theTaskExecuteRollAdd.value.selectedFabric

            fabricsStore.globalExecuteRollAddReason = reasonSelect.value.inputText       // текст
            fabricsStore.globalExecuteRollAddData = {fabricId: selectedFabricId, machineId: props.machine.ID}
            fabricsStore.globalExecuteRollAdd = true
        }

        emits('add-execute-roll')
    }

}


// __ Изменить порядок рулонов
const changeRollsOrder = async () => {
    changeRollsOrderFlag.value = true  // устанавливаем флаг изменения порядка рулонов

    reasonSelectType.value = 'danger'
    reasonSelectText.value = ['Укажите, пожалуйста, причину', 'изменения порядка рулонов.']
    reasonSelectInitValue.value = ''
    groupId.value = REASONS.FABRIC.CELLS_GROUP.ID                       // ПЯ
    categoryId.value = REASONS.FABRIC.CATEGORY.REORDERED.ID             // Категория

    if (groupId.value && categoryId.value) await nextTick()

    reasonSelect.value.resetState()                                 // сбрасываем состояние
    const answer = await reasonSelect.value.show()                  // показываем модалку и ждем ответ
    if (answer) {

        if (!reasonSelect.value.inputText.trim()) {
            globalOrderExecuteChangeReason.value = ''
            return
        }                                   // если ничего нет, то выходим

        changeRollsOrderFlag.value = true  // устанавливаем флаг изменения порядка рулонов
        globalOrderExecuteChangeReason.value = reasonSelect.value.inputText.trim()
    }


}

// line ---------------------------------------------------------------------
// line ---------------------------------------------------------------------
// line ---------------------------------------------------------------------


watch([() => props.rolls, () => fabricsStore.globalActiveRolls], () => {

    // ___ Пересчитываем состояние кнопок управления выполнением
    startButtonDisabledFlag.value = isStartButtonDisabled()     // нужно для реактивности
    pauseButtonDisabledFlag.value = isPauseButtonDisabled()     // нужно для реактивности
    resumeButtonDisabledFlag.value = isResumeButtonDisabled()   // нужно для реактивности
    endButtonDisabledFlag.value = isEndButtonDisabled()         // нужно для реактивности
    rollingButtonDisabledFlag.value = isRollingButtonDisabled()
    falseButtonDisabledFlag.value = isFalseButtonDisabled()
    changeTextileLengthButtonDisabledFlag.value = isChangeTextileLengthButtonDisabled()
    cancelButtonDisabledFlag.value = isCancelButtonDisabled()

}, {deep: true, immediate: true})


</script>

<style scoped>

</style>
