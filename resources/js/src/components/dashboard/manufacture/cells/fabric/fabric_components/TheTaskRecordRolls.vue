<template>

    <!-- hr----------------------------------------------------------------------------------------- -->
    <div class="flex">
        <!-- attract: Номер рулона (id записи) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollNumber.show"
            :text="['Номер', 'рулона']"
            :title="rollsRender.rollNumber.title"
            :type="getHeaderType()"
            :width="rollsRender.rollNumber.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Название ПС -->
        <AppLabelMultiLine
            v-if="rollsRender.fabricName.show"
            :text="['Полотно', 'стеганное']"
            :title="rollsRender.fabricName.title"
            :type="getHeaderType()"
            :width="rollsRender.fabricName.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Средняя длина ткани -->
        <AppLabelMultiLine
            v-if="rollsRender.textileLength.show"
            :text="['Длина', 'ткани']"
            :title="rollsRender.textileLength.title"
            :type="getHeaderType()"
            :width="rollsRender.textileLength.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Количество рулонов (всегда = 1) -->
        <AppLabelMultiLine
            v-if="rollsRender.rollsAmount.show"
            :text="['1', '']"
            :title="rollsRender.rollsAmount.title"
            :type="getHeaderType()"
            :width="rollsRender.rollsAmount.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Средние трудозатраты на рулон -->
        <AppLabelMultiLine
            v-if="rollsRender.productivity.show"
            :text="['Трудозатраты', '']"
            :title="rollsRender.productivity.title"
            :type="getHeaderType()"
            :width="rollsRender.productivity.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Комментарий -->
        <AppLabelMultiLine
            v-if="rollsRender.description.show"
            :text="['Комментарий', '']"
            :title="rollsRender.description.title"
            :type="getHeaderType()"
            :width="rollsRender.description.width"
            align="center"
            class="truncate"
            text-size="mini"
        />

        <!-- attract: Статус -->
        <AppLabelMultiLine
            v-if="rollsRender.status.show"
            :text="['Статус', '']"
            :title="rollsRender.status.title"
            :type="getHeaderType()"
            :width="rollsRender.status.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Начало -->
        <AppLabelMultiLine
            v-if="rollsRender.startAt.show"
            :text="['Начало', 'стегания']"
            :title="rollsRender.startAt.title"
            :type="getHeaderType()"
            :width="rollsRender.startAt.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Окончание -->
        <AppLabelMultiLine
            v-if="rollsRender.finishAt.show"
            :text="['Окончание', 'стегания']"
            :title="rollsRender.finishAt.title"
            :type="getHeaderType()"
            :width="rollsRender.finishAt.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Время стегания -->
        <AppLabelMultiLine
            v-if="rollsRender.rollTime.show"
            :text="['Время', 'стегания']"
            :title="rollsRender.rollTime.title"
            :type="getHeaderType()"
            :width="rollsRender.rollTime.width"
            align="center"
            text-size="mini"
        />

        <!-- attract: Ответственный -->
        <AppLabelMultiLine
            v-if="rollsRender.finishBy.show"
            :text="['Ответственный', '']"
            :title="rollsRender.finishBy.title"
            :type="getHeaderType()"
            :width="rollsRender.finishBy.width"
            align="center"
            text-size="mini"
        />
    </div>
    <!-- hr----------------------------------------------------------------------------------------- -->


    <div v-for="(roll, index) in rolls" :key="index">

        <div v-for="(roll_exec) in roll.rolls_exec" :key="roll_exec.id">
            <div class="flex">

                <!-- attract: Номер рулона (id записи) -->
                <AppLabel
                    v-if="rollsRender.rollNumber.show"
                    :text="rollsRender.rollNumber.data(roll_exec)"
                    :title="rollsRender.rollNumber.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.rollNumber.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Название ПС -->
                <AppLabel
                    v-if="rollsRender.fabricName.show"
                    :text="rollsRender.fabricName.data(roll_exec)"
                    :title="rollsRender.fabricName.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.fabricName.width"
                    text-size="mini"
                />

                <!-- attract: Средняя длина ткани -->
                <AppLabel
                    v-if="rollsRender.textileLength.show"
                    :text="rollsRender.textileLength.data(roll_exec)"
                    :title="rollsRender.textileLength.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.textileLength.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Количество рулонов (всегда = 1) -->
                <AppLabel
                    v-if="rollsRender.rollsAmount.show"
                    :text="rollsRender.rollsAmount.data()"
                    :title="rollsRender.rollsAmount.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.rollsAmount.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Средние трудозатраты на рулон -->
                <AppLabel
                    v-if="rollsRender.productivity.show"
                    :text="rollsRender.productivity.data(roll_exec)"
                    :title="rollsRender.productivity.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.productivity.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Комментарий -->
                <AppLabel
                    v-if="rollsRender.description.show"
                    :text="rollsRender.description.data(roll_exec)"
                    :title="rollsRender.description.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.description.width"
                    class="truncate"
                    text-size="mini"
                />

                <!-- attract: Статус -->
                <AppLabel
                    v-if="rollsRender.status.show"
                    :text="rollsRender.status.data(roll_exec)"
                    :title="rollsRender.status.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.status.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Начало -->
                <AppLabel
                    v-if="rollsRender.startAt.show"
                    :text="rollsRender.startAt.data(roll_exec)"
                    :title="rollsRender.startAt.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.startAt.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Окончание -->
                <AppLabel
                    v-if="rollsRender.finishAt.show"
                    :text="rollsRender.finishAt.data(roll_exec)"
                    :title="rollsRender.finishAt.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.finishAt.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Время стегания -->
                <AppLabel
                    v-if="rollsRender.rollTime.show"
                    :text="rollsRender.rollTime.data(roll_exec)"
                    :title="rollsRender.rollTime.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.rollTime.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Ответственный -->
                <AppLabel
                    v-if="rollsRender.finishBy.show"
                    :text="rollsRender.finishBy.data(roll_exec)"
                    :title="rollsRender.finishBy.title"
                    :type="getTypeByStatus(roll_exec)"
                    :width="rollsRender.finishBy.width"
                    text-size="mini"
                />

            </div>
        </div>


    </div>
