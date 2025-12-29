<template>
    <div v-if="!isVerified" class="flex justify-start items-center m-4">

        <div>
            <AppInputFileTS
                :check-file="checkFile"
                upload-title="Проверить заявки"
                @select-file="onFileSelected"
                @upload-file="validateOrders"
            />
        </div>

    </div>

    <div v-else>


        <div class="ml-2 mt-2">
            <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">
                <div>
                    <div class="flex ml-0.5">

                        <!-- __ Collapsed -->
                        <AppLabelMultilineTSWrapper :render-object="render.collapsed" @click="toggleCollapsed"/>

                        <!-- __ Клиент -->
                        <AppLabelMultilineTSWrapper :render-object="render.client"/>

                        <!-- __ Номер Заявки -->
                        <AppLabelMultilineTSWrapper :render-object="render.orderNoStr"/>

                        <!-- __ Общее количество элементов (изделий) -->
                        <AppLabelMultilineTSWrapper :render-object="render.orderAmount"/>

                        <!-- __ Тип элементов -->
                        <AppLabelMultilineTSWrapper :render-object="render.elementsType"/>

                        <!-- __ Дата загрузки на складе Вегас -->
                        <AppLabelMultilineTSWrapper :render-object="render.loadAt"/>

                        <!-- __ Дата разгрузки на складе клиента -->
                        <AppLabelMultilineTSWrapper :render-object="render.unloadAt"/>

                        <!-- __ Комментарий из 1С -->
                        <AppLabelMultilineTSWrapper :render-object="render.comment_1c"/>

                        <!-- __ Результат проверки -->
                        <AppLabelMultilineTSWrapper :render-object="render.validateCheck"/>

                        <!-- __ Действие -->
                        <AppLabelMultilineTSWrapper :render-object="render.validateAction"/>

                        <!-- __ Пояснение результата -->
                        <AppLabelMultilineTSWrapper :render-object="render.validateAdvice"/>

                    </div>
                </div>
            </div>

            <!-- __ Данные -->
            <div v-for="(order, index) of ordersRender" :key="index" class="ml-2 max-w-fit">

                <div v-if="!order.collapsed">
                    <TheDividerLine/>
                    <div class="min-h-3 bg-red-50 rounded-[4px]"></div>
                </div>

                <div :class="!order.collapsed ? 'bg-green-100 rounded-[4px]' : ''">

                    <div class="flex ">

                        <!-- __ Collapsed -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.collapsed"
                                           @click="render.collapsed.click!(order)"/>

                        <!-- __ Клиент -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.client"/>

                        <!-- __ Номер Заявки -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.orderNoStr"/>

                        <!-- __ Общее количество элементов (изделий) -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.orderAmount"/>

                        <!-- __ Тип элементов -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.elementsType"/>

                        <!-- __ Дата загрузки на складе Вегас -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.loadAt"/>

                        <!-- __ Дата разгрузки на складе клиента -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.unloadAt"/>

                        <!-- __ Комментарий из 1С -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.comment_1c"/>

                        <!-- __ Результат проверки -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.validateCheck"/>

                        <!-- __ Действие -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.validateAction"/>

                        <!-- __ Пояснение результата -->
                        <AppLabelTSWrapper :arg="order" :render-object="render.validateAdvice"/>


                    </div>

                    <!-- __ Сами данные по Содержимому Заявки -->
                    <div v-if="!order.collapsed">
                        <OrderItems :ok-word="OK_WORD" :order-items="order.items"/>
                        <div class="min-h-3"></div>
                        <TheDividerLine/>
                    </div>
                </div>
            </div>

        </div>


    </div>


    <AppCallout v-if="!isDataJson" text="Ошибка данных!" type="danger"/>

    <AppCallout v-if="opResult" :text="opResultText" :type="opResultType"/>

    <AppModalAsyncTS
        ref="appModalAsyncTS"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />


</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'

import type { IRenderData, IValidatedOrder, IValidatedOrderItem } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import { useOrdersStore } from '@/stores/OrdersStore.ts'
import { getFileContent } from '@/app/helpers/helpers_file_reader.js'

import { isJSON, validateJsonByTemplate } from '@/app/helpers/helpers_checks.ts'

import { DEBUG } from '@/app/constants/common.ts'
import { ORDER_TEMPLATE } from '@/app/constants/json_templates.ts'

import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppInputFileTS from '@/components/ui/inputs/AppInputFileTS.vue'
import AppModalAsyncTS from '@/components/ui/modals/AppModalAsyncTS.vue'
import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/orders/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/orders/components/AppLabelTSWrapper.vue'
import OrderItems from '@/components/dashboard/orders/order_components/order_upload/OrderItems.vue'
// import AppInputTextTSWrapper from '@/components/dashboard/orders/components/AppInputTextTSWrapper.vue'
// import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'
// import { formatDateIntl } from '@/app/helpers/helpers_date'
// import AppInputFileTS from '@/components/ui/inputs/AppInputFileTS_3.vue'

const ordersStore = useOrdersStore()

const isVerified = ref(false)   // Маяк, что данные были отправлены на проверку на сервер

// __ Определяем переменные
const orders         = ref<IValidatedOrder[]>([])
const ordersRender   = ref<IValidatedOrder[]>([])
const verifiedOrders = ref<IValidatedOrder[]>([])
const selectedFile   = ref<File | null>(null)
const fileData       = ref<string>('')
const checkFile      = ref<boolean>(false)


