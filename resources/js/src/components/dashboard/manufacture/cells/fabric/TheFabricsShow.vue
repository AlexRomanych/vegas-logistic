<template>
    <div class="ml-2 mt-2">

        <div v-if="fabrics.length">

            <div class="flex">
                <AppLabel
                    align="center"
                    text="Название ПС"
                    text-size="small"
                    type="primary"
                    width="w-[350px]"
                />

                <AppLabel
                    align="center"
                    text="Рис."
                    text-size="small"
                    type="primary"
                    width="w-[50px]"
                />

                <AppLabel
                    align="center"
                    text="Ткань"
                    text-size="small"
                    type="primary"
                    width="w-[100px]"
                />

                <AppLabel
                    align="center"
                    text="Буфер"
                    text-size="small"
                    type="primary"
                    width="w-[70px]"
                />

                <AppLabel
                    align="center"
                    text="ОПЗ"
                    text-size="small"
                    type="primary"
                    width="w-[70px]"
                />

                <AppLabel
                    align="center"
                    text="Ст. машина"
                    text-size="small"
                    type="primary"
                    width="w-[100px]"
                />

                <AppLabel
                    align="center"
                    text="Статус"
                    text-size="small"
                    type="primary"
                    width="w-[100px]"
                />

                <router-link :to="{name: 'manufacture.cell.fabrics.add'}">
                    <AppLabel
                        align="center"
                        class="cursor-pointer"
                        text="Создать"
                        text-size="normal"
                        type="warning"
                        width="w-[164px]"
                    />
                </router-link>


            </div>

            <div v-for="fabric in fabrics" :key="fabric.id">
                <div class="flex">
                    <AppLabel
                        :text="fabric.display_name"
                        text-size="small"
                        width="w-[350px]"
                    />

                    <AppLabel
                        :text="fabric.pic"
                        align="center"
                        text-size="small"
                        width="w-[50px]"
                    />

                    <AppLabel
                        :text="fabric.textile"
                        align="center"
                        text-size="small"
                        width="w-[100px]"
                    />

                    <AppLabel
                        :text="fabric.buffer.toFixed(2)"
                        align="center"
                        text-size="small"
                        width="w-[70px]"
                    />

                    <AppLabel
                        :text="fabric.optimal_party.toFixed(2)"
                        align="center"
                        text-size="small"
                        width="w-[70px]"
                    />

                    <AppLabel
                        :text="fabric.fabric_machine.short_name"
                        align="center"
                        text-size="small"
                        width="w-[100px]"
                    />

                    <AppLabel
                        :text="fabric.active ? 'Активный' : 'Архив'"
                        align="center"
                        text-size="small"
                        width="w-[100px]"
                    />

                    <router-link :to="{name: 'manufacture.cell.fabric.edit', params: {id: fabric.id}}">
                        <AppLabel
                            align="center"
                            class="cursor-pointer"
                            text="Ред."
                            text-size="small"
                            type="success"
                            width="w-[80px]"
                        />
                    </router-link>

                    <AppLabel
                        align="center"
                        class="cursor-pointer"
                        text="Удалить"
                        text-size="small"
                        type="danger"
                        width="w-[80px]"
                        @click="fabricDelete(fabric.id)"
                    />

                </div>
            </div>
        </div>
        <div v-else>

            <AppLabel
                text="Нет данных"
                type="info"
            />

        </div>


    </div>

    <AppModal
        :show="modalShow"
        mode="confirm"
        text="ПС будет удалено. Продолжить?"
        type="danger"
        @closeModal="closeModal"
    />

    <AppCallout v-if="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />

</template>

<script setup>
import {ref} from "vue";

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'
import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'

const fabricsStore = useFabricsStore()
let fabrics = await fabricsStore.getFabrics()


// console.log('fabrics', fabrics)

const modalShow = ref(false)
const modalAnswer = ref(false)
const calloutShow = ref(false)
const calloutText = ref('')
const calloutType = ref('')

let deleteFabricId = 0
let fabric = ref(null)

// Удаляем работника
const fabricDelete = (id) => {
    modalShow.value = true
    deleteFabricId = id
    // console.log(id)
    // console.log(modalShow.value)
}

// Удаляем рабочего после модального окна
const closeModal = async (answer) => {
    modalAnswer.value = answer
    modalShow.value = false
    // console.log('answer', modalAnswer.value)

    if (answer) {
        const fabric = fabrics.find(fabric => fabric.id === deleteFabricId)
        fabrics = fabrics.filter(fabric => fabric.id !== deleteFabricId)
        const result = await fabricsStore.deleteFabric(deleteFabricId)

        if (result === 'success') {
            calloutType.value = 'success'
            calloutText.value = `${fabric.name} успешно удалено`
        } else {
            calloutType.value = 'danger'
            calloutText.value = 'Упс... Что-то пошло не так...'
        }
        calloutShow.value = true
    }
}


</script>

<style scoped>

</style>
