<template>
    <div v-if="!isLoading" class="ml-2 mt-2">

        <div :style="{ width: '1600px', height: '800px' }" class="table-container">
            <div class="flex-table">
                <div class="table-row header-row">
                    <!--<div class="cell sticky-corner">Схемы \ Операции</div>-->

                    <div class="sticky-corner">
                        <AppLabelTS
                            align="center"
                            height="h-[200px]"
                            rounded="4"
                            text="Добавить схему"
                            text-size="normal"
                            type="primary"
                            width="w-[200px]"
                        />
                    </div>


                    <!-- class="cell sticky-header" -->
                    <div
                        v-for="operation of sewingOperations"
                        :key="operation.id"
                        class="sticky-header"


                    >
                        <AppLabelTS
                            :text="`${operation.name} (${operation.machine})`"
                            align="center"
                            direction="column"
                            height="h-[200px]"
                            rounded="4"
                            text-size="mini"
                            type="stone"
                            width="w-[50px]"
                        />


                    </div>


                    <!--<div-->
                    <!--    v-for="op in schemas[0]?.operations"-->
                    <!--    :key="op.name"-->
                    <!--    class="cell sticky-header"-->
                    <!--&gt;-->
                    <!--    {{ op.name }}-->
                    <!--</div>-->


                </div>

                <div v-for="schema of sewingOperationSchemas" :key="schema.name" class="table-row">


                    <div class="sticky-col">

                        <AppLabelTS
                            :text="schema.name"
                            align="center"
                            height="h-[25px]"
                            rounded="4"
                            text-size="mini"
                            type="orange"
                            width="w-[200px]"
                        />

                    </div>

                    <div v-for="operation of sewingOperations" :key="operation.id" class="flex">


                        <AppLabelTS
                            :text="getOperationValue(schema, operation)"
                            align="center"
                            height="h-[25px]"
                            rounded="4"
                            text-size="mini"
                            :type="getOperationValue(schema, operation) ? 'success' : 'dark'"
                            width="w-[50px]"
                            @click="toggleOperation(schema, operation)"
                        />


                        <!--<div-->
                        <!--    v-for="schemaOperation in schema.operations"-->
                        <!--    :key="schemaOperation.id"-->
                        <!--    class="data-cell"-->
                        <!--&gt;-->
                        <!--    <div >-->

                        <!--        <AppLabelTS-->
                        <!--            :text="schemaOperation.id === operation.id ? schemaOperation.id.toString() : ''"-->
                        <!--            align="center"-->

                        <!--            rounded="4"-->
                        <!--            text-size="mini"-->
                        <!--            type="success"-->
                        <!--            width="w-[50px]"-->
                        <!--        />-->
                        <!--    </div>-->

                        <!--</div>-->

                    </div>
                </div>
            </div>
        </div>

    </div>


    <!--<div v-if="!isLoading" class="ml-2 mt-2">-->
    <!--    <div class="sticky top-0 p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-400 max-w-fit">-->
    <!--        <div>-->
    <!--            <div class="flex ml-0.5">-->

    <!--                &lt;!&ndash; __ id &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.id"/>-->
    <!--                    <AppInputTextTSWrapper v-model="idFilter" :render-object="render.id"/>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; __ Название &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.name"/>-->
    <!--                    <AppInputTextTSWrapper v-model="nameFilter" :render-object="render.name"/>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; __ Оборудование &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.machine"/>-->
    <!--                    <AppInputTextTSWrapper v-model="machineFilter" :render-object="render.machine"/>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; __ Active &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.active"/>-->

    <!--                    &lt;!&ndash; __ Фильтр: Active &ndash;&gt;-->
    <!--                    <AppSelectSimpleTS-->
    <!--                        v-if="render.active.show"-->
    <!--                        id="active"-->
    <!--                        :select-data="activeSelect"-->
    <!--                        :text-size="render.active.headerTextSize"-->
    <!--                        :type="-->
    <!--                            activeFilter === 0-->
    <!--                            ? 'primary'-->
    <!--                            : activeFilter === 1-->
    <!--                                ? 'success'-->
    <!--                                : 'danger'-->
    <!--                        "-->
    <!--                        :width="render.active.width"-->
    <!--                        align="center"-->
    <!--                        class="mt-[8px]"-->
    <!--                        height="h-[30px]"-->
    <!--                        @change="filterByActive"-->
    <!--                    />-->
    <!--                </div>-->

    <!--                &lt;!&ndash; __ Тип расчета (Динамический, Статический, ...) &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.type"/>-->
    <!--                    <AppInputTextTSWrapper v-model="typeFilter" :render-object="render.type"/>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; __ Время операции &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.time"/>-->
    <!--                    <AppInputTextTSWrapper v-model="timeFilter" :render-object="render.time"/>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; __ Цвет &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.color"/>-->
    <!--                    <AppInputTextTSWrapper v-model="colorFilter" :render-object="render.color"/>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; __ Описание &ndash;&gt;-->
    <!--                <div>-->
    <!--                    <AppLabelMultilineTSWrapper :render-object="render.description"/>-->
    <!--                    <AppInputTextTSWrapper v-model="descriptionFilter" :render-object="render.description"/>-->
    <!--                </div>-->

    <!--                <div>-->
    <!--                    &lt;!&ndash; __ + Типовая операция &ndash;&gt;-->
    <!--                    <router-link :to="{ name: 'manufacture.cell.sewing.operations.create' }">-->
    <!--                        <AppLabelMultiLineTS-->
    <!--                            :text="['➕', '']"-->
    <!--                            align="center"-->
    <!--                            class="cursor-pointer"-->
    <!--                            rounded="4"-->
    <!--                            text-size="large"-->
    <!--                            type="warning"-->
    <!--                            width="w-[64px]"-->
    <!--                        />-->
    <!--                    </router-link>-->

    <!--                    &lt;!&ndash; __ Сброс фильтров &ndash;&gt;-->
    <!--                    <div class=" mt-[8px]">-->
    <!--                        <AppLabelTS-->
    <!--                            id="filters-reset"-->
    <!--                            align="center"-->
    <!--                            class="cursor-pointer"-->
    <!--                            height="h-[26px]"-->
    <!--                            rounded="4"-->
    <!--                            text="Очистить"-->
    <!--                            text-size="mini"-->
    <!--                            type="orange"-->
    <!--                            width="w-[64px]"-->
    <!--                            @click="resetFilters"-->
    <!--                        />-->
    <!--                    </div>-->
    <!--                </div>-->

    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    &lt;!&ndash; __ Данные &ndash;&gt;-->
    <!--    <div v-for="sewingOperation of sewingOperationsRender" :key="sewingOperation.id" class="ml-2 max-w-fit">-->
    <!--        <div class="flex ">-->

    <!--            &lt;!&ndash; __ id &ndash;&gt;-->
    <!--            <AppLabelTSWrapper :arg="sewingOperation" :render-object="render.id"/>-->

    <!--            &lt;!&ndash; __ Название &ndash;&gt;-->
    <!--            <AppLabelTSWrapper :arg="sewingOperation" :render-object="render.name"/>-->

    <!--            &lt;!&ndash; __ Оборудование &ndash;&gt;-->
    <!--            <AppLabelTSWrapper :arg="sewingOperation" :render-object="render.machine"/>-->

    <!--            &lt;!&ndash; __ Active &ndash;&gt;-->
    <!--            <AppLabelTSWrapper :arg="sewingOperation" :render-object="render.active"/>-->

    <!--            &lt;!&ndash; __ Тип расчета (Динамический, Статический, ...) &ndash;&gt;-->
    <!--            <AppLabelTSWrapper :arg="sewingOperation" :render-object="render.type"/>-->

    <!--            &lt;!&ndash; __ Время операции &ndash;&gt;-->
    <!--            <AppLabelTSWrapper :arg="sewingOperation" :render-object="render.time"/>-->

    <!--            &lt;!&ndash; __ Цвет (Picker) &ndash;&gt;-->
    <!--            <AppRGBPickerModalTS-->
    <!--                v-if="render.color.show"-->
    <!--                v-model="sewingOperation.color"-->
    <!--                @confirm="saveSewingOperationColor($event, sewingOperation)"-->
    <!--            />-->

    <!--            &lt;!&ndash; __ Описание &ndash;&gt;-->
    <!--            <AppLabelTSWrapper :arg="sewingOperation" :render-object="render.description"/>-->

    <!--            &lt;!&ndash; __ Удалить &ndash;&gt;-->
    <!--            <AppLabelTS-->
    <!--                v-if="CAN_DELETE"-->
    <!--                align="center"-->
    <!--                text="🗑️"-->
    <!--                text-size="mini"-->
    <!--                type="danger"-->
    <!--                width="w-[30px]"-->
    <!--                @click="deleteOperation(sewingOperation)"-->
    <!--            />-->

    <!--            &lt;!&ndash; __ Редактировать &ndash;&gt;-->
    <!--            <router-link-->
    <!--                :to="{ name: 'manufacture.cell.sewing.operations.edit', params: { id: sewingOperation.id } }">-->
    <!--                <AppLabelTS-->
    <!--                    v-if="CAN_EDIT"-->
    <!--                    align="center"-->
    <!--                    text="✏️"-->
    <!--                    text-size="mini"-->
    <!--                    type="warning"-->
    <!--                    width="w-[30px]"-->
    <!--                />-->
    <!--            </router-link>-->

    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watchEffect } from 'vue'

