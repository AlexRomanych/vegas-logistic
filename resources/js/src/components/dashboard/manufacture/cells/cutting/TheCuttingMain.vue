<template>
    <div class="flex flex-wrap">
        <div
            v-for="group in menuItems"
            :key="group.path"
        >
            <NavItemCard :groupMenuItem="group"/>
        </div>
    </div>
</template>

<script lang="ts" setup>

import {ref} from 'vue'

import NavItemCard from '@/components/dashboard/nav/NavItemCard.vue'

// prettier-ignore
let menuItems = [
    {name: 'Учет ПС на раскрое', path: 'manufacture.cell.cutting.fabrics.movement', shown: true, isActive: true,},
    // {name: 'Выполнение СЗ', path: 'manufacture.cell.fabric.tasks.execute', shown: true, isActive: true,},
]

// Типизация пропсов через defineProps

type Props = {
    initialValue: number
    isActive?: string
}

// const props = withDefaults(defineProps<Props>(), {
//     initialValue: 0,
//     isActive: false,
// })

const props = withDefaults(defineProps<Props>(), {initialValue: 0, isActive: '1'})

const count = ref(props.initialValue) // `props.initialValue` здесь корректно типизирован

const increment = () => {
    ;(count.value as number)++
}

// menuItems = menuItems.map((item, index) => ({...item, id: index, shown: true, isActive: true}))

interface myObj {
    readonly value: string
    name: string
    path?: string
    shown?: boolean
    isActive?: boolean
}

const a: myObj = {
    name: '',
    value: '1',
}


const b:string = 2
</script>

<style scoped></style>
