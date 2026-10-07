<template>
    <template v-if="workDay && workDay?.comment">

        <!-- __ Комментарий к дню -->
        <AppLabelTS
            :text="workDay?.comment ?? ''"
            align="left"
            class="start-group"
            height="h-[50px]"
            rounded="4"
            text-size="mini"
            type="warning"
            width="min-w-[600px]"
        />

        <template v-if="showType === 'callout'">
            <AppCalloutTS
                :show="calloutShow"
                :text="calloutMessage"
                :type="calloutType"
                height="h-[200px]"
                text-size="huge"
                width="w-[800px]"
            />
        </template>
        <template v-else-if="showType === 'modal'">
            <!-- __ Модальное окно для сообщений -->
            <AppModalAsyncMultilineTS
                ref="appModalAsyncMultilineTS"
                :mode="modalInfoMode"
                :text="calloutMessage"
                :type="modalInfoType"
                width="w-[800px]"
                height="h-[400px]"
                ok-word="Понятно"
                align="center"
            />
        </template>

    </template>
</template>

<script lang="ts" setup>
import { ref, watch, computed } from 'vue'

import type { IAssemblyDay, IBlockDay, IColorTypes, ICuttingDay, ISewingDay } from '@/types'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppCalloutTS from '@/components/ui/callouts/AppCalloutTS.vue'
import AppModalAsyncMultilineTS from '@/components/ui/modals/AppModalAsyncMultilineTS.vue'

interface IProps {
    workDay: ISewingDay | ICuttingDay| IBlockDay | IAssemblyDay | null | undefined
    showType?: 'none' | 'callout' | 'modal'
    calloutDelay?: number       // __ Задержка перед показом Комментария
    calloutDuration?: number    // __ Длительность показа Комментария
    calloutType?: IColorTypes
}

const props = withDefaults(defineProps<IProps>(), {
    showType       : 'none',
    calloutDelay   : 2,
    calloutDuration: 10,
    calloutType    : 'warning',
})

const calloutMessage = computed(() => props.workDay?.comment || '')      // определяем показываемое сообщение

// __ Тип для модального окна Сообщений
const modalInfoType            = ref<IColorTypes>(props.calloutType)
// const modalInfoText            = ref<string | string[]>(props.workDay?.comment || '')
const modalInfoMode            = ref<'inform' | 'confirm'>('inform')
const appModalAsyncMultilineTS = ref<InstanceType<typeof AppModalAsyncMultilineTS> | null>(null) // Получаем ссылку на модальное окно с асинхронной функцией

const showModal = async () => {
    if (props.showType === 'modal' && props.workDay?.comment) {
        await appModalAsyncMultilineTS.value!.show()
    }
}

// __ Callout
const calloutShow    = ref(false)      // состояние окна



watch(() => props.workDay, async () => {
    // console.log('props.workDay?.comment: ', props.workDay?.comment)
    if (props.showType === 'callout' && props.workDay?.comment) {
        setTimeout(() => calloutShow.value = true, props.calloutDelay * 1000)
        setTimeout(() => calloutShow.value = false, (props.calloutDelay + props.calloutDuration) * 1000)
    } else if (props.showType === 'modal' && props.workDay?.comment) {
        setTimeout(async () => await showModal(), props.calloutDelay * 1000)
    }
}, { immediate: true })

</script>

<style scoped>

</style>
