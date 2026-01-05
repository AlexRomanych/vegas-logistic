<template>
    <div v-if="!isVerified" class="flex justify-start items-center m-4">

        <div>
            <AppInputFileTS
                :check-file="checkFile"
                upload-title="Проверить заявки"
                @select-file="onFileSelected"
                @upload-file="validatePlanLoads"
            />
        </div>

    </div>

    <div v-else>

        <div class="ml-2 mt-2">
            <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
                <div>
                    <div class="flex ml-0.5">

                        <!-- __ Клиент -->
                        <AppLabelMultilineTSWrapper :render-object="render.client"/>

                        <!-- __ Номер Заявки -->
                        <AppLabelMultilineTSWrapper :render-object="render.orderNoStr"/>

                        <!-- __ Общее количество элементов (изделий) -->
                        <AppLabelMultilineTSWrapper :render-object="render.orderAmount"/>

                        <!-- __ Статус: Раскрытая/Прогнозная -->
                        <AppLabelMultilineTSWrapper :render-object="render.status"/>

                        <!-- __ Тип элементов -->
                        <AppLabelMultilineTSWrapper :render-object="render.elementsType"/>

                        <!-- __ Дата загрузки на складе Вегас -->
                        <AppLabelMultilineTSWrapper :render-object="render.loadAt"/>

                        <!-- __ Дата разгрузки на складе клиента -->
                        <AppLabelMultilineTSWrapper :render-object="render.unloadAt"/>

                        <!-- __ Результат проверки -->
                        <AppLabelMultilineTSWrapper :render-object="render.validateCheck"/>

                        <!-- __ Действие -->
                        <AppLabelMultilineTSWrapper :render-object="render.validateAction"/>

                        <!-- __ Пояснение результата -->
                        <AppLabelMultilineTSWrapper :render-object="render.validateAdvice"/>

                        <!-- __ Загрузка на сервер -->
                        <AppLabelMultilineTSWrapper :render-object="render.uploadFile" @click="uploadFile"/>

                    </div>
                </div>
            </div>

            <!-- __ Данные -->
            <div v-for="(planLoad, index) of planLoadsRender" :key="index" class="ml-2 max-w-fit">

                <div class="flex ">

                    <!-- __ Клиент -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.client"/>

                    <!-- __ Номер Заявки -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.orderNoStr"/>

                    <!-- __ Общее количество элементов (изделий) -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.orderAmount"/>

                    <!-- __ Статус: Раскрытая/Прогнозная -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.status"/>

                    <!-- __ Тип элементов -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.elementsType"/>

                    <!-- __ Дата загрузки на складе Вегас -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.loadAt"/>

                    <!-- __ Дата разгрузки на складе клиента -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.unloadAt"/>

                    <!-- __ Результат проверки -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.validateCheck"/>

                    <!-- __ Действие -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.validateAction"/>

                    <!-- __ Пояснение результата -->
                    <AppLabelTSWrapper :arg="planLoad" :render-object="render.validateAdvice"/>

                </div>

            </div>

        </div>

    </div>

    <AppCalloutTS
        :show="calloutShow"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutHandler"
    />

    <AppModalAsyncTS
        ref="appModalAsyncTS"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'

import type { IPlanLoadValidate, IRenderData } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { getFileContent } from '@/app/helpers/helpers_file_reader.js'

import { checkCRUD, isJSON, validateJsonByTemplate } from '@/app/helpers/helpers_checks.ts'

import { DEBUG } from '@/app/constants/common.ts'
import { PLAN_LOAD_TEMPLATE } from '@/app/constants/json_templates.ts'

import AppInputFileTS from '@/components/ui/inputs/AppInputFileTS.vue'
import AppModalAsyncTS from '@/components/ui/modals/AppModalAsyncTS.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/orders/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import AppCalloutTS from '@/components/ui/callouts/AppCalloutTS.vue'


const plansStore = usePlansStore()

const isVerified = ref(false)   // Маяк, что данные были отправлены на проверку на сервер

// __ Определяем переменные
const planLoadsRender   = ref<IPlanLoadValidate[]>([])
const planLoadsVerified = ref<IPlanLoadValidate[]>([])
const selectedFile      = ref<File | null>(null)
const fileData          = ref<string>('')
const checkFile         = ref<boolean>(false)


const isDataJson   = ref(true) // Проверка на тип файла для вызова Callout
const opResult     = ref(false) // Проверка на результат выполнения операции
const opResultText = ref('') // Сообщение результата операции
const opResultType = ref('') // Тип результата операции

// __ Тип для модального окна
const modalType       = ref<IColorTypes>('danger')
const modalText       = ref<string>('')
const modalMode       = ref<'inform' | 'confirm'>('inform')
const appModalAsyncTS = ref<any>(null)         // Получаем ссылку на модальное окно с асинхронной функцией


// __ Объект отображения данных
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_WIDTH_DATE = 'w-[70px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'micro'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
// const DATA_ALIGN_DEFAULT = 'center'

const ACTION_ORDER_IGNORE = 'Игнорировать Заявку'
const ACTION_ORDER_ADD    = 'Создать Заявку'
const getTypeByAction     = (action: string): IColorTypes => {
    if (action === ACTION_ORDER_IGNORE) return 'warning'
    if (action === ACTION_ORDER_ADD) return 'success'
    return DEFAULT_TYPE
}


