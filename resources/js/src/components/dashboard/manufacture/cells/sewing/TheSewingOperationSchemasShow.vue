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

                    <!-- __ Список Типовых операций -->
                    <div v-for="operation of sewingOperations" :key="operation.id" class="sticky-header">
                        <AppLabelTS
                            :height="HEADER_COLUMNS_HEIGHT"
                            :text="`${operation.name} (${operation.machine})`"
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
                <div v-for="schema of sewingOperationSchemas" :key="schema.name" class="table-row">

                    <!-- __ Шапка строки -->
                    <div class="sticky-col">
                        <AppLabelTS
                            :height="CELL_HEIGHT"
                            :text="schema.name"
                            :width="HEADER_ROWS_WIDTH"
                            align="center"
                            rounded="4"
                            text-size="mini"
                            type="orange"
                            @dblclick="editSchema(schema)"
                        />
                    </div>

                    <!-- __ Ячейки строки -->
                    <div v-for="operation of sewingOperations" :key="operation.id" class="flex">
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
    <SewingOperationItemEdit
        ref="sewingOperationItemEdit"
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
    <SewingOperationSchemaDataEdit
        ref="sewingOperationSchemaDataEdit"
        :schema="modalSchema!"
    />

</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'

import type {
    IColorTypes,
    ISewingOperation, ISewingOperationSchema, ISewingOperationItem, ISewingOperationUpdateObject,
} from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import SewingOperationItemEdit
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_operations/SewingOperationItemEdit.vue'
import SewingOperationSchemaDataEdit
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_operations/SewingOperationSchemaDataEdit.vue'


// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'


const sewingStore = useSewingStore()

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
const sewingOperationSchemas = ref<ISewingOperationSchema[]>([])
const sewingOperations       = ref<ISewingOperation[]>([])

// __ Тип для модального окна ячейки
const modalOperation          = ref<ISewingOperation | null>(null)
const modalSchema             = ref<ISewingOperationSchema | null>(null)
const sewingOperationItemEdit = ref<InstanceType<typeof SewingOperationItemEdit> | null>(null)

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)

// __ Тип для модального окна изменения данных Схемы
const sewingOperationSchemaDataEdit = ref<InstanceType<typeof SewingOperationSchemaDataEdit> | null>(null)


// __ Получаем значение текста для отображения в ячейке
const getOperationValue = (schema: ISewingOperationSchema, operation: ISewingOperation) => {
    for (let i = 0; i < schema.operations.length; i++) {
        if (schema.operations[i].id === operation.id) {
            return '✓'
        }
    }
    return ''
}


// __ Получаем раскраску операции
const getOperationType = (operation: ISewingOperation) => {
    if (!operation.active) {
        return 'danger'
    } else if (operation.type === 'static') {
        return 'warning'
    } else {
        return 'stone'
    }
}


// __ Получаем тип ячейки
const getType = (schema: ISewingOperationSchema, operation: ISewingOperation) => {
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
const editOperation = async (schema: ISewingOperationSchema, operation: ISewingOperation) => {
    modalOperation.value = operation
    modalSchema.value    = schema
    const result         = await sewingOperationItemEdit.value?.show()
    if (result) {
        const present = sewingOperationItemEdit.value!.present

        // __ Если операция не добавлена (или удалена)
        if (!present) {
            const findIndex = schema.operations.findIndex(item => item.id === operation.id)
            if (findIndex !== -1) {
                const deleteObject: ISewingOperationUpdateObject = {
                    operation_id: operation.id,
                    target_id:    schema.id,
                    pivot:        null
                }

                const result = await sewingStore.deleteSewingOperationFromSchema(deleteObject)

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

            const ratio     = !!sewingOperationItemEdit.value!.ratio ? sewingOperationItemEdit.value!.ratio : null
            const findIndex = schema.operations.findIndex(item => item.id === operation.id)

            if (findIndex !== -1) {
                if (schema.operations[findIndex].pivot.ratio === ratio) {
                    return // __ Если нет изменений, то выходим
                }
            }

            const updateObject: ISewingOperationUpdateObject = {
                operation_id: operation.id,
                target_id:    schema.id,
                pivot:        {
                    ratio,
                    amount:    null,
                    condition: null,
                    position:  null,
                }
            }

            const result = await sewingStore.addSewingOperationToSchema(updateObject)

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
                    id:    operation.id,
                    pivot: {
                        ratio:     ratio,
                        amount:    null,
                        condition: null,
                        position:  null
                    }
                } as ISewingOperationItem)
            }
        }
    }
}


// __ Добавляем схему
const addSchema = async () => {
    const newSchema: ISewingOperationSchema = {
        id:          0,
        name:        'Новая схема',
        active:      true,
        description: '',
        operations:  [] as ISewingOperationItem[],
    }

    const result = await sewingStore.createSewingOperationSchema(newSchema)

    if (checkCRUD(result)) {
        sewingOperationSchemas.value.push(result.data)
    } else {
        await showError(result.error)
        return
    }
}


// __ Редактируем схему (название + описание)
const editSchema = async (schema: ISewingOperationSchema) => {
    modalSchema.value = schema
    const answer      = await sewingOperationSchemaDataEdit.value!.show()
    if (answer) {
        const schemaName        = sewingOperationSchemaDataEdit.value!.name
        const schemaDescription = sewingOperationSchemaDataEdit.value!.description

        const result = await sewingStore.updateSewingOperationSchema({ ...schema, name: schemaName, description: schemaDescription })
        if (checkCRUD(result)) {
            schema.name        = schemaName
            schema.description = schemaDescription
        } else {
            await showError(result.error)
            return
        }

    }
}


// __ Получаем данные
const getData = async () => {
    [sewingOperations.value, sewingOperationSchemas.value] = await Promise.all([
        sewingStore.getSewingOperations(),
        sewingStore.getSewingOperationSchemas(),
    ])

    sewingOperations.value = sewingOperations.value
        .map(sewingOperation => ({ ...sewingOperation, description: sewingOperation.description ?? '', can_edit: true }))
        .sort((a, b) => a.id - b.id)
        // .sort((a, b) => a.name.localeCompare(b.name))

    sewingOperationSchemas.value = sewingOperationSchemas.value
        .filter(schema => schema.id !== 0)
}


// __ Формируем отображение Типовых операций
// const getDataRender = () => {
//     sewingOperationsRender.value = sewingOperations.value
// }

// __ Удаляем типовую операцию
// const deleteOperation = async (sewingOperation: ISewingOperation) => {
//     return
// }

// __ Сохраняем данные по цвету
// const saveSewingOperationColor = async (event: string, sewingOperation: ISewingOperation) => {
//     return
//     // await sewingStore.patchSewingOperationColor(sewingOperation.id, event)
// }


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getData()
            // getDataRender()
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
