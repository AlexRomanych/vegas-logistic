<template>
    <div>
        <div class="flex">

            <!-- __ collapsed -->
            <AppLabelTSWrapper :render-object="render.collapsed" header @click="collapsed = !collapsed"/>

            <!-- __ id -->
            <AppLabelTSWrapper :render-object="render.id" header/>

            <!-- __ Фамилия -->
            <AppLabelTSWrapper :render-object="render.surname" header/>

            <!-- __ Имя -->
            <AppLabelTSWrapper :render-object="render.name" header/>

            <!-- __ Отчество -->
            <AppLabelTSWrapper :render-object="render.patronymic" header/>

            <!-- __ + Сущность -->
            <template v-if="canEdit">
                <AppLabelTS
                    :height="DEFAULT_HEIGHT"
                    align="center"
                    class="cursor-pointer"
                    rounded="4"
                    text="➕"
                    text-size="mini"
                    type="warning"
                    width="w-[30px]"
                    @click="addWorker"
                />
            </template>

        </div>
    </div>

    <!-- __ Данные -->
    <div v-if="!collapsed">
        <div v-for="entity of entitiesRender" :key="entity.id">
            <div class="flex ">

                <!-- __ collapsed -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.collapsed" @click="setResponsible(entity)"/>

                <!-- __ id -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.id"/>

                <!-- __ Фамилия -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.surname"/>

                <!-- __ Имя -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.name"/>

                <!-- __ Отчество -->
                <AppLabelTSWrapper :arg="entity" :render-object="render.patronymic"/>

                <!-- __ Удалить -->
                <AppLabelTS
                    :height="DEFAULT_HEIGHT"
                    align="center"
                    rounded="4"
                    text="🗑️"
                    text-size="micro"
                    type="danger"
                    width="w-[30px]"
                    @click="removeWorker(entity)"
                />

                <!-- __ ответственный -->
                <AppLabelTSWrapper
                    v-if="sewingDay.responsible && entity.id === sewingDay.responsible.id"
                    :arg="entity"
                    :render-object="render.responsible"
                />

            </div>
        </div>
    </div>


    <!-- __ Выпадающий список-->
    <!--:func="(surname: string, name: string, patronymic: string) => getFormatFIO({surname, name, patronymic})"-->
    <AppModalAsyncSelectTSFunc
        ref="appModalAsyncSelectTS"
        v-model="selectedWorkerId"
        :func="(surname: string, name: string, patronymic: string) => `${surname} ${name} ${patronymic}`"
        :items="globalWorkers"
        title="Выберите сотрудника"
        width="w-[600px]"/>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
    />

</template>

<script lang="ts" setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'

import type { IColorTypes, IRenderData, ISewingDay, ISewingDayWorker } from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
// import { isToday } from '@/app/helpers/helpers_date'
// import { getFormatFIO } from '@/app/helpers/workers/helpers_workers'

import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppModalAsyncSelectTSFunc from '@/components/ui/modals/AppModalAsyncSelectTSFunc.vue'


// __ Унифицируем Интерфейс
type IEntity = ISewingDayWorker

interface IProps {
    sewingDay: ISewingDay
    canEdit?: boolean
}

const props = withDefaults(defineProps<IProps>(), {
    canEdit: true,
})

const emits = defineEmits<{
    (e: 'addWorker', payload: IEntity): void,
    (e: 'removeWorker', payload: IEntity): void,
    (e: 'addResponsible', payload: IEntity): void,
}>()

const sewingStore = useSewingStore()

const { globalWorkers } = storeToRefs(sewingStore)        // __ Все работники (Global State)

// __ Данные для отображения
const entitiesRender = computed(() => props.sewingDay.workers)

const collapsed = ref(false)

// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[25px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'success'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'micro'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'

const render: IRenderData = reactive({
    collapsed  : {
        id            : () => 'collapsed-search',
        header        : () => collapsed.value ? '▲' : '▼',
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? 'success' : 'danger',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍collapsed...',
        data          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? '✓' : '',
        class         : 'cursor-pointer',
    },
    id         : {
        id            : () => 'id-search',
        header        : () => 'ID',
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : false,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? 'success' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍id...',
        data          : (entity: IEntity) => entity.id.toString(),
    },
    surname    : {
        id            : () => 'surname-search',
        header        : () => 'Фамилия',
        width         : 'w-[184px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? 'success' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Фамилия...',
        data          : (entity: IEntity) => entity.surname,
    },
    name       : {
        id            : () => 'name-search',
        header        : () => 'Имя',
        width         : 'w-[90px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? 'success' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Имя...',
        data          : (entity: IEntity) => entity.name,
    },
    patronymic : {
        id            : () => 'patronymic-search',
        header        : () => 'Отчество',
        width         : 'w-[90px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? 'success' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍Отчество...',
        data          : (entity: IEntity) => entity.patronymic ?? '',

    },
    responsible: {
        id            : () => 'responsible-search',
        header        : () => 'Отчество',
        width         : 'w-[90px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? 'success' : DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Ответственный...',
        data          : (entity: IEntity) => props.sewingDay.responsible && entity.id === props.sewingDay.responsible.id ? 'ответственный' : '',

    },
})


// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)

// __ Тип для модального окна выбора Работника
const selectedWorkerId      = ref<number | null>(null)
const appModalAsyncSelectTS = ref<any>(null)

// __ Показываем сообщение об ошибке
const showError = async (error: string | null = null) => {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'
    modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    await appModalAsyncMultiline.value!.show()
}

// __ Выбираем рабочего
const addWorker = async (worker: IEntity) => {
    if (!props.canEdit) {
        return
    }
    // Warning: TODO: Раскомментировать
    // if (!isToday(new Date(props.sewingDay.action_at))) {
    //     return
    // }

    selectedWorkerId.value = worker.id
    const answer           = await appModalAsyncSelectTS.value!.show(selectedWorkerId.value)
    if (answer) {
        const selectedWorker = appModalAsyncSelectTS.value!.selected
        const result         = await sewingStore.addWorkerToSewingDay(props.sewingDay.id, selectedWorker.id)

        if (checkCRUD(result)) {
            const addWorker: IEntity = {
                id        : selectedWorker.id,
                surname   : selectedWorker.surname,
                name      : selectedWorker.name,
                patronymic: selectedWorker.patronymic,
            }

            collapsed.value = false                 // __ Разворачиваем список
            emits('addWorker', addWorker)        // __ Добавляем данные в родительский компонент
        } else {
            await showError()
            return
        }
    }
}

// __ Удаляем рабочего
const removeWorker = async (worker: IEntity) => {
    if (!props.canEdit) {
        return
    }

    const result = await sewingStore.removeWorkerFromSewingDay(props.sewingDay.id, worker.id)

    if (checkCRUD(result)) {
        emits('removeWorker', worker)   // __ Добавляем данные в родительский компонент
    } else {
        await showError()
        return
    }
}

// __ Устанавливаем ответственного
const setResponsible = async (worker: IEntity) => {
    if (!props.canEdit) {
        return
    }

    const result = await sewingStore.addResponsibleToSewingDay(props.sewingDay.id, worker.id)

    if (checkCRUD(result)) {
        emits('addResponsible', worker)   // __ Добавляем данные в родительский компонент
    } else {
        await showError()
        return
    }
}


onMounted(async () => {
    await sewingStore.getActiveWorkers()
})

</script>

<style scoped>

</style>