import type {
    IRenderData, ISelectData, ISelectDataItem, ISewingOperation, ISewingOperationSchema,
} from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import AppLabelMultilineTSWrapper
    from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/components/AppInputTextTSWrapper.vue'
import AppRGBPickerModalTS from '@/components/ui/pickers/AppRGBPickerModalTS.vue'
import AppSelectSimpleTS from '@/components/ui/selects/AppSelectSimpleTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'


const getOperationValue = (schema: ISewingOperationSchema, operation: ISewingOperation) => {
    for (let i = 0; i < schema.operations.length; i++) {
        if (schema.operations[i].id === operation.id) {
            // return schema.operations[i].id
            return '✓'
        }
    }

    return ''
}


const toggleOperation = (schema: ISewingOperationSchema, operation: ISewingOperation) => {
    for (let i = 0; i < schema.operations.length; i++) {
        if (schema.operations[i].id === operation.id) {
            schema.operations.splice(i, 1)
            return
        }
    }

    schema.operations.push({ id: operation.id })
}




const isLoading = ref(false)

const sewingStore = useSewingStore()

const DEBUG = true

// __ Права изменения
const CAN_EDIT   = true
const CAN_DELETE = true

// __ Определяем переменные
const sewingOperationSchemas = ref<ISewingOperationSchema[]>([])
const sewingOperations       = ref<ISewingOperation[]>([])
const sewingOperationsRender = ref<ISewingOperation[]>([])

