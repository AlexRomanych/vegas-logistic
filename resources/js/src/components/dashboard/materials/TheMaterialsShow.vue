<template>

    <div>
        <div v-for="material of materials">
            <div class="w-max-[400px] border-2 m-1">
                {{ material.code_1c + ' | ' + material.name }}
            </div>
        </div>

    </div>

</template>

<script lang="ts" setup>
import { useMaterialsStore } from '@/stores/MaterialsStore.ts'
import { ref } from 'vue'

const materialsStore = useMaterialsStore()
const materials = ref(await materialsStore.getMaterials())


console.log('materials: ', materials.value)

</script>

<style scoped>

</style>
