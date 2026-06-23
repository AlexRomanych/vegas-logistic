<template>
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
                            <!-- __  Заголовок 'Модели / Процедуры' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :text="SHOW_SCHEMAS_OPERATIONS ? 'Модели/Процедуры/Операции' : 'Модели/Процедуры'"
                                :width="HEADER_ROWS_WIDTH"
                                align="center"
                                rounded="4"
                                text-size="normal"
                                type="primary"
                            />

                            <!-- __  Заголовок 'ШМ' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :width="FIELD_WIDTH"
                                align="center"
                                rounded="4"
                                text="ШМ"
                                text-size="normal"
                                type="success"
                            />

                            <!-- __  Заголовок 'ТКЧ' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :width="FIELD_WIDTH"
                                align="center"
                                rounded="4"
                                text="КДЧ"
                                text-size="normal"
                                type="orange"
                            />
                            <!-- __  Заголовок 'Угол' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :width="FIELD_WIDTH"
                                align="center"
                                rounded="4"
                                text="Угол"
                                text-size="normal"
                                type="info"
                            />

                            <!-- __  Заголовок 'Процедура для Верхней крышки' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :width="PROCEDURES_TITLE_WIDTH"
                                align="center"
                                rounded="4"
                                text="Процедура для Верхней крышки"
                                text-size="normal"
                                type="stone"
                            />

                            <!-- __  Заголовок 'Процедура для Боковины' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :width="PROCEDURES_TITLE_WIDTH"
                                align="center"
                                rounded="4"
                                text="Процедура для Боковины"
                                text-size="normal"
                                type="stone"
                            />

                            <!-- __  Заголовок 'Процедура для Нижней крышки' -->
                            <AppLabelTS
                                :height="'h-[134px]'"
                                :width="PROCEDURES_TITLE_WIDTH"
                                align="center"
                                rounded="4"
                                text="Процедура для Нижней крышки"
                                text-size="normal"
                                type="stone"
                            />

                            <!-- __  Заголовок 'Схема' -->
                            <AppLabelTS
                                v-if="SHOW_SCHEMAS_OPERATIONS"
                                :height="'h-[134px]'"
                                align="center"
                                rounded="4"
                                text="Схема"
                                text-size="normal"
                                type="success"
                                width="w-[150px]"
                            />
                        </div>

                        <!--!!! Убираем пока сворачивание-->
                        <div class="flex">
                            <!-- __  Свернуть все '▲' -->
                            <AppLabelTS
                                :height="CELL_HEIGHT"
                                :width="COLLAPSE_ALL_WIDTH"
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
                                :width="COLLAPSE_ALL_WIDTH"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text="▼"
                                text-size="large"
                                type="indigo"
                                @click="expandAll"
                            />
                        </div>

                        <div class="flex mt-[-6px] mb-[2px] ml-[20px]">
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

                            <!-- __ Фильтр: ШМ -->
                            <AppInputTextTS
                                id="name-search"
                                v-model:text-value.trim="machineFilter"
                                :width="FIELD_WIDTH"
                                placeholder="🔍ШМ..."
                                text-size="mini"
                                type="success"
                            />

                            <!-- __ Фильтр: КДЧ -->
                            <AppInputTextTS
                                id="name-search"
                                v-model:text-value.trim="kdchFilter"
                                :width="FIELD_WIDTH"
                                placeholder="🔍КДЧ..."
                                text-size="mini"
                                type="orange"
                            />

                            <!-- __ Фильтр: Угол -->
                            <AppInputTextTS
                                id="name-search"
                                v-model:text-value.trim="angleFilter"
                                :width="FIELD_WIDTH"
                                placeholder="🔍Угол..."
                                text-size="mini"
                                type="info"
                            />

                            <!-- __ Фильтр: Процедура для верхней крышки -->
                            <AppInputTextTS
                                id="procedure-up-search"
                                v-model:text-value.trim="procedureUpCoverFilter"
                                :width="PROCEDURES_TITLE_WIDTH"
                                placeholder="🔍Процедура для Верхней крышки..."
                                text-size="mini"
                                type="stone"
                            />

                            <!-- __ Фильтр: Процедура для Боковины -->
                            <AppInputTextTS
                                id="procedure-down-search"
                                v-model:text-value.trim="procedureSideFilter"
                                :width="PROCEDURES_TITLE_WIDTH"
                                placeholder="🔍Процедура для Боковины..."
                                text-size="mini"
                                type="stone"
                            />

                            <!-- __ Фильтр: Процедура для Нижней крышки -->
                            <AppInputTextTS
                                id="procedure-down-search"
                                v-model:text-value.trim="procedureDownCoverFilter"
                                :width="PROCEDURES_TITLE_WIDTH"
                                placeholder="🔍Процедура для Нижней крышки..."
                                text-size="mini"
                                type="stone"
                            />

                            <!-- __ Фильтр: Схема Типовых операций -->
                            <AppInputTextTS
                                v-if="SHOW_SCHEMAS_OPERATIONS"
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
                    <template v-if="SHOW_SCHEMAS_OPERATIONS">
                        <div
                            v-for="operation of cuttingOperations"
                            :key="operation.id"
                            class="sticky-header"
                        >
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
                    </template>
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
                                <div class="flex ml-[20px]">
                                    <!-- __ Код из 1С -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="model.code_1c"
                                        :width="MODEL_CODE_1C_WIDTH"
                                        align="center"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        title="Double Click - Показать Спецификацию"
                                        type="dark"
                                        @dblclick="showSpecification(model)"
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
                                        title="Double Click - Показать Спецификацию"
                                        type="dark"
                                        @dblclick="showSpecification(model)"
                                    />

                                    <!-- __ Название ШМ -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="getMachineName(model.machine)"
                                        :type="model.machine === 'undefined' ? 'dark' : 'success'"
                                        :width="FIELD_WIDTH"
                                        align="center"
                                        rounded="4"
                                        text-size="micro"
                                    />

                                    <!-- __ КДЧ -->
                                    <AppLabelTS
                                        :class="model.kdch_doc ? 'cursor-pointer' : ''"
                                        :height="CELL_HEIGHT"
                                        :text="model.kdch_doc ? `🔍 ${model.kdch_doc.kdch}` : model.kdch ?? ''"
                                        :title="model.kdch_doc ? 'Double Click - Показать КДЧ' : ''"
                                        :type="model.kdch_doc ? 'orange' : model.kdch ? 'stone' : 'dark'"
                                        :width="FIELD_WIDTH"
                                        align="center"
                                        rounded="4"
                                        text-size="micro"
                                        @dblclick="showDocument(model.kdch_doc)"
                                    />

                                    <!-- __ Угол -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="model.angle ?? ''"
                                        :type="model.angle ? 'info' : 'danger'"
                                        :width="FIELD_WIDTH"
                                        align="center"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        title="Double Click - Добавить/Редактировать Угол"
                                        @dblclick="angleAction(model)"
                                    />

                                    <!-- __ Название Процедуры Верхней Крышки -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="getProcedureName(model, DETAIL_COVER_UP_POINTER)"
                                        :type="model.cut_proc_up_id ? 'stone' : 'danger'"
                                        :width="PROCEDURE_WIDTH"
                                        align="left"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        title="Double Click - Выбрать процедуру, Ctrl+Click - Показать процедуру"
                                        @dblclick.exact="selectProcedure(model, DETAIL_COVER_UP_POINTER)"
                                        @click.ctrl="showProcedure(model.cut_proc_up_id)"
                                    />

                                    <!-- __ Название Процедуры Боковины -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="getProcedureName(model, DETAIL_SIDE_POINTER)"
                                        :type="model.cut_proc_side_id ? 'stone' : 'danger'"
                                        :width="PROCEDURE_WIDTH"
                                        align="left"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        title="Double Click - Выбрать процедуру, Ctrl+Click - Показать процедуру"
                                        @dblclick.exact="selectProcedure(model, DETAIL_SIDE_POINTER)"
                                        @click.ctrl="showProcedure(model.cut_proc_side_id)"
                                    />

                                    <!-- __ Название Процедуры Нижней Крышки -->
                                    <AppLabelTS
                                        :height="CELL_HEIGHT"
                                        :text="getProcedureName(model, DETAIL_COVER_DOWN_POINTER)"
                                        :type="model.cut_proc_down_id ? 'stone' : 'danger'"
                                        :width="PROCEDURE_WIDTH"
                                        align="left"
                                        class="cursor-pointer"
                                        rounded="4"
                                        text-size="micro"
                                        title="Double Click - Выбрать процедуру, Ctrl+Click - Показать процедуру"
                                        @dblclick.exact="selectProcedure(model, DETAIL_COVER_DOWN_POINTER)"
                                        @click.ctrl="showProcedure(model.cut_proc_down_id)"
                                    />

                                    <!-- __ Название Схемы -->
                                    <AppLabelTS
                                        v-if="SHOW_SCHEMAS_OPERATIONS"
                                        :height="CELL_HEIGHT"
                                        :text="getSchemaName(model)"
                                        :type="model.cutting_schema_id ? 'success' : 'danger'"
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
                            <template v-if="SHOW_SCHEMAS_OPERATIONS">
                                <div
                                    v-for="operation of cuttingOperations"
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
                            </template>
                        </div>
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
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

    <!-- __ Модальное окно для редактирования данных Схемы-->
    <CuttingOperationSchemaDataEdit
        ref="cuttingOperationSchemaDataEdit"
        :schema="modalSchema!"
    />

    <!-- __ Выпадающий список-->
    <AppModalAsyncSelectTS
        ref="appModalAsyncSelectTS"
        v-model="selectedItemId"
        :items="selectedItems"
        title="Выберите данные"
        width="w-[600px]"
    />

    <!-- __ Просмотр PDF в модальном режиме -->
    <TextileDesignDocumentAsync
        ref="textileDesignDocumentAsync"
        :doc="doc"
        ok-word="Понятно"
        type="primary"
    />

    <!-- __ Карточка Спецификации -->
    <CardSpecification
        ref="cardSpecification"
        :construct="modelConstruct"
    />

    <!-- __ Карточка Процедуры расчета -->
    <CardProcedure
        ref="cardProcedure"
        :is-admin="false"
        :procedure="procedure"
    />

    <!-- __ Модальное окно для изменения/добавления Угла -->
    <AngleEdit
        ref="angleEdit"
        :angle="angle"
        label="Угол для Раскроя"
    />

