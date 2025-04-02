<template>
    <div class="ml-2 mt-2">

        <div v-if="fabrics.length">

            <div class="sticky-header top-0 flex pt-1 pb-1 bg-slate-100">
                <AppLabelMultiLine
                    :text="['Название', 'полотна стеганного']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[310px]"
                />

                <AppLabelMultiLine
                    :text="['Рис.', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[40px]"
                />

                <AppLabelMultiLine
                    :text="['Ткань', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[90px]"
                />

                <AppLabelMultiLine
                    :text="['Буфер', 'м.п.']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[60px]"
                />

                <AppLabelMultiLine
                    :text="['Мин', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Минимальный запас"
                    type="primary"
                    width="w-[60px]"
                />

                <AppLabelMultiLine
                    :text="['Макс', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Максимальный запас"
                    type="primary"
                    width="w-[60px]"
                />

                <AppLabelMultiLine
                    :text="['Ср. дл.', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Средняя длина рулона"
                    type="primary"
                    width="w-[60px]"
                />

                <AppLabelMultiLine
                    :text="['ОПЗ', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Оптимальная партия для запуска"
                    type="primary"
                    width="w-[60px]"
                />

                <AppLabelMultiLine
                    :text="['Основная', 'СМ']"
                    align="center"
                    text-size="small"
                    title="Основная стегальная машина"
                    type="success"
                    width="w-[80px]"
                />

                <AppLabelMultiLine
                    :text="['Альтернат.', 'СМ 1']"
                    align="center"
                    text-size="small"
                    title="Альтернативная стегальная машина 1"
                    type="primary"
                    width="w-[80px]"
                />

                <AppLabelMultiLine
                    :text="['Альтернат.', 'СМ 2']"
                    align="center"
                    text-size="small"
                    title="Альтернативная стегальная машина 2"
                    type="warning"
                    width="w-[80px]"
                />


                <AppLabelMultiLine
                    :text="['Статус', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[80px]"
                />

                <AppLabelMultiLine
                    :text="['Коэфф.', 'ткани']"
                    align="center"
                    text-size="small"
                    title="Коэффициент перевода ткани в ПС"
                    type="primary"
                    width="w-[60px]"
                />

                <AppLabelMultiLine
                    :text="['Описание', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[200px]"
                />

                <router-link :to="{name: 'manufacture.cell.fabrics.add'}">
                    <AppLabelMultiLine
                        :text="['Создать', '']"
                        align="center"
                        class="cursor-pointer"
                        text-size="normal"
                        type="success"
                        width="w-[84px]"
                    />
                </router-link>

            </div>


            <div v-for="fabric in fabrics" :key="fabric.id">
                <div class="flex">
                    <AppLabel
                        :text="fabric.display_name"
                        text-size="mini"
                        width="w-[310px]"
                    />

                    <AppLabel
                        :text="fabric.picture.name"
                        align="center"
                        text-size="mini"
                        width="w-[40px]"
                    />

                    <AppLabel
                        :text="fabric.textile"
                        align="center"
                        text-size="mini"
                        width="w-[90px]"
                    />

                    <AppLabel
                        :text="fabric.buffer.amount.toFixed(2)"
                        :type="!(!fabric.buffer.amount && fabric.active) ? 'dark' : 'danger'"
                        align="center"
                        text-size="mini"
                        width="w-[60px]"
                    />

                    <AppLabel
                        :text="fabric.buffer.min.toFixed(2)"
                        align="center"
                        text-size="mini"
                        width="w-[60px]"
                    />

                    <AppLabel
                        :text="fabric.buffer.max.toFixed(2)"
                        align="center"
                        text-size="mini"
                        width="w-[60px]"
                    />

                    <AppLabel
                        :text="fabric.buffer.average_length.toFixed(3)"
                        :type="!(!fabric.buffer.average_length && fabric.active) ? 'dark' : 'danger'"
                        align="center"
                        text-size="mini"
                        width="w-[60px]"
                    />

                    <AppLabel
                        :text="fabric.buffer.optimal_party.toFixed(2)"
                        align="center"
                        text-size="mini"
                        width="w-[60px]"
                    />

                    <AppLabel
                        :text="fabric.machines[0].short_name"
                        :type="!(!fabric.machines[0].short_name && fabric.active) ? 'success' : 'danger'"
                        align="center"
                        text-size="mini"
                        width="w-[80px]"
                    />

                    <AppLabel
                        :text="fabric.machines[1].id ? fabric.machines[1].short_name : ''"
                        :type="fabric.machines[1].id ? 'primary' : 'dark'"
                        align="center"
                        text-size="mini"
                        width="w-[80px]"
                    />

                    <AppLabel
                        :text="fabric.machines[2].id ? fabric.machines[2].short_name : ''"
                        :type="fabric.machines[2].id ? 'warning' : 'dark'"
                        align="center"
                        text-size="mini"
                        width="w-[80px]"
                    />

                    <AppLabel
                        :text="fabric.active ? 'Активный' : 'Архив'"
                        :type="fabric.active ? 'success' : 'dark'"
                        align="center"
                        text-size="mini"
                        width="w-[80px]"
                    />

                    <AppLabel
                        :text="fabric.buffer.rate.toFixed(3)"
                        :type="!(!fabric.buffer.rate && fabric.active) ? 'dark' : 'danger'"
                        align="center"
                        text-size="mini"
                        width="w-[60px]"
                    />

                    <AppLabel
                        :text="fabric.text.description ? fabric.text.description : '...'"
                        class="truncate"
                        text-size="mini"
                        width="w-[200px]"
                    />

                    <router-link :to="{name: 'manufacture.cell.fabric.edit', params: {id: fabric.id}}">
                        <AppLabel
                            align="center"
                            class="cursor-pointer"
                            text="Ред."
                            text-size="mini"
                            type="warning"
                            width="w-[40px]"
                        />
                    </router-link>

                    <AppLabel
                        align="center"
                        class="cursor-pointer font-bold"
                        text="X"
                        text-size="small"
                        type="danger"
                        width="w-[40px]"
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
import {ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'
import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'

const fabricsStore = useFabricsStore()
let fabrics = await fabricsStore.getFabrics()

// attract: Сортируем по основной стегальной машине
fabrics.sort((fabric1, fabric2) => fabric1.machines[0].short_name.localeCompare(fabric2.machines[0].short_name))
// union.sort((a, b) => a.element.model.localeCompare(b.element.model))

console.log('fabrics', fabrics)

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
.sticky-header {
    position: sticky;
//top: var(--header-height); top: 0; //padding-top: 10px;
}
</style>
