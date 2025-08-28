<template>

</template>


<script setup lang="ts">
import { ref, reactive, onMounted, /*watch,*/ } from 'vue'
// import { onBeforeRouteLeave, onBeforeRouteUpdate } from 'vue-router'
// import { storeToRefs } from 'pinia'
//
// import { useFabricsStore } from '@/stores/FabricsStore.js'
//
// import {
//     FABRIC_TASK_STATUS,
//     FABRIC_MACHINES,
//     FABRICS_NULLABLE,
//     FABRIC_TASKS_EXECUTE, FABRIC_ROLL_STATUS,
//     TASK_TAB_PREFIX,
// } from '@/app/constants/fabrics.js'
//
// import {
//     getTitleByFabricTaskStatusCode,
//     getStyleTypeByFabricTaskStatusCode,
//     getFabricTasksPeriod,
// } from '@/app/helpers/manufacture/helpers_fabric.js'
//
// import {
//     getDayOfWeek,
//     formatDate,
//     isToday,
//     isWorkingDay,
// } from '@/app/helpers/helpers_date.js'
//
// import { catchErrorHandler } from '@/app/helpers/helpers_checks.ts'
//
// import TheTaskCommonInfo
//     from '@/components/dashboard/manufacture/cells/fabric/fabric_components/TheTaskCommonInfo.vue'
// import TheTaskExecuteMachine
//     from '@/components/dashboard/manufacture/cells/fabric/fabric_components/fabric_execute/TheTaskExecuteMachine.vue'
//
// import AppLabel from '@/components/ui/labels/AppLabel.vue'
// import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
// import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
// import AppCallout from '@/components/ui/callouts/AppCallout.vue'


// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'
import { useFabricsStore } from '@/stores/FabricsStore'
import { getFabricTasksPeriod } from '@/app/helpers/manufacture/helpers_fabric'


const isLoading = ref(true)
// __ End Loader

const fabricsStore = useFabricsStore()

// __ Подготавливаем переменные
let fabrics = []  // загружаем после монтирования
let tasksPeriod = null
let tasks = []
const taskData = ref(null)
const activeTask = ref(null)


// __ Получаем все ткани и запоминаем в хранилище
const getFabrics = async () => {
    fabrics = await fabricsStore.getFabrics(true)
}


// __ Получаем период отображения сменного задания
const getTasksPeriod = () => tasksPeriod = getFabricTasksPeriod()

// __ Получаем сами сменные задания
const getTasks = async () => tasks = await fabricsStore.getTasksByPeriod(tasksPeriod)



onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getFabrics()                              // Получаем список ПС
            getTasksPeriod()
            await prepareTasksData()                        // Получаем список СЗ
            // resetTabs()                                     // сбрасываем все табы
            // tabs.common.shown = true                        // делаем вкладку "общие данные" активной, чтобы запустить реактивность
            // getActiveTaskAndTab()                           // Получаем активное СЗ и вкладку из LocalStorage
            // setActiveTaskAndTab()                           // Устанавливаем активное СЗ и вкладку в LocalStorage
            // fabrics.globalOrderExecuteChangeFlag = false    // сбрасываем флаг изменения порядка рулонов
        },
        undefined,
        // false,
    )
    isLoading.value = false
})

</script>


<style scoped>

</style>

