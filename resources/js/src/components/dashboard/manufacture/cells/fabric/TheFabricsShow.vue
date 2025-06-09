<template>
    <div class="ml-2 mt-2">


        <div class="sticky top-0 pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">


            <div class="flex">

                <!-- attract: Код по 1С -->
                <AppLabelMultiLine
                    :text="['Код в 1С', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[100px]"
                />

                <!-- attract: ПС -->
                <AppLabelMultiLine
                    :text="['Название', 'полотна стеганного']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[310px]"
                />

                <!-- attract: Рисунок -->
                <AppLabelMultiLine
                    :text="['Рис.', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[40px]"
                />

                <!-- attract: Ткань -->
                <AppLabelMultiLine
                    :text="['Ткань', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[90px]"
                />

                <!-- attract: Буфер -->
                <AppLabelMultiLine
                    :text="['Буфер', 'м.п.']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Минимальный запас -->
                <AppLabelMultiLine
                    :text="['Мин', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Минимальный запас"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Максимальный запас -->
                <AppLabelMultiLine
                    :text="['Макс', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Максимальный запас"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Средняя длина рулона -->
                <AppLabelMultiLine
                    :text="['Ср. дл.', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Средняя длина рулона"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Оптимальная партия для запуска -->
                <AppLabelMultiLine
                    :text="['ОПЗ', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Оптимальная партия для запуска"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Производительность -->
                <AppLabelMultiLine
                    :text="['Пр-сть', 'м.п./ч.']"
                    align="center"
                    text-size="small"
                    title="Производительность"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Основная СМ -->
                <AppLabelMultiLine
                    :text="['Основная', 'СМ']"
                    align="center"
                    text-size="small"
                    title="Основная стегальная машина"
                    type="success"
                    width="w-[80px]"
                />

                <!-- attract: Альтернативная СМ 1 -->
                <AppLabelMultiLine
                    :text="['Альтернат.', 'СМ 1']"
                    align="center"
                    text-size="small"
                    title="Альтернативная стегальная машина 1"
                    type="primary"
                    width="w-[80px]"
                />

                <!-- attract: Альтернативная СМ 2 -->
                <AppLabelMultiLine
                    :text="['Альтернат.', 'СМ 2']"
                    align="center"
                    text-size="small"
                    title="Альтернативная стегальная машина 2"
                    type="warning"
                    width="w-[80px]"
                />

                <!-- attract: Статус -->
                <AppLabelMultiLine
                    :text="['Статус', '']"
                    align="center"
                    text-size="small"
                    tile="Статус"
                    type="primary"
                    width="w-[80px]"
                />

                <!-- attract: Коэффициент перевода ткани в ПС -->
                <AppLabelMultiLine
                    :text="['Коэфф.', 'ткани']"
                    align="center"
                    text-size="small"
                    title="Коэффициент перевода ткани в ПС"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Описание -->
                <AppLabelMultiLine
                    :text="['Описание', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[200px]"
                    ешеду="Описание"
                />

                <!-- attract: Кнопка создать -->
                <router-link :to="{name: 'manufacture.cell.fabrics.add'}">
                    <AppLabelMultiLine
                        :text="['Создать', '']"
                        align="center"
                        class="cursor-pointer"
                        text-size="normal"
                        type="success"
                        width="w-[100px]"
                    />
                </router-link>

            </div>

            <div class="flex">

                <!-- attract: Фильтр: Код 1С -->
                <AppInputText
                    id="contract-search"
                    v-model.trim="codeFilter"
                    placeholder="Код 1С"
                    text-size="mini"
                    type="primary"
                    width="w-[100px]"
                />

                <!-- attract: Фильтр: название ПС -->
                <AppInputText
                    id="name-search"
                    v-model.trim="nameFilter"
                    placeholder="Название ПС"
                    text-size="mini"
                    type="primary"
                    width="w-[310px]"
                />

            </div>

        </div>

        <!-- attract: Сами данные -->
        <div v-if="fabrics.length">

            <div class="pt-1 pb-1 bg-slate-200 border-2 rounded-lg border-slate-700 p-1 mb-1 max-w-fit">

                <div v-for="fabric in fabrics" :key="fabric.id">
                    <div class="flex">

                        <AppLabel
                            :text="fabric.code_1C"
                            align="center"
                            text-size="mini"
                            width="w-[100px]"
                        />


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
                            :type="!(!round(fabric.buffer.amount, 0) && fabric.active) ? 'dark' : 'danger'"
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
                            :text="fabric.buffer.productivity.toFixed(3)"
                            :type="!(!fabric.buffer.productivity && fabric.active) ? 'dark' : 'danger'"
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
                                text="Редактировать"
                                text-size="mini"
                                type="warning"
                                width="w-[100px]"
                            />
                        </router-link>

<!--                        <AppLabel-->
<!--                            align="center"-->
<!--                            class="cursor-pointer font-bold"-->
<!--                            text="X"-->
<!--                            text-size="small"-->
<!--                            type="danger"-->
<!--                            width="w-[40px]"-->
<!--                            @click="fabricDelete(fabric.id)"-->
<!--                        />-->

                    </div>
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
import {ref, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {round} from '/resources/js/src/app/helpers/helpers_lib.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'
import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppInputText from '/resources/js/src/components/ui/inputs/AppInputText.vue'


const fabricsStore = useFabricsStore()

const getFabrics = async () => await fabricsStore.getFabrics()
const allFabrics = ref(await getFabrics())
const fabrics = ref(allFabrics.value)

// let fabrics = await fabricsStore.getFabrics()

// attract: Сортируем по основной стегальной машине
fabrics.value.sort((fabric1, fabric2) => fabric1.machines[0].short_name.localeCompare(fabric2.machines[0].short_name))
fabrics.value.sort(fabric => fabric.active, true)

// union.sort((a, b) => a.element.model.localeCompare(b.element.model))

// attract: Переменные для фильтрации
const codeFilter = ref('')
const nameFilter = ref('')


console.log('fabrics', fabrics.value)

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
        const fabric = fabrics.value.find(fabric => fabric.id === deleteFabricId)
        fabrics.value = fabrics.value.filter(fabric => fabric.id !== deleteFabricId)
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


// attract: Реализация фильтра
watch([
    () => codeFilter.value,
    () => nameFilter.value
], ([newCodeFilter, newNameFilter]) => {

    fabrics.value = allFabrics.value
        .filter(fabric => fabric.code_1C.includes(newCodeFilter.toLowerCase()))
        .filter(fabric => fabric.display_name.toLowerCase().includes(newNameFilter.toLowerCase()))

}, {deep: true})


</script>

<style scoped>
.sticky-header {
    position: sticky;
    /* top: var(--header-height); top: 0; */
    /* padding-top: 10px; */
}
</style>
