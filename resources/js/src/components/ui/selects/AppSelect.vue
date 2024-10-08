<template>

<!--    <div v-if="selectData.data.length" :class="[width]"-->
<!--         class="border-[2px] rounded-[6px] h-[58px] m-1 p-0 relative bg-slate-600">-->
<!--        <label v-if="label"-->
<!--               :for="selectData.name"-->
<!--               class="absolute text-xs mt-1 ml-1 text-white font-semibold">-->
<!--            {{ label }}-->
<!--        </label>-->

<!--        <select id="" :name="selectData.name" class="m-0 pt-4 h-full w-full rounded-[6px] bg-slate-600 text-white">-->

<!--            <option v-for="item in selectData.data"-->
<!--                    :key="item.id"-->
<!--                    :value="item.id"-->
<!--            >-->
<!--                {{ item.name }}-->
<!--            </option>-->

<!--        </select>-->
<!--    </div>-->
</template>

<script setup>
import {colorsList} from "@/src/app/constants/colorsClasses.js"
import {getColorClassByType, getTextColorClassByType} from "@/src/app/helpers/helpers.js"
import {logicalExpression} from "@babel/types";

const props = defineProps({
    width: {
        type: String,
        required: false,
        default: 'w-[300px]'
    },
    type: {
        type: String,
        required: false,
        default: 'secondary',
        validator: (type) => colorsList.includes(type)
    },
    selectData: {
        type: Object,
        required: false,
        default: {name: Date.now().toString(), data: []},
    },
    label: {
        type: String,
        required: false,
        default: 'Выберите...'
    }

})

console.log(props.selectData)

</script>

<style scoped>

</style>