// __ Объект отображения данных
const DEFAULT_WIDTH_BOOL = 'w-[70px]'
const DEFAULT_HEIGHT     = 'h-[30px]'
const HEADER_TYPE        = 'primary'
const DATA_TYPE          = 'primary'
const DEFAULT_TYPE       = 'dark'
const HEADER_TEXT_SIZE   = 'mini'
const DATA_TEXT_SIZE     = 'micro'
const HEADER_ALIGN       = 'center'
const DATA_ALIGN         = 'left'
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const DEFAULT_WIDTH_DATE = 'w-[100px]'
// const DATA_ALIGN_DEFAULT = 'center'

const render: IRenderData = reactive({
    id:      {
        id:         () => 'id-search',
        header:     ['ID', ''],
        width:      'w-[50px]',
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍id...',
        data:           (sewingOperation: ISewingOperation) => sewingOperation.id.toString()
    },
    name:    {
        id:         () => 'name-search',
        header:     ['Название', 'типовой операции'],
        width:      'w-[300px]',
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Название...',
        data:           (sewingOperation: ISewingOperation) => sewingOperation.name
    },
    machine: {
        id:         () => 'machine-search',
        header:     ['Оборудо-', 'вание'],
        width:      'w-[100px]',
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Обор-ние...',
        data:           (sewingOperation: ISewingOperation) => sewingOperation.machine
    },
    active:  {
        id:         () => 'active-search',
        header:     ['Актуаль-', 'ность'],
        width:      DEFAULT_WIDTH_BOOL,
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       (sewingOperation: ISewingOperation) => sewingOperation.active ? 'success' : 'danger',
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Active...',
        data:           (sewingOperation: ISewingOperation) => sewingOperation.active ? '✓' : '✗'
    },


    type:        {
        id:         () => 'type-search',
        header:     ['Тип', 'расчета'],
        width:      'w-[100px]',
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Тип...',
        data:           (sewingOperation: ISewingOperation) => {
            switch (sewingOperation.type) {
                case 'dynamic':
                    return 'Динамический'
                case 'static':
                    return 'Статический'
            }
        }
    },
    time:        {
        id:         () => 'time-search',
        header:     ['Время', 'операции, сек.'],
        width:      'w-[100px]',
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Время...',
        data:           (sewingOperation: ISewingOperation) => sewingOperation.time.toString()
    },
    color:       {
        id:         () => 'color-search',
        header:     ['Цвет', 'ярлычка'],
        width:      'w-[100px]',
        height:     DEFAULT_HEIGHT,
        show:       false,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      'center',
        placeholder:    '🔍Цвет...',
        data:           (sewingOperation: ISewingOperation) => sewingOperation.color,
        class:          'cursor-pointer'
    },
    description: {  // __ Описание Заявки
        id:         () => 'description-search',
        header:     ['Описание', ''],
        width:      'w-[450px]',
        height:     DEFAULT_HEIGHT,
        show:       true,
        headerType: () => HEADER_TYPE,
        dataType:   () => DATA_TYPE,
        type:       () => DEFAULT_TYPE,
        // color:          (sewingOperation: ISewingOperation) => sewingOperation.color,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize:   DATA_TEXT_SIZE,
        headerAlign:    HEADER_ALIGN,
        dataAlign:      DATA_ALIGN,
        placeholder:    '🔍Описание...',
        data:           (sewingOperation: ISewingOperation) => sewingOperation.description ?? ''
    },
})

