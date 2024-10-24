<template>
    <div :class="[width, 'table-container']">

        <table :class="[textSizeClass, 'table']">

            <caption v-if="caption" class="caption-container">
                {{ caption }}
            </caption>

            <thead v-if="headers" class="text-left ">
                <tr class="h-10">
                    <th v-for="header in headers" :key="header" scope="col">{{ header }}</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(row, index) in data" :key="index">
                    <td v-for="(value, key) in row" :key="key">{{ value }}</td>
                </tr>
            </tbody>

        </table>

        <div class="h-4"></div>

    </div>
</template>

<script setup>

const props = defineProps({
    width: {
        type: String,
        required: false,
        default: 'w-[300px]'
    },
    caption: {
        type: String,
        required: false,
        default: ''
    },
    tableData: {
        type: Object,
        required: false,
        default: {}
    },
    textSize: {
        type: String,
        required: false,
        default: 'normal',
        validator: (size) => ['mini', 'normal', 'small', 'large', 'huge'].includes(size)
    }


})

let textSizeClass = ''
switch(props.textSize) {
    case 'mini':
        textSizeClass = 'text-xs'
        break
    case 'small':
        textSizeClass = 'text-sm'
        break
    case 'normal':
        textSizeClass = 'text-base'
        break
    case 'large':
        textSizeClass = 'text-lg'
        break
    case 'huge':
        textSizeClass = 'text-xl'
        break
    default:
        textSizeClass = 'text-base'
        break
}

const {headers, data} = props.tableData

if ('headers' in props.tableData) {
    headers.unshift('#')
}

if ('data' in props.tableData) {
    data.forEach((row, index) => row.unshift(index + 1))
}

</script>

<style scoped>
tr {
    @apply border-b border-slate-300
}

th {
    vertical-align: bottom;
}

tbody > tr:nth-of-type(even) {
 /*   background-color: rgb(237 238 242); */
    @apply bg-slate-200
}

.table-container {
    @apply m-1 p-1 border border-slate-300 rounded-lg
}

.table {
    @apply w-full table-auto border-collapse
}

.caption-container {
    @apply caption-bottom text-center text-xs mt-1 font-semibold
}
</style>
