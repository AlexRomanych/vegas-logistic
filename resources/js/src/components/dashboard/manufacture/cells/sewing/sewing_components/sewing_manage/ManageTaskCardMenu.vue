<template>
    <div class="flex h-full ml-[20px]">

        <!-- __ Переместить все элементы в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text="activePanel === 'left' ? ' Все ▶' : '◀ Все'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', 'all')"
        />

        <!-- __ Переместить все УШМ в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text="activePanel === 'left' ? ' УШМ ▶' : '◀ УШМ'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', 'universal')"
        />

        <!-- __ Переместить все АШМ в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text="activePanel === 'left' ? ' АШМ ▶' : '◀ АШМ'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', 'auto')"
        />

        <!-- __ Переместить все ГС в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text="activePanel === 'left' ? ' ГС ▶' : '◀ ГС'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', 'solid_hard')"
        />

        <!-- __ Переместить все ГП в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text="activePanel === 'left' ? ' ГП ▶' : '◀ ГП'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
            @click="emits('moveToPanel', 'solid_lite')"
        />

        <!-- __ Разбить количество -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="◀ Изменить кол-во ▶"
            width="w-[150px]"
            @click="emits('divideElementAmount')"
        />

        <!-- __ Показать Детали (Ткань, ТКЧ, Кант) -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="showDetails ? MENU_ITEMS_ACTIVE_TYPE : MENU_ITEMS_TYPE"
            class="field"
            text="Детали"
            width="w-[90px]"
            @click="emits('showDetails')"
        />

        <!-- __ Показать Комментарии -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="showComments ? MENU_ITEMS_ACTIVE_TYPE : MENU_ITEMS_TYPE"
            class="field"
            text="Комментарии"
            width="w-[100px]"
            @click="emits('showComments')"
        />

        <!-- __ Обновить -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Обновить"
            width="w-[90px]"
            @click="reloadData"
        />

        <!-- __ Упорядочить -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Упорядочить"
            width="w-[90px]"
        />

        <!-- __ Объединить дубликаты -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="canMerge ? MENU_ITEMS_TYPE : 'danger'"
            class="field"
            text="Объединить"
            width="w-[90px]"
            @click="mergeLines"
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

import type { IColorTypes, ISewingLinesPanel, ISewingTaskLine } from '@/types'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'


interface IProps {
    activePanel: ISewingLinesPanel
    sewingLines: ISewingTaskLine[],
    showComments?: boolean,
    showDetails?: boolean,
}

const props = withDefaults(defineProps<IProps>(), {
    showComments: false,
    showDetails:  false,
})

const emits = defineEmits<{
    (e: 'divideElementAmount'): void            // __ Разбить количество
    (e: 'showDetails'): void                    // __ Показать Детали (Ткань, ТКЧ, Кант)
    (e: 'showComments'): void                   // __ Показать Комментарии
    (e: 'reloadData'): void                     // __ Перегрузить данные
    (e: 'moveToPanel', type: string): void      // __ Переместить в другую панель
    (e: 'mergeLines'): void                     // __ Объединить строки
}>()


// __ Константы панелей меню
const MENU_ITEMS_WIDTH       = 'w-[60px]'
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

const canMerge = computed(() => props.sewingLines.length > 1)

// __ Объединить строки
const mergeLines = async () => {
    if (!canMerge.value) {
        return
    }
    modalText.value = ['Все строки, принадлежащие одинаковым', 'элементам, будут объединены.', 'Продолжить?']
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