</template>

<script setup>
import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_ROLL_STATUS, FABRIC_ROLL_STATUS_LIST} from '/resources/js/src/app/constants/fabrics.js'

import {formatTimeWithLeadingZeros} from '/resources/js/src/app/helpers/helpers_date.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'

const props = defineProps({
    rolls: {
        type: Array,
        required: false,
        default: []
    }
})

console.log(props.rolls)

const FABRIC_ROLL_STATUS_ARRAY = Object.values(FABRIC_ROLL_STATUS_LIST);
// console.log(FABRIC_ROLL_STATUS_ARRAY)

// attract: Получаем данные из хранилища по ПС
const fabricsStore = useFabricsStore()
const fabrics = fabricsStore.fabricsMemory

// attract: Получаем ПС по ID
// const getFabricById = (fabric_id) => fabrics.find((fabric) => fabric.id === fabric_id)

// attract: Получаем тип раскраски в зависимости от статуса выполнения рулона
const getTypeByStatus = (roll_exec) => {
    return 'success'
}

// attract: Получаем тип шапки таблицы
const getHeaderType = () => 'primary'

// attract: Задаем глобальный объект для унификации отображения рулонов
const rollsRender = {
    rollNumber: {width: 'w-[60px]', show: true, title: '№ рулона', data: (roll_exec) => roll_exec.id.toString()},
    fabricName: {
        width: 'w-[270px]',
        show: true,
        title: 'Название ПС',
        data: (roll_exec) => fabrics.find((fabric) => fabric.id === roll_exec.fabric_id).display_name
    },
    textileLength: {
        width: 'w-[50px]',
        show: true,
        title: 'Средняя длина ткани, м.п.',
        data: (roll_exec) => roll_exec.textile_length.toFixed(2)
    },
    rollsAmount: {width: 'w-[30px]', show: true, title: 'Кол-во рулонов, шт.', data: () => '1'},
    productivity: {
        width: 'w-[90px]',
        show: true,
        title: 'Средние трудозатраты, ч.',
        data: (roll_exec) => formatTimeWithLeadingZeros(roll_exec.textile_length / roll_exec.productivity, 'hour')
    },
    description: {width: 'w-[120px]', show: true, title: 'Комментарий', data: (roll_exec) => roll_exec.descr},
    status: {
        width: 'w-[70px]',
        show: true,
        title: 'Статус',
        data: (roll_exec) => FABRIC_ROLL_STATUS_ARRAY[roll_exec.status].TITLE
    },
    startAt: {
        width: 'w-[110px]',
        show: true,
        title: 'Начало стегания рулона',
        data: (roll_exec) => '16.04.2023 10:59'
    },
    finishAt: {
        width: 'w-[110px]',
        show: true,
        title: 'Окончание стегания рулона',
        data: (roll_exec) => '16.04.2023 12:38'
    },
    rollTime: {width: 'w-[90px]', show: true, title: 'Время стегания', data: (roll_exec) => '01ч. 59м. 59с.'},
    finishBy: {width: 'w-[110px]', show: true, title: 'Ответственный', data: (roll_exec) => 'Сидорук И.И.'},
}

</script>

<style scoped>

</style>
