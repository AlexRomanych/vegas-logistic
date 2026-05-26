<template>
    <div v-if="!isLoading" class="ml-2 mt-2">

        <!-- __ Сама таблица -->
        <div class="table-container">

            <div class="flex-table">

                <!-- __ Шапка таблицы -->
                <div class="table-row header-row">

                    <!-- __ Угол -->
                    <div class="sticky-corner">
                        <AppLabelTS
                            :height="HEADER_COLUMNS_HEIGHT"
                            :width="HEADER_ROWS_WIDTH"
                            align="center"
                            rounded="4"
                            text="Добавить схему"
                            text-size="normal"
                            type="primary"
                            @click="addSchema"
                        />
                    </div>

                    <!-- __ Проверка схемы -->
                    <AppLabelTS
                        :height="HEADER_COLUMNS_HEIGHT"
                        :width="CELL_WIDTH"
                        align="center"
                        direction="column"
                        rounded="4"
                        text="Проверка времени схемы"
                        text-size="mini"
                        type="indigo"
                    />

                    <!-- __ Список Типовых операций -->
                    <div v-for="operation of cuttingOperations" :key="operation.id" class="sticky-header">
                        <AppLabelTS
                            :height="HEADER_COLUMNS_HEIGHT"
                            :text="`${operation.name} (${operation.cover_type})`"
                            :type="getOperationType(operation)"
                            :width="CELL_WIDTH"
                            align="center"
                            direction="column"
                            rounded="4"
                            text-size="mini"
                        />
                    </div>

                </div>

                <!-- __ Строки таблицы -->
                <div v-for="schema of cuttingOperationSchemas" :key="schema.name" class="table-row">

                    <!-- __ Шапка строки -->
                    <div class="sticky-col">
                        <AppLabelTS
                            :height="CELL_HEIGHT"
                            :text="schema.name"
                            :width="HEADER_ROWS_WIDTH"
                            align="center"
                            class="cursor-pointer"
                            rounded="4"
                            text-size="mini"
                            type="orange"
                            @dblclick="editSchema(schema)"
                        />
                    </div>

                    <!-- __ Проверка времени схемы -->
                    <div class="sticky-col">
                        <AppLabelTS
                            :height="CELL_HEIGHT"
                            :width="CELL_WIDTH"
                            align="center"
                            class="cursor-pointer"
                            rounded="4"
                            text="📝"
                            text-size="normal"
                            type="indigo"
                            @dblclick="checkSchema(schema)"
                        />
                    </div>

                    <!-- __ Ячейки строки -->
                    <div v-for="operation of cuttingOperations" :key="operation.id" class="flex">
                        <AppLabelTS
                            :height="CELL_HEIGHT"
                            :text="getOperationValue(schema, operation)"
                            :type="getType(schema, operation)"
                            :width="CELL_WIDTH"
                            align="center"
                            rounded="4"
                            text-size="mini"
                            @dblclick="editOperation(schema, operation)"
                        />
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- __ Модальное окно для информации о записи -->
    <CuttingOperationItemEdit
        ref="cuttingOperationItemEdit"
        :operation="modalOperation!"
        :schema="modalSchema!"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
    />

    <!-- __ Модальное окно для редактирования данных Схемы-->
    <CuttingOperationSchemaDataEdit
        ref="cuttingOperationSchemaDataEdit"
        :schema="modalSchema!"
    />

    <!-- __ Модальное окно проверки суммарного времени трудозатрат схемы типовых операций -->
    <CuttingOperationCheck
        ref="cuttingOperationCheck"
        :headers="checkResultsHeaders"
        :schema="checkSchemaData"
        :table-data="checkResultsData"
    />


</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'

import type {
    IColorTypes,
    ICuttingOperation, ICuttingOperationSchema, ICuttingOperationItem, ICuttingOperationUpdateObject,
} from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import CuttingOperationItemEdit
    from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_operations/CuttingOperationItemEdit.vue'
import CuttingOperationSchemaDataEdit
    from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_operations/CuttingOperationSchemaDataEdit.vue'
import CuttingOperationCheck from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_operations/CuttingOperationCheck.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { round } from '@/app/helpers/helpers_lib.ts'
import { DETAIL_PANEL, DETAIL_SIDE } from '@/app/constants/cutting.ts'




const cuttingStore = useCuttingStore()

const isLoading = ref(false)

const DEBUG = true

// __ Права изменения
// const CAN_EDIT   = true
// const CAN_DELETE = true

// __ Константы
const HEADER_COLUMNS_HEIGHT = 'h-[200px]'
const HEADER_ROWS_WIDTH     = 'w-[200px]'
const CELL_WIDTH            = 'w-[50px]'
const CELL_HEIGHT           = 'h-[25px]'

