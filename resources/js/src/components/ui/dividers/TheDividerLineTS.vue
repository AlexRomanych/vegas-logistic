<template>
    <!-- __ Разделительная линия -->
    <div
        :class="[mTop, mBottom]"
        class="mx-auto bg-slate-400 min-h-[2px] rounded-lg "
        style="width: calc(100% - 8px)"
    >
    </div>
</template>

<script setup lang="ts">

interface IProps {
    mTop?: string
    mBottom?: string
}

withDefaults(defineProps<IProps>(), {
    mTop:    'mt-2',
    mBottom: 'mb-2'
})

</script>

<style scoped>

</style>