// __ Фильтры
const idFilter          = ref('')
const nameFilter        = ref('')
const machineFilter     = ref('')
const typeFilter        = ref('')
const timeFilter        = ref('')
const colorFilter       = ref('')
const descriptionFilter = ref('')
const activeFilter      = ref(0)

// __ Подготавливаем селекты
const activeSelect: ISelectData = {
    name: 'order-active',
    data: [
        { id: 0, name: 'Все', selected: true, disabled: false },
        { id: 1, name: '✓', selected: false, disabled: false },
        { id: 2, name: '✗', selected: false, disabled: false },
    ],
}

// __ Обрабатываем селекты
const filterByActive = (value: ISelectDataItem) => {
    activeFilter.value = value.id
}

// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value          = ''
    nameFilter.value        = ''
    machineFilter.value     = ''
    typeFilter.value        = ''
    timeFilter.value        = ''
    colorFilter.value       = ''
    descriptionFilter.value = ''
    activeFilter.value      = 0
}


// __ Получаем данные
const getData = async () => {
    [sewingOperations.value, sewingOperationSchemas.value] = await Promise.all([
        sewingStore.getSewingOperations(),
        sewingStore.getSewingOperationSchemas(),
    ])


    // sewingOperationSchemas.value = await sewingStore.getSewingOperationSchemas()
    // sewingOperations.value = await sewingStore.getSewingOperations()
    sewingOperations.value = sewingOperations.value
        .map(sewingOperation => ({ ...sewingOperation, description: sewingOperation.description ?? '', can_edit: true }))
        .sort((a, b) => a.id - b.id)
}