// __ Глобальный Collapse
const collapseAll = ref(true)


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
const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_WIDTH_DATE = 'w-[70px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'micro'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
const DATA_ALIGN_DEFAULT = 'center'

const OK_WORD = 'ok'
// const CLIENT_MISSING_WORD = 'ok'

const render: IRenderData = reactive({
    collapsed:      {
        header:         ['▲', '▼'],
        width:          'w-[30px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => 'warning',
        dataType:       () => DATA_TYPE,
        type:           (order: IValidatedOrder) => order.renderType as IColorTypes,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        data:           (order: IValidatedOrder) => order.collapsed ? '▲' : '▼',
        click:          (order: IValidatedOrder) => order.collapsed = !order.collapsed
    },
    client:         {
        id:             () => 'client-search',
        header:         ['Клиент', ''],
        width:          'w-[100px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (order: IValidatedOrder) => order.client_id !== 0 ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Клиент...',
        data:           (order: IValidatedOrder) => order.client_full_name
    },
    orderNoStr:     {
        id:             () => 'order-no-search',
        header:         ['№', 'Заявки'],
        width:          'w-[50px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (order: IValidatedOrder) => order.client_id !== 0 ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍№...',
        data:           (order: IValidatedOrder) => order.order_no,
        // color:          (order: IValidatedOrder) => order.order_type.color,
        // title:          (order: IValidatedOrder) => order.order_type.display_name
    },
    elementsType:   {
        id:             () => 'elements-type-search',
        header:         ['Тип', 'изделий'],
        width:          'w-[70px]',
        height:         DEFAULT_HEIGHT,
        show:           true,
        headerType:     () => HEADER_TYPE,
        dataType:       () => DATA_TYPE,
        type:           (order: IValidatedOrder) => {
            if (!order || !order.elements_type) return DEFAULT_TYPE
            return order.elements_type.toLowerCase().includes('матрасы')
                ? 'success' : order.elements_type.toLowerCase().includes('аксессуары')
                    ? 'info' : 'danger'
        },
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Тип изделий...',
        data:           (order: IValidatedOrder) => order.elements_type || '',
        // color:          (order: IValidatedOrder) => order.order_type.color,
        // title:          (order: IValidatedOrder) => order.order_type.display_name
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
        data:           (order: IValidatedOrder) => order.items.reduce((acc: number, line: IValidatedOrderItem) => acc + line.a, 0).toString()
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
        data:           (order: IValidatedOrder) => order.load_at
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
        data:           (order: IValidatedOrder) => order.unload_at
    },
    comment_1c:     {
        id:             () => 'comment-1c-search',
        header:         ['Комментарий из 1С', ''],
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
        placeholder:    '🔍Комментарий из 1С...',
        data:           (order: IValidatedOrder) => order.comment ?? ''
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
        data:           (order: IValidatedOrder) => order.validate.check
    },
    validateAction: {
        id:             () => 'validate-check-search',
        header:         ['Действие', ''],
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
        placeholder:    '🔍Действие...',
        data:           (order: IValidatedOrder) => order.validate.action
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
        data:           (order: IValidatedOrder) => order.validate.advice
    },

})


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
        const isValidData = validateJsonByTemplate(fileData.value, ORDER_TEMPLATE)
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


// __ Collapse/Expand all
const toggleCollapsed = () => {
    collapseAll.value = !collapseAll.value
    ordersRender.value.forEach(order => order.collapsed = collapseAll.value)
}


// __ Валидируем входящие данные на сервере
const validateOrders = async () => {
    verifiedOrders.value = (await ordersStore.validateOrders(fileData.value))  // Получаем валидированные данные

    isVerified.value = true

    ordersRender.value = verifiedOrders.value
        .sort((a, b) => a.order_no_1c.localeCompare(b.order_no_1c))
        .map((order: IValidatedOrder) => ({
            ...order,
            collapsed: collapseAll.value,
            // renderType: 'danger'
            renderType: (order.items.every((item: IValidatedOrderItem) => item.validate.check === OK_WORD) ? 'warning' : 'danger') as IColorTypes

        }))


    DEBUG && console.log('ordersRender: ', ordersRender.value)

}


const uploadFile = async () => {

    if (selectedFile.value) {
        // const fileData = await getFileContent(selectedFile.value)

        isDataJson.value = true

        if (isJSON(fileData.value)) {
            // Отправляем в RAW формате и возвращаем результат операции
            // todo сделать проверку на существующие заявки
            const ordersStore = useOrdersStore()
            const res         = await ordersStore.uploadOrders(fileData.value)
            // const res = await ordersStore.uploadOrders(fileData)

            // if (res.length === 0) {
            //     opResultText.value = 'Данные успешно загружены'
            //     opResultType.value = 'success'
            //     setTimeout(() => {
            //         opResult.value = false
            //     }, 5000)
            // } else {
            //     const dubsTextArray = res.map((item) => {
            //         return item['sh'] + ' ' + item['n']
            //     })
            //
            //     // console.log(dubsTextArray)
            //
            //     opResultText.value = 'Дубликат:' + dubsTextArray.join(', ')
            //     opResultType.value = 'danger'
            // }
            // opResult.value = true
            // setTimeout(() => {
            //     opResult.value = false
            // }, 5000)
        } else {
            isDataJson.value = false
            setTimeout(() => {
                isDataJson.value = true
            }, 5000)
        }
    }
}


</script>