const render: IRenderData = reactive({
    client:         {
        id:             () => 'client-search',
        header:         ['Клиент', ''],
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (planLoad: IPlanLoadValidate) => planLoad.client_id !== 0 ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Клиент...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.client_short_name ?? '',
    },
    orderNoStr:     {
        id:             () => 'order-no-search',
        header:         ['№', 'Заявки'],
        width:          'w-[50px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (planLoad: IPlanLoadValidate) => planLoad.client_id !== 0 ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍№...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.order_no,
        // color:          (planLoad: IPlanLoadValidate) => planLoad.order_type.color,
        // title:          (planLoad: IPlanLoadValidate) => planLoad.order_type.display_name
    },
    status:         {
        id:             () => 'status-search',
        header:         ['Статус', ''],
        width:          'w-[70px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (planLoad: IPlanLoadValidate) => {
            if (!planLoad || !planLoad.order_status) return DEFAULT_TYPE
            return planLoad.order_status.toLowerCase().includes('раскрытая')
                ? 'indigo' : planLoad.order_status.toLowerCase().includes('прогнозная')
                    ? 'orange' : 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Статус...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.order_status || '',
    },
    elementsType:   {
        id:             () => 'elements-type-search',
        header:         ['Тип', 'изделий'],
        width:          'w-[70px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (planLoad: IPlanLoadValidate) => {
            if (!planLoad || !planLoad.elements_type) return DEFAULT_TYPE
            return planLoad.elements_type.toLowerCase().includes('матрасы')
                ? 'success' : planLoad.elements_type.toLowerCase().includes('аксессуары')
                    ? 'info' : 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Тип изделий...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.elements_type || '',
    },
    orderAmount:    {
        id:             () => 'order-amount-search',
        header:         ['Кол-', 'во'],
        width:          'w-[50px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Кол-во...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.amounts.totals!.toString()
    },
    loadAt:         {
        id:             () => 'load-at-search',
        header:         ['Дата', 'загрузки'],
        width:          DEFAULT_WIDTH_DATE,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍дд.мм.гггг...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.load_at
    },
    unloadAt:       {
        id:             () => 'unload-at-search',
        header:         ['Дата', 'разгрузки'],
        width:          DEFAULT_WIDTH_DATE,
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍дд.мм.гггг...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.unload_at
    },
    validateCheck:  {
        id:             () => 'validate-check-search',
        header:         ['Результат', 'проверки'],
        width:          'w-[200px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Результат проверки...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.validate.check
    },
    validateAction: {
        id:             () => 'validate-check-search',
        header:         ['Действие', ''],
        width:          'w-[200px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (planLoad: IPlanLoadValidate) => getTypeByAction(planLoad.validate.action),
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Действие...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.validate.action
    },
    validateAdvice: {
        id:             () => 'validate-check-search',
        header:         ['Описание', ''],
        width:          'w-[350px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Описание...',
        data:           (planLoad: IPlanLoadValidate) => planLoad.validate.advice
    },

    uploadFile: {      // __ Кнопка загрузки
        id:             () => 'upload',
        header:         ['Загрузить', ''],
        width:          'w-[150px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => 'orange',
        dataType:       () => DATA_TYPE,
        type:           () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        class:          'cursor-pointer',
    },


})

// __ Callout
const calloutShow    = ref(false)      // состояние окна
const calloutMessage = ref('')      // определяем показываемое сообщение
const calloutType    = ref<IColorTypes>('danger')   // определяем тип callout
const calloutHandler = () => setInterval(() => (calloutShow.value = false), 500)

// todo Сделать отображение данных файла и сделать проверку на тип файла(данных)
// Получаем данные файла
const onFileSelected = async (formData: File) => {
    selectedFile.value = formData
    fileData.value     = await getFileContent(selectedFile.value)

    const isFileDataJson = isJSON(fileData.value)
    if (!isFileDataJson) {
        modalText.value = 'Файл не является валидным JSON!!!'
        appModalAsyncTS.value.show()             // показываем модалку и ждем ответ
        fileData.value     = ''                  // Очищаем данные файла
        selectedFile.value = null                // Очищаем выбранный файл
        checkFile.value    = false               // Отключаем второй шаг загрузки

    } else {
        const isValidData = validateJsonByTemplate(fileData.value, PLAN_LOAD_TEMPLATE)
        // DEBUG && console.log('isValidData: ', isValidData)
        if (!isValidData) {
            modalText.value = 'Файл не соответствует структуре JSON заявок!!!'
            appModalAsyncTS.value.show()             // показываем модалку и ждем ответ
            fileData.value     = ''                  // Очищаем данные файла
            selectedFile.value = null                // Очищаем выбранный файл
            checkFile.value    = false               // Отключаем второй шаг загрузки
        } else {
            checkFile.value = true
        }

    }

    // DEBUG && console.log('fileData: ', fileData.value)
    // DEBUG && console.log('isFileDataJson: ', isFileDataJson)
}


// __ Валидируем входящие данные на сервере
const validatePlanLoads = async () => {
    planLoadsVerified.value = await plansStore.validatePlanLoads(fileData.value)  // Получаем валидированные данные

    isVerified.value = true

    planLoadsRender.value = planLoadsVerified.value
        .sort((a, b) => a.client_short_name!.localeCompare(b.client_short_name!))

    DEBUG && console.log('planLoadsRender: ', planLoadsRender.value)
}


// __ Загружаем данные на сервер
const uploadFile = async () => {

    const result = await plansStore.uploadLoads(JSON.stringify(planLoadsVerified.value))

    if (checkCRUD(result.data)) {
        calloutMessage.value = result.payload
        calloutType.value    = 'success'
    } else {
        calloutMessage.value = result.error
        calloutType.value    = 'danger'
    }

    calloutShow.value = true    // показываем callout
    calloutHandler()            // запускаем таймер на скрытие callout
}


</script>



