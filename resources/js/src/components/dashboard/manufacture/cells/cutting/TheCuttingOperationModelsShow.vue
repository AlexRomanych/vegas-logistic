<template>
    <!--<AppModalAsyncSelectTS-->
    <!--    v-model="selectedSchemaId"-->
    <!--    :items="test()"-->
    <!--    width="w-[600px]"-->
    <!--    placeholder="Выберите схему"-->
    <!--    @change="console.log(123)"-->
    <!--/>-->

    <div
        v-if="!isLoading"
        class="ml-2 mt-2"
    >
        <!-- __ Сама таблица -->
        <div class="table-container">
            <div class="flex-table">
                <!-- __ Шапка таблицы -->
                <div class="table-row header-row">
                    <!-- __ Угол -->
                    <div class="sticky-corner">
                        <div class="flex">
                            <!-- __  Заголовок 'Модели / Операции' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :width="HEADER_ROWS_WIDTH"
                                align="center"
                                rounded="4"
                                text="Модели / Операции"
                                text-size="normal"
                                type="primary"
                            />

                            <!-- __  Заголовок 'Схема' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                align="center"
                                rounded="4"
                                text="Схема"
                                text-size="normal"
                                type="success"
                                width="w-[150px]"
                            />
                        </div>

                        <div class="flex">
                            <!-- __  Свернуть все '▲' -->
                            <AppLabelTS
                                :height="CELL_HEIGHT"
                                :width="'w-[202px]'"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text="▲"
                                text-size="large"
                                type="indigo"
                                @click="collapseAll"
                            />

                            <!-- __  Развернуть все '▼' -->
                            <AppLabelTS
                                :height="CELL_HEIGHT"
                                :width="'w-[202px]'"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text="▼"
                                text-size="large"
                                type="indigo"
                                @click="expandAll"
                            />
                        </div>

                        <div class="flex mt-[-6px] mb-[2px]">
                            <!-- __ Фильтр: Код 1С -->
                            <AppInputTextTS
                                id="code-1c-search"
                                v-model:text-value.trim="codeFilter"
                                :width="MODEL_CODE_1C_WIDTH"
                                placeholder="🔍Код..."
                                text-size="mini"
                                type="orange"
                            />

                            <!-- __ Фильтр: Название модели -->
                            <AppInputTextTS
                                id="name-search"
                                v-model:text-value.trim="nameFilter"
                                :width="MODEL_NAME_WIDTH"
                                placeholder="🔍Название..."
                                text-size="mini"
                                type="orange"
                            />

                            <!-- __ Фильтр: Схема Типовых операций -->
                            <AppInputTextTS
                                id="schema-search"
                                v-model:text-value.trim="schemaFilter"
                                :width="SCHEMA_WIDTH"
                                placeholder="🔍Схема..."
                                text-size="mini"
                                type="success"
                            />
                        </div>
                    </div>

                    <!-- __ Список Типовых операций -->
                    <div
                        v-for="operation of sewingOperations"
                        :key="operation.id"
                        class="sticky-header"
                    >
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
                <div
                    v-for="collection of modelsRender"
                    :key="collection.collection"
                >
                    <!-- __ Шапка строки (коллекции) -->
                    <div class="table-row">
                        <div class="sticky-col">
                            <div class="flex">
                                <!-- __ '▲' : '▼' с анимацией -->
                                <AppLabelTS
                                    :class="{ 'rotate-180': !collection.collapsed }"
                                    :height="CELL_HEIGHT"
                                    :width="COLLECTION_COLLAPSE_WIDTH"
                                    align="center"
                                    class="cursor-pointer transition-transform duration-300"
                                    rounded="4"
                                    text="▼"
                                    text-size="mini"
                                    type="indigo"
                                    @click="collection.collapsed = !collection.collapsed"
                                />

                                <!--&lt;!&ndash; __ '▲' : '▼' &ndash;&gt;-->
                                <!--<AppLabelTS-->
                                <!--    :height="CELL_HEIGHT"-->
                                <!--    :text="collection.collapsed ? '▲' : '▼'"-->
                                <!--    :width="COLLECTION_COLLAPSE_WIDTH"-->
                                <!--    align="center"-->
                                <!--    rounded="4"-->
                                <!--    text-size="mini"-->
                                <!--    type="indigo"-->
                                <!--    @click="collection.collapsed = !collection.collapsed"-->
                                <!--/>-->

                                <!-- __ Название коллекции -->
                                <AppLabelTS
                                    :height="CELL_HEIGHT"
                                    :text="collection.collection"
                                    :width="COLLECTION_NAME_WIDTH"
                                    align="left"
                                    class="cursor-pointer"
                                    rounded="4"
                                    text-size="mini"
                                    type="indigo"
                                    @click="collection.collapsed = !collection.collapsed"
                                />
                            </div>
                        </div>
                    </div>

                    <div v-if="!collection.collapsed">
                        <div
                            v-for="model of collection.items"
                            :key="model.code_1c"
                            class="table-row"
                        >
                            <!-- __ Шапка строки -->
                            <div class="sticky-col">
                                <div class="flex">
                                    <!-- __ Код по 1С -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="model.code_1c"
                                        :width="MODEL_CODE_1C_WIDTH"
                                        align="center"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        type="stone"
                                    />

                                    <!-- __ Название модели -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="model.name_report ?? model.name"
                                        :width="MODEL_NAME_WIDTH"
                                        align="left"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        type="stone"
                                    />

                                    <!-- __ Название Схемы -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="getSchemaName(model)"
                                        :type="model.sewing_schema_id ? 'success' : 'danger'"
                                        :width="SCHEMA_WIDTH"
                                        align="left"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        @dblclick="selectSchema(model)"
                                    />
                                </div>
                            </div>

                            <!-- __ Ячейки строки -->
                            <div
                                v-for="operation of sewingOperations"
                                :key="operation.id"
                                class="flex"
                            >
                                <AppLabelTS
                                    :height="CELL_HEIGHT"
                                    :text="getOperationValue(model, operation)"
                                    :type="getType(model, operation)"
                                    :width="CELL_WIDTH"
                                    align="center"
                                    rounded="4"
                                    text-size="mini"
                                    @dblclick="editOperation(model, operation)"
                                />
                            </div>
                        </div>
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

    <!-- __ Выпадающий список-->
    <AppModalAsyncSelectTS
        ref="appModalAsyncSelectTS"
        v-model="selectedSchemaId"
        :items="sewingOperationSchemas"
        title="Выберите схему"
        width="w-[600px]"
    />
