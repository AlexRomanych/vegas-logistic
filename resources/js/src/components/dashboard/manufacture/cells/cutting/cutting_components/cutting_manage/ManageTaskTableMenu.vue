<template>
    <div class="flex h-full ml-[20px]">

        <!-- __ Показать Детали (Ткань, ТКЧ, Кант) -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="showDetails ? MENU_ITEMS_ACTIVE_TYPE : MENU_ITEMS_TYPE"
            class="field"
            text="Детали"
            width="w-[60px]"
            @click="emits('showDetails')"
        />

        <!-- __ Обновить -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Обновить"
            width="w-[65px]"
            @click="reloadData"
        />

        <!-- __ Добавить комментарий к СЗ -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="cuttingTask.comment ? 'orange' : MENU_ITEMS_TYPE"
            class="field"
            text="Комментарий"
            width="w-[90px]"
            @click="emits('addComment')"
        />

    </div>


    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

</template>

<script lang="ts" setup>
import { ref } from 'vue'

import type { IColorTypes, ICuttingTablePanel, ICuttingTask, ICuttingTaskLine } from '@/types'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'


interface IProps {
    activePanel: ICuttingTablePanel
    cuttingLines: ICuttingTaskLine[],
    cuttingTask: ICuttingTask,
    showDetails?: boolean,
}

/*const props =*/ withDefaults(defineProps<IProps>(), {
    showComments: false,
    showDetails:  false,
})

const emits = defineEmits<{
    (e: 'showDetails'): void                    // __ Показать Детали (Ткань, ТКЧ, Кант)
    (e: 'reloadData'): void                     // __ Перегрузить данные
    (e: 'addComment'): void                     // __ Добавить комментарий к СЗ
}>()


// __ Константы панелей меню
const MENU_ITEMS_HEIGHT      = 'h-[35px]'
const MENU_ITEMS_TYPE        = 'primary'
const MENU_ITEMS_ACTIVE_TYPE = 'success'
const MENU_ITEMS_ALIGN       = 'center'
const MENU_ITEMS_TEXT_SIZE   = 'mini'


// __ Тип для модального окна
const modalType              = ref<IColorTypes>('danger')
const modalText              = ref<string| string[]>('')
const modalMode              = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией


// __ Перегрузить данные
const reloadData = async () => {
    modalText.value = ['Все изменения будут потеряны и данные будут обновлены.', 'Продолжить?']
    modalType.value = 'danger'
    const answer    = await appModalAsyncMultiline.value!.show()             // показываем модалку и ждем ответ
    if (answer) {
        emits('reloadData')
    }
}


</script>

<style scoped>
.field {
    @apply cursor-pointer;
}
</style>