</template>

<script lang="ts" setup>
import { onMounted, ref, watchEffect } from 'vue'

import type {
    IColorTypes,
    ICuttingOperation,
    ICuttingOperationModelsCollection,
    ICuttingOperationSchema,
    ICuttingOperationItem,
    ICuttingOperationUpdateObject,
    ICuttingOperationModel, ICuttingProcedure, ICuttingDetailType, ITextileDocument, IModelConstruct, IModelProcedure,
} from '@/types'

import { useCuttingStore } from '@/stores/CuttingStore.ts'
import { useModelsStore } from '@/stores/ModelsStore.ts'

import { DETAIL_COVER_DOWN_POINTER, DETAIL_COVER_UP_POINTER, DETAIL_PANEL, DETAIL_SIDE, DETAIL_SIDE_POINTER } from '@/app/constants/cutting.ts'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { getMachineName } from '@/app/helpers/helpers_model.ts'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import CuttingOperationItemEdit from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_operations/CuttingOperationItemEdit.vue'
import CuttingOperationSchemaDataEdit
    from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_operations/CuttingOperationSchemaDataEdit.vue'
import AppModalAsyncSelectTS from '@/components/ui/modals/AppModalAsyncSelectTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import TextileDesignDocumentAsync from '@/components/dashboard/manufacture/shared/textile_design/TextileDesignDocumentAsync.vue'
import CardSpecification from '@/components/dashboard/models/components/CardSpecification.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'
import CardProcedure from '@/components/dashboard/manufacture/cells/cutting/cutting_components/common/CardProcedure.vue'
import AngleEdit from '@/components/dashboard/manufacture/cells/cutting/cutting_components/cutting_procedures/AngleEdit.vue'
// import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'


