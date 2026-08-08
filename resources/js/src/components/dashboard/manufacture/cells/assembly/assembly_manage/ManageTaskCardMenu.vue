<template>
    <div class="flex h-full ml-[20px]">

        <!-- __ Переместить все элементы в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text="activePanel === 'left' ? ' Все ▶' : '◀ Все'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', 'all')"
        />

        <!-- __ Переместить все элементы Линии 1 в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text="activePanel === 'left' ? ' Линия 1 ▶' : '◀ Линия 1'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', BLOCK_MANUF_LINES.LINE_1)"
        />

        <!-- __ Переместить все элементы Линии 2 в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text="activePanel === 'left' ? ' Линия 2 ▶' : '◀ Линия 2'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', BLOCK_MANUF_LINES.LINE_2)"
        />


        <!-- __ Переместить все элементы Стола ?? в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text="activePanel === 'left' ? ' Линия ?? ▶' : '◀ Линия ??'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', BLOCK_MANUF_LINES.LINE_0)"
        />

        <!-- __ Разбить количество -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="◀ Изменить кол-во ▶"
            width="w-[150px]"
            @click="emits('divideElementAmount')"
        />

        <!--&lt;!&ndash; __ Показать Детали (Ткань, ТКЧ, Кант) &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :align="MENU_ITEMS_ALIGN"-->
        <!--    :height="MENU_ITEMS_HEIGHT"-->
        <!--    :text-size="MENU_ITEMS_TEXT_SIZE"-->
        <!--    :type="showDetails ? MENU_ITEMS_ACTIVE_TYPE : MENU_ITEMS_TYPE"-->
        <!--    class="field"-->
        <!--    text="Детали"-->
        <!--    width="w-[60px]"-->
        <!--    @click="emits('showDetails')"-->
        <!--/>-->

        <!--&lt;!&ndash; __ Показать Комментарии &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :align="MENU_ITEMS_ALIGN"-->
        <!--    :height="MENU_ITEMS_HEIGHT"-->
        <!--    :text-size="MENU_ITEMS_TEXT_SIZE"-->
        <!--    :type="showComments ? MENU_ITEMS_ACTIVE_TYPE : MENU_ITEMS_TYPE"-->
        <!--    class="field"-->
        <!--    text="Примечание"-->
        <!--    width="w-[85px]"-->
        <!--    @click="emits('showComments')"-->
        <!--/>-->

        <!-- __ Обновить -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Обновить"
            width="w-[65px]"
            @click="reloadData"
        />

        <!--&lt;!&ndash; __ Упорядочить &ndash;&gt;-->
        <!--<AppLabelTS-->
        <!--    :align="MENU_ITEMS_ALIGN"-->
        <!--    :height="MENU_ITEMS_HEIGHT"-->
        <!--    :text-size="MENU_ITEMS_TEXT_SIZE"-->
        <!--    :type="MENU_ITEMS_TYPE"-->
        <!--    class="field"-->
        <!--    text="Упорядочить"-->
        <!--    width="w-[85px]"-->
        <!--/>-->

        <!-- __ Объединить дубликаты -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="canMerge ? MENU_ITEMS_TYPE : 'danger'"
            class="field"
            text="Объединить"
            width="w-[85px]"
            @click="mergeLines"
        />

        <!-- __ Добавить комментарий к СЗ -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :rounded="DEFAULT_ROUNDED"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="blockTask.comment ? 'orange' : MENU_ITEMS_TYPE"
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
import { computed, ref } from 'vue'

import type { IColorTypes, IBlockLinesPanel, IBlockTask, IBlockTaskLine } from '@/types'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import { BLOCK_MANUF_LINES } from '@/app/constants/blocks.ts'


interface IProps {
    activePanel: IBlockLinesPanel
    blockLines: IBlockTaskLine[],
    blockTask: IBlockTask,
    showComments?: boolean,
    showDetails?: boolean,
}

const props = withDefaults(defineProps<IProps>(), {
    showComments: false,
    showDetails : false,
})

const emits = defineEmits<{
    (e: 'divideElementAmount'): void            // __ Разбить количество
    (e: 'reloadData'): void                     // __ Перегрузить данные
    (e: 'moveToPanel', type: string): void      // __ Переместить в другую панель
    (e: 'mergeLines'): void                     // __ Объединить строки
    (e: 'addComment'): void                     // __ Добавить комментарий к СЗ
    (e: 'showDetails'): void                    // __ Показать Детали (Ткань, ТКЧ, Кант) !!! Резерв
    (e: 'showComments'): void                   // __ Показать Комментарии !!! Резерв
}>()

const DEFAULT_ROUNDED = '4'

// __ Константы панелей меню
const MENU_ITEMS_WIDTH     = 'w-[90px]'
const MENU_ITEMS_HEIGHT    = 'h-[35px]'
const MENU_ITEMS_TYPE      = 'primary'
const MENU_ITEMS_ALIGN     = 'center'
const MENU_ITEMS_TEXT_SIZE = 'mini'
// const MENU_ITEMS_ACTIVE_TYPE = 'success'


// __ Тип для модального окна
const modalType              = ref<IColorTypes>('danger')
const modalText              = ref<string | string[]>('')
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

const canMerge = computed(() => props.blockLines.length > 1)

// __ Объединить строки
const mergeLines = async () => {
    if (!canMerge.value) {
        return
    }
    modalText.value = ['Все строки, принадлежащие одинаковым', 'Блокам, будут объединены.', 'Продолжить?']
    modalType.value = 'danger'
    const answer    = await appModalAsyncMultiline.value!.show()             // показываем модалку и ждем ответ
    if (answer) {
        emits('mergeLines')
    }
}


</script>

<style scoped>
.field {
    @apply cursor-pointer;
}
</style>
