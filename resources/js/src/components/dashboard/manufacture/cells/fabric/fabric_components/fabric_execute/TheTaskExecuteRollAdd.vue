<template>
    <Teleport to="body">

        <div v-if="showModal"
             class="dark-container">

            <div :class="[width, height, borderColor, 'modal-container']">

                <!-- attract: Крестик закрытия окна -->
                <div class="close-cross-container">
                    <div class="m-1 p-1">
                        <AppInputButton
                            id="close"
                            :type="type"
                            height="w-5"
                            title="x"
                            width="w-[30px]"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>

                <!-- attract: Информационный текст -->
                <div class="m-2">
                    <AppLabelMultiLine
                        v-if="text.length"
                        :text="text"
                        :type="type"
                        align="center"
                        height="h-[200px]"
                        width="w-[500px]"
                    />
                </div>

                <!-- attract: Выбор ПС -->
                <div class="m-2">

                    <!-- attract: Список ПС -->
                    <AppSelect
                        :multiple="false"
                        :selectData="selectData"
                        :type="type"
                        :width="width"
                        label="Выберите Полотно стеганное"
                        @change="changeHandler"
                    />

                </div>

                <div class="w-full h-full flex justify-end pt-4">

                    <div v-if="mode === 'confirm'" class="m-1 p-1">

                        <AppInputButton
                            v-if="saveButtonState"
                            id="confirm"
                            :type="type"
                            title="Сохранить"
                            @buttonClick="select(true)"
                        />

                    </div>

                    <div class="m-1 p-1">

                        <AppInputButton
                            id="confirm"
                            :title="mode === 'confirm' ? 'Отмена' : 'Закрыть'"
                            :type="type"
                            @buttonClick="select(false)"
                        />

                    </div>

                </div>

            </div>

        </div>

    </Teleport>
</template>

<script setup>

import {computed, ref} from 'vue'

import {colorsList} from '/resources/js/src/app/constants/colorsClasses.js'
import {getColorClassByType} from '/resources/js/src/app/helpers/helpers.js'

import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'
// import AppInputNumber from '/resources/js/src/components/ui/inputs/AppInputNumber.vue'
// import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'

const props = defineProps({
    width: {
        type: String,
        required: false,
        default: 'min-w-[500px]',
    },
    height: {
        type: String,
        required: false,
        default: 'min-h-[300px]',
    },
    type: {
        type: String,
        required: false,
        default: 'primary',
        validator: (type) => colorsList.includes(type)
    },
    text: {
        type: Array,
        required: false,
        default: () => [],
        validator: (text) => Array.isArray(text)
    },
    mode: {
        type: String,
        required: false,
        default: 'confirm',
        validator: (mode) => ['inform', 'confirm'].includes(mode)
    },
    fabrics: {
        type: Array,
        required: false,
        default: () => [],
        validator: (fabrics) => Array.isArray(fabrics)
    },

})



const emit = defineEmits(['select'])

// attract: Выбранное ПС
const selectedFabric = ref(0)

// attract: Формируем объект для отображения выпадающего списка
const getSelectData = (inData = []) => {
    const selectData = {
        name: 'fabrics',
        data: inData
            .map(item => ({id: item.id, name: item.display_name}))
        // data: [
        //     {id: 3, name: 'Three', selected: true},
        //     {id: 4, name: 'Four', disabled: true},
        // ]
    }

    if (selectData.data.length) {
        selectData.data[1].selected = true              // Выбираем первый элемент по умолчанию}
        selectedFabric.value = selectData.data[1].id    // Запоминаем id выбранного элемента
    }
    return selectData
}
const selectData = ref(getSelectData(props.fabrics))

const showModal = ref(false)                    // реактивность видимости модального окна

const getSaveButtonState = () => true
const saveButtonState = ref(getSaveButtonState())      // реактивность состояния кнопки "Сохранить" в зависимости от area

// attract: Обработка асинхронности
let resolvePromise
const show = (inSelectData) => {                  // Передаем сюда значение для select, потому что может быть не определен props на момент вызова. Это своего рода подстраховка

    selectData.value = getSelectData(inSelectData)
    console.log('selectData show: ', inSelectData)

    showModal.value = true;
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value) => {
    if (resolvePromise) {
        resolvePromise(value)
        // resolvePromise( {value, text: targetText.value})    // возвращает объект, можно так возвращать результаты
        showModal.value = false
        resolvePromise = null
    }
}

// attract: даем родителю доступ к методам и свойствам компонента
defineExpose({
    show,
    selectedFabric,
})


// attract: Обработка события выбора ПС
const changeHandler = (inSelectedFabric) => {
    // console.log('changeHandler: ', inSelectedFabric)
    selectedFabric.value = inSelectedFabric.id
}

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

</script>

<style scoped>

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8
}

.close-cross-container {
    @apply flex justify-end w-full h-full
}

</style>