type ISelectableItem = (ICuttingOperationSchema | ICuttingProcedure) & { id: number; name: string };

const cuttingStore = useCuttingStore()
const modelsStore  = useModelsStore()

const isLoading = ref(false)

const DEBUG = true

// __ Права изменения
// const CAN_EDIT = true
// const CAN_DELETE = true

// __ Показываем здесь или нет схемы трудозатрат
const SHOW_SCHEMAS_OPERATIONS = false

// __ Константы
const PROCEDURES_TITLE_WIDTH = SHOW_SCHEMAS_OPERATIONS ? 'w-[250px]' : 'w-[300px]'
const HEADER_COLUMNS_HEIGHT  = 'h-[200px]'
const HEADER_ROWS_WIDTH      = 'w-[274px]'

const COLLECTION_COLLAPSE_WIDTH = 'w-[40px]'
const COLLECTION_NAME_WIDTH     = 'w-[538px]'

const MODEL_CODE_1C_WIDTH = 'w-[70px]'
const MODEL_NAME_WIDTH    = 'w-[180px]'
const SCHEMA_WIDTH        = 'w-[150px]'
const PROCEDURE_WIDTH     = PROCEDURES_TITLE_WIDTH
const FIELD_WIDTH         = 'w-[70px]'

const CELL_WIDTH  = 'w-[50px]'
const CELL_HEIGHT = 'h-[30px]'