// __ Определяем переменные
const cuttingOperationSchemas = ref<ICuttingOperationSchema[]>([])
const cuttingOperations       = ref<ICuttingOperation[]>([])

// __ Тип для модального окна ячейки
const modalOperation          = ref<ICuttingOperation | null>(null)
const modalSchema             = ref<ICuttingOperationSchema | null>(null)
const cuttingOperationItemEdit = ref<InstanceType<typeof CuttingOperationItemEdit> | null>(null)

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)

// __ Тип для модального окна изменения данных Схемы
const cuttingOperationSchemaDataEdit = ref<InstanceType<typeof CuttingOperationSchemaDataEdit> | null>(null)

// __ Модальное окно проверки суммарного времени трудозатрат схемы типовых операций
const cuttingOperationCheck = ref<InstanceType<typeof CuttingOperationCheck> | null>(null)
const checkResultsHeaders  = ref<string[]>([])
const checkResultsData     = ref<string[][]>([])
const checkSchemaData      = ref<ICuttingOperationSchema | null>(null)


// __ Получаем значение текста для отображения в ячейке
const getOperationValue = (schema: ICuttingOperationSchema, operation: ICuttingOperation) => {
    for (let i = 0; i < schema.operations.length; i++) {
        if (schema.operations[i].id === operation.id) {
            return '✓'
        }
    }
    return ''
}


// __ Получаем раскраску операции
const getOperationType = (operation: ICuttingOperation) => {
    if (!operation.active) {
        return 'danger'
    } else if (operation.detail === DETAIL_SIDE) {
        return 'warning'
    } else if (operation.detail === DETAIL_PANEL) {
        return 'indigo'
    } else {
        return 'stone'
    }

    // if (!operation.active) {
    //     return 'danger'
    // } else if (operation.type === 'static') {
    //     return 'warning'
    // } else {
    //     return 'stone'
    // }
}


// __ Получаем тип ячейки
const getType = (schema: ICuttingOperationSchema, operation: ICuttingOperation) => {
    for (let i = 0; i < schema.operations.length; i++) {
        if (schema.operations[i].id === operation.id) {
            if (!operation.active) {
                return 'danger'
            }
            return schema.operations[i].pivot.ratio === null ? 'success' : 'warning'
        }
    }
    return 'dark'
}

// __ Показываем сообщение об ошибке
const showError = async (error: string | null = null) => {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'
    modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    await appModalAsyncMultiline.value!.show()
}


// __ Редактируем операцию или удаляем или переключаем
const editOperation = async (schema: ICuttingOperationSchema | null, operation: ICuttingOperation) => {
    if (!schema) {
        return
    }
    modalOperation.value = operation
    modalSchema.value    = schema
    const result         = await cuttingOperationItemEdit.value?.show()
    if (result) {
        const present = cuttingOperationItemEdit.value!.present

        // __ Если операция не добавлена (или удалена)
        if (!present) {
            const findIndex = schema.operations.findIndex(item => item.id === operation.id)
            if (findIndex !== -1) {
                const deleteObject: ICuttingOperationUpdateObject = {
                    operation_id: operation.id,
                    target_id   : schema.id,
                    pivot       : null
                }

                const result = await cuttingStore.deleteCuttingOperationFromSchema(deleteObject)

                // __ Если ошибка
                if (!checkCRUD(result)) {
                    await showError()
                }

                schema.operations.splice(findIndex, 1)

            } else {
                return // __ Если нет в схеме и не установили, то выходим
            }
        } else {
            // __ Если операция добавлена или уже есть в схеме

            const ratio     = !!cuttingOperationItemEdit.value!.ratio ? cuttingOperationItemEdit.value!.ratio : null
            const findIndex = schema.operations.findIndex(item => item.id === operation.id)

            if (findIndex !== -1) {
                if (schema.operations[findIndex].pivot.ratio === ratio) {
                    return // __ Если нет изменений, то выходим
                }
            }

            const updateObject: ICuttingOperationUpdateObject = {
                operation_id: operation.id,
                target_id   : schema.id,
                pivot       : {
                    ratio,
                    amount   : null,
                    condition: null,
                    position : null,
                }
            }

            const result = await cuttingStore.addCuttingOperationToSchema(updateObject)

            // __ Если ошибка
            if (!checkCRUD(result)) {
                await showError()
                return
            }

            // __ Добавляем или обновляем
            if (findIndex !== -1) {
                schema.operations[findIndex].pivot.ratio = ratio // __ Если есть в схеме, то обновляем значение
            } else {
                schema.operations.push({
                    id   : operation.id,
                    pivot: {
                        ratio    : ratio,
                        amount   : null,
                        condition: null,
                        position : null
                    }
                } as ICuttingOperationItem)
            }
        }
    }
}


