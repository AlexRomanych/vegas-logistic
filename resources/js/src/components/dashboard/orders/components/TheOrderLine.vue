<template>

        <tr>
            <td>{{ line.size }}</td>
            <td>{{ line.model }}</td>
            <td>{{ line.amount }}</td>
            <td>{{ line.textile }}</td>
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
