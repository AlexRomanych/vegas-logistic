<template>
    <AppInputTextTS
        v-if="renderObject.show"
        :id="renderObject.id ? typeof renderObject.id === 'function' ? renderObject.id(arg) : '' : ''"
        v-model:text-value="internalValue"
        :text-size="renderObject.filterTextSize"
        :type="typeof renderObject.type === 'function' ? renderObject.type(arg) : renderObject.type"
        :width="renderObject.width"
        :placeholder="renderObject.placeholder"
    />
</template>

<script setup lang="ts">

import type { IRenderDataItem } from '@/types'
import type { IColorTypes } from '@/app/constants/colorsClasses.ts'

import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import { computed } from 'vue'

interface IProps {
    renderObject: IRenderDataItem
    modelValue?: string
    headerType?: IColorTypes
    arg?: any
}

const props = withDefaults(defineProps<IProps>(), {
    modelValue: '',
    headerType: 'primary',
    arg: undefined
})

// 2. Объявляем стандартное событие v-model
const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
}>()

// 3. Создаем вычисляемое свойство (computed property) с геттером и сеттером,
//    которое будет служить прокси между modelValue родителя и v-model:text-value дочернего компонента.
const internalValue = computed({
    get() {
        return props.modelValue
    },
    set(value: string) {
        // Применяем модификатор .trim() здесь, прежде чем отправить значение обратно родителю
        const trimmedValue = value.trim()
        emit('update:modelValue', trimmedValue)
    }
})

</script>

<style scoped>

</style>