// __ Добавляем схему
const addSchema = async () => {
    const newSchema: ICuttingOperationSchema = {
        id         : 0,
        name       : 'Новая схема',
        active     : true,
        description: '',
        operations : [] as ICuttingOperationItem[],
    }

    const result = await cuttingStore.createCuttingOperationSchema(newSchema)

    if (checkCRUD(result)) {
        cuttingOperationSchemas.value.push(result.data)
    } else {
        await showError(result.error)
        return
    }
}


// __ Редактируем схему (название + описание)
const editSchema = async (schema: ICuttingOperationSchema) => {
    modalSchema.value = schema
    const answer      = await cuttingOperationSchemaDataEdit.value!.show()
    if (answer) {
        const schemaName        = cuttingOperationSchemaDataEdit.value!.name
        const schemaDescription = cuttingOperationSchemaDataEdit.value!.description

        const result = await cuttingStore.updateCuttingOperationSchema({ ...schema, name: schemaName, description: schemaDescription })
        if (checkCRUD(result)) {
            schema.name        = schemaName
            schema.description = schemaDescription
        } else {
            await showError(result.error)
            return
        }

    }
}


// __ Проверяем схему (суммарное время операций)
const checkSchema = async (schema: ICuttingOperationSchema | null = null) => {
    if (!schema) {
        return
    }


    const checkData: { key: string, value: number }[] = await cuttingStore.checkCuttingOperationSchemaForSummaryTime(schema.id)

    // console.log('checkData: ', checkData)

    checkSchemaData.value     = schema
    checkResultsHeaders.value = ['Размер', 'сек./1 ед.', 'мин./1 ед.', 'мин./5 ед.', 'мин./10 ед.',]
    checkResultsData.value    = checkData.map(item => {
        const result: string[] = []
        for (const [key, value] of Object.entries(item)) {
            result.push(key)
            result.push(round(value as number).toString())
            result.push(formatTimeWithLeadingZeros(round(value as number)))
            result.push(formatTimeWithLeadingZeros(round((value as number)) * 5))
            result.push(formatTimeWithLeadingZeros(round((value as number)) * 10))
        }

        return result
    })

    console.log(checkResultsData.value)

    await cuttingOperationCheck.value!.show()
}


// __ Получаем данные
const getData = async () => {
    [cuttingOperations.value, cuttingOperationSchemas.value] = await Promise.all([
        cuttingStore.getCuttingOperations(),
        cuttingStore.getCuttingOperationSchemas(),
    ])

    cuttingOperations.value = cuttingOperations.value
        .map(cuttingOperation => ({ ...cuttingOperation, description: cuttingOperation.description ?? '', can_edit: true }))
        .sort((a, b) => a.id - b.id)
    // .sort((a, b) => a.name.localeCompare(b.name))

    cuttingOperationSchemas.value = cuttingOperationSchemas.value
        .filter(schema => schema.id !== 0)
}


// __ Формируем отображение Типовых операций
// const getDataRender = () => {
//     cuttingOperationsRender.value = cuttingOperations.value
// }

// __ Удаляем типовую операцию
// const deleteOperation = async (cuttingOperation: ICuttingOperation) => {
//     return
// }

// __ Сохраняем данные по цвету
// const saveCuttingOperationColor = async (event: string, cuttingOperation: ICuttingOperation) => {
//     return
//     // await cuttingStore.patchCuttingOperationColor(cuttingOperation.id, event)
// }


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getData()
            // getDataRender()
            if (DEBUG) console.log('cuttingOperationSchemas: ', cuttingOperationSchemas.value)
            if (DEBUG) console.log('cuttingOperations: ', cuttingOperations.value)

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

    /* Высота: из всей высоты экрана вычитаем сумму хедера и футера */
    height: calc(100vh - var(--header-height) - var(--footer-height) - 15px);
    /* Ширина: из всей ширины вычитаем ширину сайдбара */
    width: calc(100vw - var(--sidebar-width) - 15px);

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
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    /*background: white;*/ /* Добавь фон, чтобы не было прозрачности */
    @apply bg-slate-100;
}

.sticky-col {
    position: sticky;
    left: 0;
    z-index: 10;
    /*background: white;*/
    @apply bg-slate-100;
}

.sticky-corner {
    position: sticky;
    left: 0;
    z-index: 30; /* Самый высокий индекс, чтобы не уходить под заголовки колонок */
    /*background: white;*/
    @apply bg-slate-100;
}

.header-row {
    position: sticky;
    top: 0;
    z-index: 20; /* Больше, чем у sticky-col */
    /*background: white;*/ /* Чтобы под ней не просвечивали данные */
    @apply bg-slate-100;
}

</style>
