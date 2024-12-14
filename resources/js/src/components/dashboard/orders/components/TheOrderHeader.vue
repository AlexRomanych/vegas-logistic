<template>
    <div>

        <div class="flex items-center">
            <AppInputButton
                :id="order.id"
                title="+"
                type="dark"
                width="w-[30px]"
                height="h-[30px]"
            />

            <AppLabel
                :text="order.client.short_name"
                :bold="true"
            />

            <AppLabel
                :text="'№' + order.no_num"
                :bold="true"
                width="w-[100px]"
            />

            <div>
                {{ '№' + order.no_num }}
            </div>
            <div>
                {{ formattedDate(order.load_date) }}
            </div>
        </div>


        <table>
            <TheOrderLine
                v-for="line in order.lines"
                :key="line.id"
                :line="line"
            />
        </table>


    </div>
</template>

<script setup>
import TheOrderLine from '@/src/components/dashboard/orders/components/TheOrderLine.vue'
import AppInputButton from '@/src/components/ui/inputs/AppInputButton.vue'
import AppLabel from "@/src/components/ui/labels/AppLabel.vue";

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
})

// Форматируем дату в определенный формат
const formattedDate = (date) => {
    return new Date(date).toLocaleDateString()
}



// console.log(props.order)
</script>

<style scoped>

</style>
