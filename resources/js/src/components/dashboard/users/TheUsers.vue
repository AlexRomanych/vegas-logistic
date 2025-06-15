<template></template>

<script setup>
import AppTable from '@/components/ui/tables/AppTable.vue'

import { jwtGet } from '@/app/utils/jwt_api.js'

const res = await jwtGet('/models')
</script>

<style scoped></style>
