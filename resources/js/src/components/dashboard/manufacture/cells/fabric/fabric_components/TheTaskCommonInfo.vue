<template>
    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!-- attract: Кем и когда создано задание -->
        <div class="mb-2">

            <!--  attract: descr: Группа: Сменное задание-->
            <div>
                <AppLabel
                    text="Сменное задание:"
                    type="success"
                />
            </div>

            <!-- attract: Статус -->
            <div class="flex items-start ml-3">
                <AppLabel
                    text="Статус:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="getTitleByFabricTaskStatusCode(task.common.status)"
                    :type="getStyleTypeByFabricTaskStatusCode(task.common.status)"
                    text-size="mini"
                    type="warning"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Ответственный -->
            <div class="flex items-start ml-3">
                <AppLabel
                    text="Специалист:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="task.common.created_by"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Дата создания -->
            <div class="flex items-start ml-3">
                <AppLabel
                    text="Дата создания:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="formatDateInFullFormat(task.common.created_at)"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <!-- attract: Время создания -->
            <div class="flex items-start ml-3">
                <AppLabel
                    text="Время создания:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    :text="formatTimeInFullFormat(task.common.created_at)"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

        </div>

        <TheDividerLine/>

        <!-- attract: Бригада -->
        <div class="mb-4">

            <div class="flex items-start">
                <AppLabel
                    text="Бригада:"
                    type="success"
                    width="w-[100px]"
                />
                <AppLabel
                    :text="'№ ' + task.common.team.toString()"
                    type="success"
                    width="w-[50px]"
                />
            </div>

            <div class="ml-3">
                <AppLabel
                    text="Иванов И. И."
                    text-size="mini"
                    width="w-[200px]"
                />
                <AppLabel
                    text="Петров П. П."
                    text-size="mini"
                    width="w-[200px]"
                />
                <AppLabel
                    text="Сдоров С. С."
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

        </div>

        <TheDividerLine/>

        <!-- attract: Трудозатраты -->
        <div class="mb-2">
            <div>
                <AppLabel
                    text="Трудозатраты:"
                    type="success"
                />

            </div>

            <div class="flex items-start ml-3">
                <AppLabel
                    text="Американец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    text="03ч. 39м. 59с."
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <div class="flex items-start ml-3">
                <AppLabel
                    text="Немец:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    text="03ч. 39м. 59с."
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

        </div>

    </div>

</template>

<script setup>

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode,

} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import {
    formatTimeWithLeadingZeros,
    formatDateInFullFormat,
    formatTimeInFullFormat
} from '/resources/js/src/app/helpers/helpers_date.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import TheDividerLine
    from '/resources/js/src/components/dashboard/manufacture/cells/fabric/fabric_components/TheDividerLine.vue'

const props = defineProps({
    task: {
        type: Object,
        required: false,
        default: () => ({})
    },
})

console.log(props.task)


</script>

<style scoped>

</style>