const COLLAPSE_ALL_WIDTH = SHOW_SCHEMAS_OPERATIONS ? 'w-[593px]' : 'w-[702px]'

// __ Определяем переменные
const cuttingOperationSchemas = ref<ICuttingOperationSchema[]>([])
const cuttingOperations       = ref<ICuttingOperation[]>([])
const cuttingProcedures       = ref<ICuttingProcedure[]>([])
const models                  = ref<ICuttingOperationModelsCollection[]>([])
const modelsRender            = ref<ICuttingOperationModelsCollection[]>([])

// __ Фильтр
const codeFilter               = ref('')
const nameFilter               = ref('')
const machineFilter            = ref('')
const angleFilter              = ref('')
const kdchFilter               = ref('')
const schemaFilter             = ref('')
const procedureUpCoverFilter   = ref('')
const procedureSideFilter      = ref('')
const procedureDownCoverFilter = ref('')
// const procedureFilter          = ref('')

// __ Тип для модального окна ячейки
const modalOperation           = ref<ICuttingOperation | null>(null)
const modalSchema              = ref<ICuttingOperationSchema | null>(null)
const cuttingOperationItemEdit = ref<InstanceType<typeof CuttingOperationItemEdit> | null>(null)

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null)

// __ Тип для модального окна изменения данных Схемы
const cuttingOperationSchemaDataEdit = ref<InstanceType<typeof CuttingOperationSchemaDataEdit> | null>(null)

// __ Тип для модального окна выбора Схемы
const selectedItems         = ref<ISelectableItem[]>([])
const selectedItemId        = ref()
const appModalAsyncSelectTS = ref<any>(null)
// const appModalAsyncSelectTS = ref<InstanceType<typeof AppModalAsyncSelectTS> | null>(null)

// __ Вспомогательная. Возвращает массив операций в зависимости от схемы или ее отсутствия
const getTargetOperations = (model: ICuttingOperationModel) => {
    let targetOperations: ICuttingOperationItem[] = model.operations
    if (model.cutting_schema_id) {
        const schema = cuttingOperationSchemas.value.find((schema) => schema.id === model.cutting_schema_id)
        if (schema) {
            targetOperations = schema.operations
        }
    }
    return targetOperations
}

