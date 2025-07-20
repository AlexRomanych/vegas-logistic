<template>
    <!--    <div class="m-2 p-2 flex justify-center items-center h-full">-->
    <div class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <!-- __ Название Причины -->
                <AppInputText
                    id="name"
                    v-model.trim="v$.name.$model"
                    :errors="v$.name.$errors"
                    :value="v$.name.$model"
                    label="Название причины"
                    mode="text"
                    placeholder="Введите название причины..."
                />

                <!-- __ Сокращенное название Причины (пока не показываем)-->
                <AppInputText
                    v-if="false"
                    id="display-name"
                    v-model.trim="v$.displayName.$model"
                    :errors="v$.displayName.$errors"
                    :value="v$.displayName.$model"
                    label="Отображаемое название причины"
                    mode="text"
                    placeholder="Введите сокращенное название причины..."
                />

                <!-- __ Актуальность -->
                <div class="mt-3"></div>
                <AppCheckbox
                    id="active"
                    :checkboxData="checkboxData"
                    dir="horizontal"
                    inputType="radio"
                    legend="Актуальность записи"
                    type="secondary"
                    width="w-[500px]"
                    @checked="activityHandler"
                />

                <!-- __ Описание Причины -->
                <AppInputArea
                    id="description"
                    v-model.trim="v$.description.$model"
                    :value="v$.description.$model"
                    height="min-h-[100px]"
                    label="Описание причины"
                    label-text-size="mini"
                    placeholder="Заполните описание причины..."
                    text-size="small"
                    type="light"
                />

                <!-- __ Назад -->
                <div class="mt-5 flex justify-between w-full gap-x-2">
                    <router-link :to="{ name: 'reasons' }" class="w-full">
                        <AppInputButton
                            id="returnButton"
                            func="button"
                            title="Назад"
                            width="w-full"
                        />
                    </router-link>

                    <!-- __ Сохранить -->
                    <AppInputButton
                        id="submitButton"
                        func="submit"
                        title="Сохранить"
                        type="success"
                        width="w-full"
                    />

                </div>

            </div>

        </form>

        <AppCallout
            :show="calloutShow"
            :text="calloutMessage"
            :type="calloutType"
            @toggleShow="calloutClose"
        />

    </div>
</template>

<script lang="ts" setup>

import type {
    IReason
} from '@/app/types'

import { onMounted, ref, } from 'vue'

import { useReasonStore } from '@/stores/ReasonsStore.ts'
import { checkApiAnswer } from '@/app/helpers/helpers_checks.ts'


import { FABRIC_PAGE_MODE } from '@/app/constants/fabrics.ts'

import { useRoute, } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength, helpers } from '@vuelidate/validators'


import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppInputArea from '@/components/ui/inputs/AppInputTextArea.vue'
import AppCheckbox from '@/components/ui/checkboxes/AppCheckbox.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'

// import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
// import AppLabel from '@/components/ui/labels/AppLabel.vue'
// import AppInputNumber from '@/components/ui/inputs/AppInputNumber.vue'
// import AppSelect from '@/components/ui/selects/AppSelect.vue'


// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'

const loadingService = useLoading()
// import 'vue-loading-overlay/dist/css/index.css'
// import { LOADER_SETTINGS } from '@/app/constants/common.ts'

// const $loading = useLoading({...LOADER_SETTINGS})
// const loader = $loading.show()
// const isLoading = ref(true)
// __ End Loader

const reasonsStore = useReasonStore()
const route = useRoute()

// __ Callout
const calloutShow = ref(false) // состояние окна
const calloutMessage = ref('') // определяем показываемое сообщение
const calloutType = ref('danger') // определяем тип callout

// для того чтобы оставить анимацию в callout
const calloutClose = (show: number = 5000) => {
    setInterval(() => (calloutShow.value = false), show)
}
// __ End Callout


// __ Определяем режим работы формы (редактирование или создание)
const editMode = route.meta.mode === FABRIC_PAGE_MODE.EDIT

console.log('meta: ', route.query)

