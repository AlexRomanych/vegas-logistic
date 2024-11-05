<template>

</template>

<script setup>
import AppTable from "@/src/components/ui/tables/AppTable.vue";

import {jwtGet} from "@/src/app/utils/jwt_api.js";

const res = await jwtGet('/models');

</script>

<style scoped>

</style>