// __ Получаем значение текста для отображения в ячейке
const getOperationValue = (model: ICuttingOperationModel, operation: ICuttingOperation) => {
    const targetOperations = getTargetOperations(model)
    for (let i = 0; i < targetOperations.length; i++) {
        if (targetOperations[i].id === operation.id) {
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
const getType = (model: ICuttingOperationModel, operation: ICuttingOperation) => {
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

const getSchemaName = (model: ICuttingOperationModel) => {
    const schema = cuttingOperationSchemas.value.find((schema) => schema.id === model.cutting_schema_id)
    return schema ? schema.name : ''
}

const getProcedureName = (model: ICuttingOperationModel, detailType: ICuttingDetailType) => {
    let procedure
    switch (detailType) {
        case DETAIL_COVER_UP_POINTER:
            procedure = cuttingProcedures.value.find(procedure => procedure.id === model.cut_proc_up_id)
            break
        case DETAIL_COVER_DOWN_POINTER:
            procedure = cuttingProcedures.value.find(procedure => procedure.id === model.cut_proc_down_id)
            break
        case DETAIL_SIDE_POINTER:
            procedure = cuttingProcedures.value.find(procedure => procedure.id === model.cut_proc_side_id)
    }

    return procedure ? procedure.name : ''
}

// __ Показываем сообщение об ошибке
const showError = async (error: string | null = null) => {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'
    modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    await appModalAsyncMultilineTS.value!.show()
}

// __ Редактируем операцию или удаляем или переключаем
const editOperation = async (model: ICuttingOperationModel, operation: ICuttingOperation) => {
    if (model.cutting_schema_id !== 0) {
        return
    }

    // __ Тут по идее всегда будет 0 (Без схемы)
    const schema = cuttingOperationSchemas.value.find((schema) => schema.id === model.cutting_schema_id)
    if (!schema) {
        return
    }
    const sourceSchema      = JSON.parse(JSON.stringify(schema))
    sourceSchema.operations = model.operations

    modalOperation.value = operation
    modalSchema.value    = sourceSchema

    const result = await cuttingOperationItemEdit.value?.show()
    if (result) {
        const present = cuttingOperationItemEdit.value!.present

        // __ Если операция не добавлена (или удалена)
        if (!present) {
            const findIndex = model.operations.findIndex((item) => item.id === operation.id)
            if (findIndex !== -1) {
                const deleteObject: ICuttingOperationUpdateObject = {
                    operation_id: operation.id,
                    target_id   : model.code_1c,
                    pivot       : null,
                }

                const result = await cuttingStore.deleteCuttingOperationFromModel(deleteObject)

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

            const ratio     = !!cuttingOperationItemEdit.value!.ratio ? cuttingOperationItemEdit.value!.ratio : null
            const findIndex = model.operations.findIndex((item) => item.id === operation.id)

            if (findIndex !== -1) {
                if (model.operations[findIndex].pivot.ratio === ratio) {
                    return // __ Если нет изменений, то выходим
                }
            }

            const updateObject: ICuttingOperationUpdateObject = {
                operation_id: operation.id,
                target_id   : model.code_1c,
                pivot       : {
                    ratio,
                    amount   : null,
                    condition: null,
                    position : null,
                },
            }

            const result = await cuttingStore.addCuttingOperationToModel(updateObject)

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
                    id   : operation.id,
                    pivot: {
                        ratio    : ratio,
                        amount   : null,
                        condition: null,
                        position : null,
                    },
                } as ICuttingOperationItem)
            }
        }
    }
}

// __ Выбираем схему для модели
const selectSchema = async (model: ICuttingOperationModel) => {
    selectedItems.value  = cuttingOperationSchemas.value
    selectedItemId.value = model.cutting_schema_id
    // selectedSchemaId.value = model.cutting_schema_id
    const answer         = await appModalAsyncSelectTS.value!.show(selectedItemId.value)
    if (answer) {
        const selectedSchema = appModalAsyncSelectTS.value!.selected
        if (selectedSchema.id === model.cutting_schema_id) {
            return
        }

        const result = await cuttingStore.updateModelCuttingOperationSchema(model.code_1c, selectedSchema.id)

        if (checkCRUD(result)) {
            model.cutting_schema_id = selectedSchema.id
        } else {
            await showError()
            return
        }
    }
}

// __ Выбираем Процедуру Раскроя для модели
const selectProcedure = async (model: ICuttingOperationModel, detailType: ICuttingDetailType) => {
    selectedItems.value = cuttingProcedures.value

    switch (detailType) {
        case DETAIL_COVER_UP_POINTER:
            selectedItems.value  = cuttingProcedures.value.filter(procedure => procedure.object_name === DETAIL_PANEL)
            selectedItemId.value = model.cut_proc_up_id
            break
        case DETAIL_COVER_DOWN_POINTER:
            selectedItems.value  = cuttingProcedures.value.filter(procedure => procedure.object_name === DETAIL_PANEL)
            selectedItemId.value = model.cut_proc_down_id
            break
        case DETAIL_SIDE_POINTER:
            selectedItems.value  = cuttingProcedures.value.filter(procedure => procedure.object_name === DETAIL_SIDE)
            selectedItemId.value = model.cut_proc_side_id
    }

    const answer = await appModalAsyncSelectTS.value!.show(selectedItemId.value)

    if (answer) {
        const selectedProcedure = appModalAsyncSelectTS.value!.selected
        switch (detailType) {
            case DETAIL_COVER_UP_POINTER:
                if (selectedProcedure.id === model.cut_proc_up_id) {
                    return
                }
                break
            case DETAIL_COVER_DOWN_POINTER:
                if (selectedProcedure.id === model.cut_proc_down_id) {
                    return
                }
                break
            case DETAIL_SIDE_POINTER:
                if (selectedProcedure.id === model.cut_proc_side_id) {
                    return
                }
        }

        const result = await cuttingStore.updateModelCuttingProcedure(model.code_1c, selectedProcedure.id, detailType)

        if (checkCRUD(result)) {

            switch (detailType) {
                case DETAIL_COVER_UP_POINTER:
                    model.cut_proc_up_id = selectedProcedure.id
                    break
                case DETAIL_COVER_DOWN_POINTER:
                    model.cut_proc_down_id = selectedProcedure.id
                    break
                case DETAIL_SIDE_POINTER:
                    model.cut_proc_side_id = selectedProcedure.id
            }

        } else {
            await showError()
            return
        }
    }
}

// __ Показываем КДЧ
const textileDesignDocumentAsync = ref<InstanceType<typeof TextileDesignDocumentAsync> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией
const doc                        = ref<ITextileDocument | null>()

const showDocument = async (textileDocument: ITextileDocument | null) => {
    if (!textileDocument) return

    doc.value = textileDocument
    await textileDesignDocumentAsync.value!.show()
    doc.value = null
    return
}

// __ Карточка Спецификаций
const cardSpecification = ref<InstanceType<typeof CardSpecification> | null>(null)
const modelConstruct    = ref<IModelConstruct | null>(null)

// __ Показываем спецификацию
const showSpecification = async (model: ICuttingOperationModel | null) => {
    if (!model) {
        return
    }

    const construct = await modelsStore.getConstructByModelCode1c(model.code_1c)
    if (!construct) {
        modalInfoType.value = 'danger'
        modalInfoText.value = [
            `Спецификация для модели: ${model.name}`,
            'не найдена!'
        ]
        await appModalAsyncMultilineTS.value?.show()
        return
    }

    modelConstruct.value = construct
    await cardSpecification.value?.show()
}

// __ Карточка Процедуры расчета
const cardProcedure = ref<InstanceType<typeof CardProcedure> | null>(null)
const procedure     = ref<IModelProcedure | null>(null)

// __ Показываем процедуру
const showProcedure = async (id: number | null = null) => {
    if (!id || id === 0) {
        return
    }
    // console.log(procedure)
    procedure.value = await cuttingStore.getCuttingProcedure(id)
    await cardProcedure.value?.show()
}

// __ Тип для модального окна изменения Комментария
const angle     = ref('')
const angleEdit = ref<InstanceType<typeof AngleEdit> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Добавляем/Редактируем Угол
const angleAction = async (model: ICuttingOperationModel | null = null) => {
    if (!model) {
        return
    }
    angle.value  = model.angle ?? '' // __ Устанавливаем Угол
    const answer = await angleEdit.value!.show()
    if (answer) {
        const newAngle = angleEdit.value!.angle.trim()
        const result   = await cuttingStore.setCuttingAngle(model.code_1c, newAngle)
        if (!checkCRUD(result)) {
            await showError()
            return
        } else {
            model.angle = newAngle
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
    [cuttingOperations.value, cuttingOperationSchemas.value, models.value, cuttingProcedures.value] = await Promise.all([
        cuttingStore.getCuttingOperations(),
        cuttingStore.getCuttingOperationSchemas(),
        cuttingStore.getModelsForLabor(),
        cuttingStore.getCuttingProcedures(),
    ])

    cuttingOperations.value = cuttingOperations.value
        .map(cuttingOperation => ({
            ...cuttingOperation,
            description: cuttingOperation.description ?? '',
            can_edit   : true,
        }))
        .sort((a, b) => a.id - b.id)
    // .sort((a, b) => a.name.localeCompare(b.name))

    // cuttingOperationSchemas.value = cuttingOperationSchemas.value
    //     .filter(schema => schema.id !== 0)

    cuttingProcedures.value = cuttingProcedures.value
        .map(cuttingProcedure => ({
            ...cuttingProcedure,
            description: cuttingProcedure.description ?? '',
            can_edit   : true
        }))
        .sort((a, b) => a.id - b.id)


    models.value = models.value.map(collection => {
        return {
            ...collection,
            items    : collection.items.map(item => ({ ...item, kdch: item.kdch ?? '', angle: item.angle ?? '' })),
            collapsed: true,
        }
    })
    // models.value = models.value.map((collection) => ({ ...collection, collapsed: true }))
}

// __ Формируем отображение Коллекций -> Моделей
const getDataRender = () => {
    modelsRender.value = models.value
}

// __ Реализация фильтров
watchEffect(() => {
    // return
    const memRender    = [...modelsRender.value] // Запоминаем состояние из-за collapsed
    modelsRender.value = []

    const schemasIds = cuttingOperationSchemas.value
        .map(schema => schema.name.toLowerCase().includes(schemaFilter.value.toLowerCase()) ? schema.id : -1)
        .filter((id) => id !== -1)

    for (let i = 0; i < models.value.length; i++) {
        const newItems = []
        for (let j = 0; j < models.value[i].items.length; j++) {

            const machineName = getMachineName(models.value[i].items[j].machine)
            const hasMachine  = machineName.toLowerCase().includes(machineFilter.value.toLowerCase())

            const procedureUp    = cuttingProcedures.value.find(proc => proc.id === models.value[i].items[j].cut_proc_up_id)
            const hasProcedureUp = procedureUp && procedureUp.name.toLowerCase().includes(procedureUpCoverFilter.value.toLocaleLowerCase())

            const procedureDown    = cuttingProcedures.value.find(proc => proc.id === models.value[i].items[j].cut_proc_down_id)
            const hasProcedureDown = procedureDown && procedureDown.name.toLowerCase().includes(procedureDownCoverFilter.value.toLocaleLowerCase())

            const procedureSide    = cuttingProcedures.value.find(proc => proc.id === models.value[i].items[j].cut_proc_side_id)
            const hasProcedureSide = procedureSide && procedureSide.name.toLowerCase().includes(procedureSideFilter.value.toLocaleLowerCase())

            if (
                models.value[i].items[j].name_report.toLowerCase().includes(nameFilter.value.toLowerCase()) &&
                models.value[i].items[j].code_1c.toLowerCase().includes(codeFilter.value.toLowerCase()) &&
                models.value[i].items[j].angle?.toLowerCase().includes(angleFilter.value.toLowerCase()) &&
                models.value[i].items[j].kdch?.toLowerCase().includes(kdchFilter.value.toLowerCase()) &&
                hasMachine && hasProcedureUp && hasProcedureDown && hasProcedureSide &&
                schemasIds.includes(models.value[i].items[j].cutting_schema_id)
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
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getData()
            getDataRender()
            if (DEBUG) console.log('cuttingOperationSchemas: ', cuttingOperationSchemas.value)
            if (DEBUG) console.log('cuttingOperations: ', cuttingOperations.value)
            if (DEBUG) console.log('models: ', models.value)
            if (DEBUG) console.log('modelsRender: ', modelsRender.value)
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
