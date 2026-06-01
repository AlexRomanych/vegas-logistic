<template>
    <div
        v-if="!isLoading"
        class="mx-2 mt-2"
    >
        <div class="sticky top-0 p-1 mb-1 bg-slate-200 border-2 rounded-lg border-slate-300 max-w">
            <div class="mx-0.5">
                <div class="flex">
                    <!-- __ id -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.id"/>
                        <AppInputTextTSWrapper
                            v-model="idFilter"
                            :render-object="render.id"
                        />
                    </div>

                    <!-- __ Название -->
                    <div>
                        <AppLabelMultilineTSWrapper :render-object="render.kdch"/>
                        <AppInputTextTSWrapper
                            v-model="kdchFilter"
                            :render-object="render.kdch"
                        />
                    </div>

                    <div>
                        <!-- __ + КДЧ -->
                        <router-link :to="{ name: 'manufacture.textile.design.upload' }">
                            <AppLabelMultiLineTS
                                :list="entitiesRender"
                                :text="['➕', '']"
                                align="center"
                                class="cursor-pointer"
                                rounded="4"
                                text-size="large"
                                type="warning"
                                width="w-[64px]"
                            />
                        </router-link>

                        <!-- __ Сброс фильтров -->
                        <div class=" mt-[8px]">
                            <AppLabelTS
                                id="filters-reset"
                                align="center"
                                class="cursor-pointer"
                                height="h-[29px]"
                                rounded="4"
                                text="Очистить"
                                text-size="mini"
                                type="orange"
                                width="w-[64px]"
                                @click="resetFilters"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="ml-2">
            <!-- __ Данные (КДЧ) -->
            <div
                v-for="doc of entitiesRender"
                :key="doc.id"
                class="max-w-fit"
            >

                <div class="flex">

                    <!-- __ id -->
                    <AppLabelTSWrapper
                        :arg="doc"
                        :render-object="render.id"
                        class="cursor-pointer"
                        @click="showDocument(doc)"
                    />

                    <!-- __ Название -->
                    <AppLabelTSWrapper
                        :arg="doc"
                        :render-object="render.kdch"
                        @click="showDocument(doc)"
                    />

                    <!-- __ Просмотр -->
                    <AppLabelTS
                        v-if="CAN_DELETE"
                        align="center"
                        class="cursor-pointer"
                        rounded="4"
                        text="🔍"
                        text-size="mini"
                        type="indigo"
                        width="w-[30px]"
                        @click="showDocument(doc)"
                    />

                    <!-- __ Удалить -->
                    <AppLabelTS
                        v-if="CAN_DELETE"
                        align="center"
                        class="cursor-pointer"
                        rounded="4"
                        text="🗑️"
                        text-size="mini"
                        type="danger"
                        width="w-[30px]"
                        @click="deleteDocument(doc)"
                    />

                </div>


            </div>
        </div>
    </div>

    <!-- __ Просмотр PDF в модальном режиме -->
    <TextileDesignDocumentAsync
        ref="textileDesignDocumentAsync"
        :doc="doc"
        ok-word="Понятно"
        type="primary"
    />

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultilineTS
        ref="appModalAsyncMultilineTS"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

</template>

<script lang="ts" setup>
import type { IColorTypes, IRenderData, ITextileDocument } from '@/types'
import { onMounted, reactive, ref, watchEffect } from 'vue'

import { useSharedStore } from '@/stores/SharedStore.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppInputTextTSWrapper from '@/components/dashboard/models/components/AppInputTextTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/models/components/AppLabelTSWrapper.vue'
import AppLabelMultilineTSWrapper from '@/components/dashboard/models/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import TextileDesignDocumentAsync from '@/components/dashboard/manufacture/shared/textile_design/TextileDesignDocumentAsync.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'


const sharedStore = useSharedStore()

const isLoading = ref(false)

// const CAN_EDIT   = true
const CAN_DELETE = true

let entities: ITextileDocument[] = []
const entitiesRender             = ref<ITextileDocument[]>([])


// __ Объект отображения данных
// const DEFAULT_WIDTH = 'w-[100px]'
// const DEFAULT_WIDTH_BOOL = 'w-[70px]'
// const DEFAULT_WIDTH_DATE = 'w-[100px]'
const DEFAULT_HEIGHT   = 'h-[30px]'
const HEADER_TYPE      = 'primary'
const DATA_TYPE        = 'primary'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'left'
// const DATA_ALIGN_DEFAULT = 'center'

const render: IRenderData = reactive({
    id  : {
        id            : () => 'id-search',
        header        : ['ID', ''],
        width         : 'w-[50px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        placeholder   : '🔍Id...',
        data          : (doc: ITextileDocument) => doc.id.toString(),
        class         : 'cursor-pointer',
    },
    kdch: {
        id            : () => 'name-search',
        header        : ['Название', 'КДЧ'],
        width         : 'w-[400px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => 'indigo',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        placeholder   : '🔍КДЧ...',
        data          : (doc: ITextileDocument) => doc.kdch,
        class         : 'cursor-pointer',
    },
})

// __ Фильтры
const kdchFilter = ref('')
const idFilter   = ref('')

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>('danger')
const modalInfoText            = ref<string | string[]>('')
const modalInfoMode            = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value = renderError
    await appModalAsyncMultilineTS.value!.show()
}

// __ Удаляем КДЧ
const deleteDocument = async (textileDocument: ITextileDocument) => {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'confirm'

    modalInfoText.value = ['Запись будет удалена.', 'Продолжить?']
    const answer        = await appModalAsyncMultilineTS.value!.show()

    if (!answer) {
        return
    }

    const result = await sharedStore.deleteTextileDesignDocumentById(textileDocument.id)

    if (!checkCRUD(result)) {
        await showError()
        return
    } else {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'

        modalInfoText.value = result.payload
        await appModalAsyncMultilineTS.value!.show()
        entitiesRender.value = entitiesRender.value.filter(doc => doc.id !== textileDocument.id)
        return
    }
}

// __ Показываем КДЧ
const textileDesignDocumentAsync = ref<InstanceType<typeof TextileDesignDocumentAsync> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией
const doc                        = ref<ITextileDocument | null>()

const showDocument = async (textileDocument: ITextileDocument) => {
    doc.value = textileDocument
    await textileDesignDocumentAsync.value!.show()
    doc.value = null
    return
}

// __ Обнуляем фильтры
const resetFilters = () => {
    idFilter.value   = ''
    kdchFilter.value = ''
}


watchEffect(() => {

    const kdchSearch = kdchFilter.value.toLowerCase()
    const idSearch   = idFilter.value.toLowerCase()

    entitiesRender.value = entities
        .filter(entity => entity.kdch.toLowerCase().includes(kdchSearch))
        .filter(entity => entity.id.toString().toLowerCase().includes(idSearch))
})

// __ Получение данных
const getEntities = async () => {
    entities = await sharedStore.getTextileDesignDocuments()
    entities = entities
        .sort((a, b) => a.kdch.localeCompare(b.kdch))
}

// __ Формирование данных для рендера
const getEntitiesRender = () => {
    entitiesRender.value = entities
}


onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getEntities()
            console.log('docs: ', entities)

            getEntitiesRender()
        },
        undefined
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>

</style>
