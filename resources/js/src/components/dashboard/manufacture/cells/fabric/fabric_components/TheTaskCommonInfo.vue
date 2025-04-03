<template>
    <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

        <!-- attract: Кем и когда создано задание -->
        <div class="mb-2">

            <div>
                <AppLabel
                    text="Сменное задание:"
                    type="success"
                />
            </div>

            <div class="flex items-start ml-3">
                <AppLabel
                    text="Статус:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    text="Выполняется"
                    text-size="mini"
                    type="warning"
                    width="w-[200px]"
                />
            </div>

            <div class="flex items-start ml-3">
                <AppLabel
                    text="Специалист ОПП:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    text="Рабчук А.А."
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <div class="flex items-start ml-3">
                <AppLabel
                    text="Дата создания:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    text="29 сентября 2025 года"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

            <div class="flex items-start ml-3">
                <AppLabel
                    text="Время создания:"
                    text-size="mini"
                    width="w-[150px]"
                />
                <AppLabel
                    text="15:24:54"
                    text-size="mini"
                    width="w-[200px]"
                />
            </div>

        </div>

        <!-- attract: Бригада -->
        <div class="mb-4">

            <div class="flex items-start">
                <AppLabel
                    text="Бригада:"
                    type="success"
                    width="w-[100px]"
                />
                <AppLabel
                    text="№ 2"
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

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
</script>

<style scoped>

</style>
