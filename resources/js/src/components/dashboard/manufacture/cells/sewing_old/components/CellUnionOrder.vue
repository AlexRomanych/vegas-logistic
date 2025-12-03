<template>
    <div class="ml-8">

        <div class="flex justify-start items-center">

            <AppLabel
                :bold="true"
                :text="order.short_name"
                textSize="small"
            />

            <AppLabel
                :bold="true"
                :text="'№ ' + getPrettyOrderNumber(order.no_num)"
                align="center"
                textSize="small"
                width="w-[70px]"
            />

            <AppLabel
                :bold="true"
                :text="order.amount.toString()"
                align="center"
                width="w-[40px]"
            />

            <AppLabel
                :bold="true"
                :text="order.norm.toFixed(2)"
                align="center"
                type="primary"
                width="w-[60px]"
            />

        </div>

    </div>

</template>

<script setup>
import {ref} from "vue"
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue"

import {getPrettyOrderNumber} from '/resources/js/src/app/helpers/helpers_order.js'

const props = defineProps({
    order: {
        type: Object,
        required: true
    }

})

</script>

<style scoped>

</style>
