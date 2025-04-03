<template>

    <!-- attract: Режим отображения + Режим редактирования -->
    <div class="flex">

        <!-- attract: Номер рулона -->
        <AppLabelMultiLine
            :text="!editMode ? roll.num.toString() : [roll.num.toString(), '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[80px]"
        />

        <!-- attract: ПС -->
        <div v-if="!editMode">
            <AppLabelMultiLine
                :text="!editMode ? roll.fabric : [roll.fabric, '']"
                height="h-[30px]"
                text-size="mini"
                type="primary"
                width="w-[300px]"
            />
        </div>
        <div v-else>
            <AppSelect
                :multiple="false"
                :selectData="selectData"
                label="Выберите ПС:"
                text-size="mini"
                type="primary"
                width="w-[304px]"
                height="h-[64px]"
                @change="selectHandler"
            />
        </div>

        <!-- attract: Средняя длина рулона -->
        <AppLabelMultiLine
            :text="!editMode ? '100.8' : ['100.8', '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[100px]"
        />

        <!-- attract: Количество в рулонах -->
        <AppLabelMultiLine
            :text="!editMode ? '2' : ['2', '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            type="primary"
            width="w-[70px]"
        />

        <!-- attract: Количество в м.п. -->
        <AppLabelMultiLine
            :text="!editMode ? '201.6' : ['201.6', '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            type="primary"
            width="w-[70px]"
        />

        <!-- attract: Трудозатраты -->
        <AppLabelMultiLine
            :text="!editMode ? '208' : ['208', '']"
            align="center"
            height="h-[30px]"
            text-size="mini"
            width="w-[80px]"
        />

        <!-- attract: Комментарий -->
        <div v-if="!editMode">
            <AppLabel
                :text="roll.descr"
                class="truncate"
                height="h-[30px]"
                text-size="mini"
                type="primary"
                width="w-[300px]"
            />
        </div>
        <div v-else>
            <AppInputTextArea
                id="comment"
                :rows=2
                :value="roll.descr"
                class="cursor-pointer"
                height="min-h-[60px]"
                text-size="mini"
                type="primary"
                width="w-[304px]"
            />
        </div>

        <!-- attract: Управляющие кнопки -->
        <div v-if="!editMode" class="flex">

            <!-- attract: Редактировать -->
            <AppLabel
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text="Ред."
                text-size="mini"
                type="warning"
                width="w-[50px]"
                @click="setEditMode"
            />

            <!-- attract: Удалить -->
            <AppLabel
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text="Х"
                text-size="mini"
                type="danger"
                width="w-[50px]"
            />

        </div>

        <div v-else class="flex">

            <!-- attract: Отменить -->
            <AppLabelMultiLine
                :text="['Отмена', '']"
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text-size="mini"
                type="warning"
                width="w-[50px]"
                @click="cancelEditMode"
            />

            <!-- attract: Сохранить -->
            <AppLabelMultiLine
                :text="['V', '']"
                align="center"
                class="cursor-pointer font-bold"
                height="h-[30px]"
                text-size="mini"
                type="success"
                width="w-[50px]"
                @click="saveTaskRecord"
            />

        </div>

    </div>

</template>

<script setup>

import {ref} from 'vue'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'

const props = defineProps({
    roll: {
        type: Object,
        required: false,
        default: () => ({})
    },
    editMode: {
        type: Boolean,
        required: false,
        default: false
    },
    selectData: {
        type: Object,
        required: false,
        default: () => ({})
    }
})

// console.log('editMode: ', props.editMode)
// console.log('selectData: ', props.selectData)

const emit = defineEmits(['setEditMode'])

const editMode = ref(false)

const selectHandler = (item) => console.log(item)

const setEditMode = () => editMode.value = true
const cancelEditMode = () => editMode.value = false

const saveTaskRecord = () => {
    emit('saveTaskRecord', false)
}

</script>

<style scoped>

</style>
