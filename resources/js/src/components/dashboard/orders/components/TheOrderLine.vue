<template>

        <tr>
            <td class="w-[130px] text-center">{{ line.size }}</td>
            <td class="w-[250px]">{{ line.model }}</td>
            <td class="w-[20px] text-right">{{ line.amount }}</td>
            <td class="pl-3">{{ line.textile }}</td>
            <td>{{ line.comment }}</td>
            <td>{{ line.describe_1 }}</td>
            <td>{{ line.describe_2 }}</td>
            <td>{{ line.describe_3 }}</td>
        </tr>

</template>

<script setup>

const props = defineProps({
    line: {
        type: Object,
        required: true
    }
})

</script>

<style scoped>

</style>