// __ Формируем отображение Типовых операций
const getDataRender = () => {
    sewingOperationsRender.value = sewingOperations.value
}

// __ Удаляем типовую операцию
const deleteOperation = async (sewingOperation: ISewingOperation) => {
    return
}

// __ Сохраняем данные по цвету
const saveSewingOperationColor = async (event: string, sewingOperation: ISewingOperation) => {
    return
    // await sewingStore.patchSewingOperationColor(sewingOperation.id, event)
}


// __ Реализация фильтров
watchEffect(() => {
    sewingOperationsRender.value = sewingOperations.value
        .filter(orderType => orderType.id.toString().toLowerCase().includes(idFilter.value.toLowerCase()))
        .filter(orderType => orderType.name.toLowerCase().includes(nameFilter.value.toLowerCase()))
        .filter(orderType => orderType.machine.toLowerCase().includes(machineFilter.value.toLowerCase()))
        .filter(orderType => {
            const calcType = orderType.type === 'dynamic' ? 'Динамический' : 'Статический'
            return calcType.toLowerCase().includes(typeFilter.value.toLowerCase())
        })
        .filter(orderType => orderType.time.toString().includes(timeFilter.value.toLowerCase()))
        .filter(orderType => orderType.color.toLowerCase().includes(colorFilter.value.toLowerCase()))
        .filter(orderType => orderType.description!.toLowerCase().includes(descriptionFilter.value.toLowerCase()))
        .filter(order => {
            if (activeFilter.value === 0) return true
            else if (activeFilter.value === 1) return order.active
            else if (activeFilter.value === 2) return !order.active
        })
    return
})


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getData()
            getDataRender()
            if (DEBUG) console.log('sewingOperationSchemas: ', sewingOperationSchemas.value)
            if (DEBUG) console.log('sewingOperations: ', sewingOperations.value)

        },
        undefined,
        // false,
    )

    isLoading.value = false
})

</script>

<style scoped>

.table-container {
    overflow: auto; /* Включаем скролл для всего контейнера */
    position: relative;
    @apply border-2 border-gray-300 rounded-md p-1;
}

.flex-table {
    display: flex;
    flex-direction: column;
    min-width: max-content; /* Важно: чтобы строки не сжимались меньше контента */
}

.table-row {
    display: flex;
}

.sticky-header {
    /* position: sticky;  <-- Это можно убрать, если родитель sticky */
    /* top: 0;           <-- Это теперь не нужно здесь */
    display: flex;
    align-items: center;
    justify-content: center;
    background: white; /* Добавь фон, чтобы не было прозрачности */


    /*    position: sticky;
        top: 0;*/
    z-index: 10;

    /*    display: flex;
        align-items: center;
        justify-content: center;*/
}

.sticky-col {
    position: sticky;
    left: 0;
    z-index: 10;
    background: white;

}

.sticky-corner {

    position: sticky;
    left: 0;
    z-index: 30; /* Самый высокий индекс, чтобы не уходить под заголовки колонок */
    background: white;


    /*    position: sticky;
        top: 0;
        left: 0;
        z-index: 20;
        box-sizing: border-box; !* Важно! *!*/
}

.header-row {
    position: sticky;
    top: 0;
    z-index: 20; /* Больше, чем у sticky-col */
    background: white; /* Чтобы под ней не просвечивали данные */
    /*background: #f8f9fa;*/
}

</style>
