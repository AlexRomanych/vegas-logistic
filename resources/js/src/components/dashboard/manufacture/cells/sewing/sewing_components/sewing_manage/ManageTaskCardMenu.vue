<template>
    <div class="flex h-full ml-[20px]">

        <!-- __ Переместить все УШМ в другую группу -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text="activePanel === 'left' ? ' УШМ ▶' : '◀ УШМ'"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            :width="MENU_ITEMS_WIDTH"
            class="field"
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
        />


        <!-- __ Разбить количество -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="◀ Разбить кол-во ▶"
            width="w-[150px]"
            @click="emits('divideElementAmount')"
        />

        <!-- __ Упорядочить -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Упорядочить"
            width="w-[100px]"
        />

        <!-- __ Схлопнуть -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Схлопнуть"
            width="w-[100px]"
        />

        <!-- __ Показать Детали (Ткань, ТКЧ, Кант) -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Детали"
            width="w-[100px]"
        />

        <!-- __ Показать Комментарии -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Комментарии"
            width="w-[100px]"
        />

        <!-- __ Обновить -->
        <AppLabelTS
            :align="MENU_ITEMS_ALIGN"
            :height="MENU_ITEMS_HEIGHT"
            :text-size="MENU_ITEMS_TEXT_SIZE"
            :type="MENU_ITEMS_TYPE"
            class="field"
            text="Обновить"
            width="w-[100px]"
        />
    </div>
</template>

<script lang="ts" setup>
import type { ISewingLinesPanel } from '@/types'

import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface IProps {
    activePanel: ISewingLinesPanel
}

/*const props =*/
defineProps<IProps>()

const emits = defineEmits<{
    (e: 'divideElementAmount'): void
}>()


// __ Константы панелей меню
const MENU_ITEMS_WIDTH     = 'w-[60px]'
const MENU_ITEMS_HEIGHT    = 'h-[35px]'
const MENU_ITEMS_TYPE      = 'primary'
const MENU_ITEMS_ALIGN     = 'center'
const MENU_ITEMS_TEXT_SIZE = 'mini'

</script>

<style scoped>
.field {
    @apply cursor-pointer;
}
</style>