// __ Определяем шаблон причины
const reasonTemplate = ref<IReason>({
    id: 0,
    name: '',
    display_name: '',
    active: true,
    description: '',
    reason_category_id: (() => {
        const categoryIdFromQuery = Array.isArray(route.query.category_id)
            ? route.query.category_id[0]
            : route.query.category_id;

        if (categoryIdFromQuery) { // Проверяем, что значение существует и не пустое
            const parsedId = parseInt(categoryIdFromQuery);
            return isNaN(parsedId) ? 0 : parsedId; // Если результат NaN, возвращаем 0
        }
        return 0; // Если categoryIdFromQuery было undefined/null/пустой строкой
    })(),
    reason_number_in_reason_category: 0
})

console.log('reasonTemplate: ', reasonTemplate.value)

// __ Определяем объекты валидации
const name = ref(reasonTemplate.value.name)
const displayName = ref(reasonTemplate.value.display_name)
const active = ref(reasonTemplate.value.active)
const description = ref(reasonTemplate.value.description)


// __ Определяем объект с данными чекбокса
const checkboxData = {
    name: 'Activity',
    data: [
        {id: 1, name: 'Активная', checked: active.value},
        {id: 2, name: 'Архив', checked: !active.value},
    ],
}


// __ Подгружаем данные по Причине, если мы в режиме редактирования
const getReason = async () => {
    if (!editMode) return

    // isLoading.value = true
    reasonTemplate.value = await loaderHandler(
        loadingService,
        () => reasonsStore.getReasonById(parseInt(route.params.id as string)),
    )
    // isLoading.value = false

    name.value = reasonTemplate.value.name
    displayName.value = reasonTemplate.value.display_name
    active.value = reasonTemplate.value.active
    description.value = reasonTemplate.value.description
    checkboxData.data.forEach(item => item.id === 1 ? item.checked = active.value : item.checked = !active.value)

    console.log('reasonTemplate: ', reasonTemplate.value)

}


// __ Определяем объект валидации
const verify = {
    name,
    description,
    displayName,
}

// __ Определяем правила валидации
const MIN_NAME_LENGTH = 20
const REQUIRED_MESSAGE = 'Поле обязательно для заполнения'

const rules = {
    name: {
        $autoDirty: true,
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(
            `Минимальная длина названия - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    // displayName: {
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    //     minLength: helpers.withMessage(
    //         `Минимальная отображаемого названия - ${MIN_DISPLAY_NAME_LENGTH} символов`,
    //         minLength(MIN_DISPLAY_NAME_LENGTH),
    //     ),
    // },
    displayName: {},
    description: {},
}

// __ Оборачиваем в объект валидации
const v$ = useVuelidate(rules, verify)

// __ Обработчик чекбокса на active
const activityHandler = <T extends { id: number }>(data: T): boolean => reasonTemplate.value.active = data.id === 1


// __ Отправка формы
const formSubmit = async (/*e: Event*/) => {

    // v$.value.$touch()                                // валидируем всю форму (обновляет маркеры валидации внутри объекта v$)
    const isFormCorrect = await v$.value.$validate()    // валидируем всю форму

    if (!isFormCorrect) return // это показатель ошибки

    reasonTemplate.value.name = name.value.trim()
    reasonTemplate.value.display_name = displayName.value.trim()
    reasonTemplate.value.description = description.value.trim()

    // console.log('reached: reasonTemplate')

    const result = ref()
    if (!editMode) {
        result.value = await reasonsStore.createReason(reasonTemplate.value)
    } else {
        result.value = await reasonsStore.updateReason(reasonTemplate.value)
    }

    console.log('store/update: ', result.value)

    if (checkApiAnswer(result.value).code === 0) {
        calloutType.value = 'success'
        calloutMessage.value = 'Сохранено!'
    } else {
        calloutType.value = 'danger'
        calloutMessage.value = 'Упс, что-то пошло не так...'
    }

    calloutShow.value = true
    calloutClose()
}


// __ Запускаем сразу валидацию формы
onMounted(() => {
    getReason()
    v$.value.$touch()
})
</script>

<style scoped></style>
