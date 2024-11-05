<template>
    <div class="w-full h-full bg-slate-600 text-white">
        The Models
    </div>

</template>

<script setup>
console.log('Here')
// import axios from 'axios'


import AppTable from "@/src/components/ui/tables/AppTable.vue";

// const res = axios.get('api/v1/models');
// const data = await res


// console.log(data);
import {jwtGet} from "@/src/app/utils/jwt_api.js";

const res = jwtGet('/models');

</script>

<style scoped>

</style>