</template>

<script lang="ts" setup>
    import { onMounted, ref, watchEffect } from 'vue'

    import type {
        IColorTypes,
        ISewingOperation,
        ISewingOperationModelsCollection,
        ISewingOperationSchema,
        ISewingOperationItem,
        ISewingOperationUpdateObject,
        ISewingOperationModel,
    } from '@/types'

    import { useSewingStore } from '@/stores/SewingStore.ts'

    import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

    import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
    import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
    import SewingOperationItemEdit from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_operations/SewingOperationItemEdit.vue'
    import SewingOperationSchemaDataEdit from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_operations/SewingOperationSchemaDataEdit.vue'
    import AppModalAsyncSelectTS from '@/components/ui/modals/AppModalAsyncSelectTS.vue'
    import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'

    // __ Loader
    import { useLoading } from 'vue-loading-overlay'
    import { loaderHandler } from '@/app/helpers/helpers_render.ts'

    const sewingStore = useSewingStore()

    const isLoading = ref(false)

    const DEBUG = true

    // __ Права изменения
    const CAN_EDIT = true
    const CAN_DELETE = true

    // __ Константы
    const HEADER_COLUMNS_HEIGHT = 'h-[200px]'
    const HEADER_ROWS_WIDTH = 'w-[254px]'

    const COLLECTION_COLLAPSE_WIDTH = 'w-[40px]'
    const COLLECTION_NAME_WIDTH = 'w-[364px]'

    const MODEL_CODE_1C_WIDTH = 'w-[70px]'
    const MODEL_NAME_WIDTH = 'w-[180px]'
    const SCHEMA_WIDTH = 'w-[150px]'

    const CELL_WIDTH = 'w-[50px]'
    const CELL_HEIGHT = 'h-[30px]'

    // __ Определяем переменные
    const sewingOperationSchemas = ref<ISewingOperationSchema[]>([])
    const sewingOperations = ref<ISewingOperation[]>([])
    const models = ref<ISewingOperationModelsCollection[]>([])
    const modelsRender = ref<ISewingOperationModelsCollection[]>([])

    // __ Фильтр
    const codeFilter = ref('')
    const nameFilter = ref('')
    const schemaFilter = ref('')

    // __ Тип для модального окна ячейки
    const modalOperation = ref<ISewingOperation | null>(null)
    const modalSchema = ref<ISewingOperationSchema | null>(null)
    const sewingOperationItemEdit = ref<InstanceType<typeof SewingOperationItemEdit> | null>(null)

    // __ Тип для модального окна Сообщений
    const modalInfoType = ref<IColorTypes>('danger')
    const modalInfoText = ref<string | string[]>('')
    const modalInfoMode = ref<'inform' | 'confirm'>('confirm')
    const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)

    // __ Тип для модального окна изменения данных Схемы
    const sewingOperationSchemaDataEdit = ref<InstanceType<typeof SewingOperationSchemaDataEdit> | null>(null)

    // __ Тип для модального окна выбора Схемы
    const selectedSchemaId = ref<number | null>(null)
    const appModalAsyncSelectTS = ref<any>(null)
    // const appModalAsyncSelectTS = ref<InstanceType<typeof AppModalAsyncSelectTS> | null>(null)

    // __ Вспомогательная. Возвращает массив операций в зависимости от схемы или ее отсутствия
    const getTargetOperations = (model: ISewingOperationModel) => {
        let targetOperations: ISewingOperationItem[] = model.operations
        if (model.sewing_schema_id) {
            const schema = sewingOperationSchemas.value.find((schema) => schema.id === model.sewing_schema_id)
            if (schema) {
                targetOperations = schema.operations
            }
        }
        return targetOperations
    }

    // __ Получаем значение текста для отображения в ячейке
    const getOperationValue = (model: ISewingOperationModel, operation: ISewingOperation) => {
        const targetOperations = getTargetOperations(model)
        for (let i = 0; i < targetOperations.length; i++) {
            if (targetOperations[i].id === operation.id) {
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
    const getType = (model: ISewingOperationModel, operation: ISewingOperation) => {
        const targetOperations = getTargetOperations(model)
        for (let i = 0; i < targetOperations.length; i++) {
            if (targetOperations[i].id === operation.id) {
                if (!operation.active) {
                    return 'danger'
                }
                return targetOperations[i].pivot.ratio === null ? 'success' : 'warning'
            }
        }
        return 'dark'
    }

    const getSchemaName = (model: ISewingOperationModel) => {
        const schema = sewingOperationSchemas.value.find((schema) => schema.id === model.sewing_schema_id)
        return schema ? schema.name : ''
    }

    // __ Показываем сообщение об ошибке
    const showError = async (error: string | null = null) => {
        modalInfoType.value = 'danger'
        modalInfoMode.value = 'inform'
        modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
        await appModalAsyncMultiline.value!.show()
    }

    // __ Редактируем операцию или удаляем или переключаем
    const editOperation = async (model: ISewingOperationModel, operation: ISewingOperation) => {
        if (model.sewing_schema_id !== 0) {
            return
        }

        // __ Тут по идее всегда будет 0 (Без схемы)
        const schema = sewingOperationSchemas.value.find((schema) => schema.id === model.sewing_schema_id)
        if (!schema) {
            return
        }
        const sourceSchema = JSON.parse(JSON.stringify(schema))
        sourceSchema.operations = model.operations

        modalOperation.value = operation
        modalSchema.value = sourceSchema

        const result = await sewingOperationItemEdit.value?.show()
        if (result) {
            const present = sewingOperationItemEdit.value!.present

            // __ Если операция не добавлена (или удалена)
            if (!present) {
                const findIndex = model.operations.findIndex((item) => item.id === operation.id)
                if (findIndex !== -1) {
                    const deleteObject: ISewingOperationUpdateObject = {
                        operation_id: operation.id,
                        target_id: model.code_1c,
                        pivot: null,
                    }

                    const result = await sewingStore.deleteSewingOperationFromModel(deleteObject)

                    // __ Если ошибка
                    if (!checkCRUD(result)) {
                        await showError()
                    }

                    model.operations.splice(findIndex, 1)
                } else {
                    return // __ Если нет в схеме и не установили, то выходим
                }
            } else {
                // __ Если операция добавлена или уже есть в схеме

                const ratio = !!sewingOperationItemEdit.value!.ratio ? sewingOperationItemEdit.value!.ratio : null
                const findIndex = model.operations.findIndex((item) => item.id === operation.id)

                if (findIndex !== -1) {
                    if (model.operations[findIndex].pivot.ratio === ratio) {
                        return // __ Если нет изменений, то выходим
                    }
                }

                const updateObject: ISewingOperationUpdateObject = {
                    operation_id: operation.id,
                    target_id: model.code_1c,
                    pivot: {
                        ratio,
                        amount: null,
                        condition: null,
                        position: null,
                    },
                }

                const result = await sewingStore.addSewingOperationToModel(updateObject)

                // __ Если ошибка
                if (!checkCRUD(result)) {
                    await showError()
                    return
                }

                // __ Добавляем или обновляем
                if (findIndex !== -1) {
                    model.operations[findIndex].pivot.ratio = ratio // __ Если есть в схеме, то обновляем значение
                } else {
                    model.operations.push({
                        id: operation.id,
                        pivot: {
                            ratio: ratio,
                            amount: null,
                            condition: null,
                            position: null,
                        },
                    } as ISewingOperationItem)
                }
            }
        }
    }

    // __ Выбираем схему для модели
    const selectSchema = async (model: ISewingOperationModel) => {
        selectedSchemaId.value = model.sewing_schema_id
        const answer = await appModalAsyncSelectTS.value!.show(selectedSchemaId.value)
        if (answer) {
            const selectedSchema = appModalAsyncSelectTS.value!.selected
            if (selectedSchema.id === model.sewing_schema_id) {
                return
            }

            const result = await sewingStore.updateModelSewingOperationSchema(model.code_1c, selectedSchema.id)

            if (checkCRUD(result)) {
                model.sewing_schema_id = selectedSchema.id
            } else {
                await showError()
                return
            }
        }
    }

    // __ Свернуть все
    const collapseAll = () => {
        modelsRender.value.forEach(collection => collection.collapsed = true)
    }

    // __ Развернуть все
    const expandAll = () => {
        modelsRender.value.forEach(collection => collection.collapsed = false)
    }

    // __ Получаем данные
    const getData = async () => {
        [sewingOperations.value, sewingOperationSchemas.value, models.value] = await Promise.all([
            sewingStore.getSewingOperations(),
            sewingStore.getSewingOperationSchemas(),
            sewingStore.getModelsForLabor(),
        ])

        sewingOperations.value = sewingOperations.value
            .map((sewingOperation) => ({
                ...sewingOperation,
                description: sewingOperation.description ?? '',
                can_edit: true,
            }))
            .sort((a, b) => a.id - b.id)
        // .sort((a, b) => a.name.localeCompare(b.name))

        // sewingOperationSchemas.value = sewingOperationSchemas.value
        //     .filter(schema => schema.id !== 0)

        models.value = models.value.map((collection) => ({ ...collection, collapsed: true }))
    }

    // __ Формируем отображение Коллекций -> Моделей
    const getDataRender = () => {
        modelsRender.value = models.value
    }

    // __ Реализация фильтров
    watchEffect(() => {
        const memRender = [...modelsRender.value] // Запоминаем состояние из-за collapsed
        modelsRender.value = []

        const schemasIds = sewingOperationSchemas.value
            .map((schema) => (schema.name.toLowerCase().includes(schemaFilter.value.toLowerCase()) ? schema.id : -1))
            .filter((id) => id !== -1)

        for (let i = 0; i < models.value.length; i++) {
            const newItems = []
            for (let j = 0; j < models.value[i].items.length; j++) {
                if (
                    models.value[i].items[j].name_report.toLowerCase().includes(nameFilter.value.toLowerCase()) &&
                    models.value[i].items[j].code_1c.toLowerCase().includes(codeFilter.value.toLowerCase()) &&
                    schemasIds.includes(models.value[i].items[j].sewing_schema_id)
                ) {
                    newItems.push(models.value[i].items[j])
                }
            }

            if (newItems.length) {
                const newCollection = { ...models.value[i] }
                newCollection.items = newItems

                // __ Восстанавливаем collapsed
                const findMemCollection = memRender.find(
                    (collection) => collection.collection === newCollection.collection
                )
                newCollection.collapsed = findMemCollection?.collapsed

                modelsRender.value.push(newCollection)
            }
        }
    })

    onMounted(async () => {
        isLoading.value = true
        const loadingService = useLoading()
        await loaderHandler(
            loadingService,
            async () => {
                await getData()
                getDataRender()
                if (DEBUG) console.log('sewingOperationSchemas: ', sewingOperationSchemas.value)
                if (DEBUG) console.log('sewingOperations: ', sewingOperations.value)
                if (DEBUG) console.log('models: ', models.value)
            },
            undefined
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
