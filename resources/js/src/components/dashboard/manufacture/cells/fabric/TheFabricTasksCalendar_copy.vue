<template>

    <div class="ml-2 mt-2">

        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">


            <AppLabel
                :width="dateColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Дата"
                type="primary"
            />

            <AppLabel
                :width="machineColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Американец"
                type="primary"
            />

            <AppLabel
                :width="machineColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Немец"
                type="primary"
            />

            <AppLabel
                :width="machineColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Китаец"
                type="primary"
            />

            <AppLabel
                :width="machineColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Кореец"
                type="primary"
            />

            <AppLabel
                :width="actionsColWidth"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Действия"
                type="primary"
            />

        </div>


        <div v-for="index in 10">


            <div class="flex border-2 border-slate-400 bg-slate-200 rounded-lg p-1 mb-1 max-w-fit">
                <!-- Контейнер под горизонтальный блок -->

                <div :class="leftDateColWidth"
                     class="flex items-center justify-center bg-slate-300 rounded-lg border-2 border-slate-400 font-semibold">
                    <!-- Дата -->

                    <div class="text-center">

                        <div class="">
                            17.
                        </div>

                        <div class="">
                            03.
                        </div>

                        <div class="">
                            25
                        </div>

                    </div>


                </div>

                <!-- Эмуляция стегальных машин" -->
                <div v-for="x in 4">

                    <div class="ml-1 border-2 border-slate-400 bg-slate-300 rounded-lg p-1 flex flex-col justify-between h-full">

                        <div> <!-- Верхний блок с ПС -->

                            <div
                                v-for="index in (Math.floor(Math.random() * (8)))"
                                class="flex">

                                <AppLabel
                                    :width="fabricColWidth"
                                    align="left"
                                    class="cursor-pointer"
                                    text="ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)"
                                    textSize="micro"
                                    title="Ткань + всякая нужна информация"
                                />

                                <!-- Количество в рулонах -->
                                <AppLabel
                                    :text="(Math.floor(Math.random() * (8)) + 1).toString()"
                                    :width="amountRollsColWidth"
                                    align="center"
                                    textSize="micro"
                                    title="Количество в рулонах"
                                />

                                <!-- Количество в погонных метрах -->
                                <AppLabel
                                    :text="(Math.random() * (500) + 1).toFixed(1)"
                                    :width="amountMetersColWidth"
                                    align="center"
                                    textSize="micro"
                                    title="Количество в погонных метрах"
                                />

                                <!-- Трудозатраты -->
                                <AppLabel
                                    :width="laborsColWidth"
                                    align="center"
                                    text="5,1"
                                    textSize="micro"
                                    title="Трудозатраты, ч"
                                />

                            </div>

                        </div>

                        <div> <!-- Нижний блок с итого и чертой -->

                            <!-- горизонтальная черта -->
                            <div
                                class="w-[350px] h-[3px] border-b-2 border-slate-500 justify-self-center ml-0.5 mb-0.5">
                            </div>

                            <div class="flex"> <!-- Итого -->

                                <AppLabel
                                    :width="fabricColWidth"
                                    align="right"
                                    class="cursor-pointer"
                                    text="Итого:"
                                    textSize="micro"
                                    title="Ткань + всякая нужна информация"
                                    type="success"
                                />

                                <!-- Количество в рулонах -->
                                <AppLabel
                                    :text="(Math.floor(Math.random() * (8)) + 1).toString()"
                                    :width="amountRollsColWidth"
                                    align="center"
                                    textSize="micro"
                                    title="Количество в рулонах"
                                    type="success"
                                />

                                <!-- Количество в погонных метрах -->
                                <AppLabel
                                    :text="(Math.random() * (500) + 1).toFixed(1)"
                                    :width="amountMetersColWidth"
                                    align="center"
                                    textSize="micro"
                                    title="Количество в погонных метрах"
                                    type="success"
                                />

                                <!-- Трудозатраты -->
                                <AppLabel
                                    :width="laborsColWidth"
                                    align="center"
                                    text="5,1"
                                    textSize="micro"
                                    title="Трудозатраты, ч"
                                    type="success"
                                />
                            </div>

                        </div>

                    </div>

                </div>

                <!-- Сервис -->
                <div :class="actionsColWidth"
                     class="ml-1 mr-0.5 flex items-start justify-center bg-slate-300 rounded-lg border-2 border-slate-400 font-semibold">

                    <div class="text-center w-full">

                        <!-- передаем width="", чтобы ширина наследовалась -->
                        <AppLabel
                            width=""
                            align="center"
                            text="Инфо по участку:"
                            textSize="mini"
                            title="Всплывающая подсказка"
                            type="success"
                        />

                        <AppLabel
                            width=""
                            align="center"
                            text="Всего: 11,5 ч."
                            textSize="mini"
                            title="Всплывающая подсказка"
                            type="info"
                        />

                        <AppLabel
                            width=""
                            align="center"
                            text="Статус: закрыт"
                            textSize="mini"
                            title="Разный статус - разными цветами"
                            type="danger"
                        />


                        <AppLabel
                            width=""
                            align="center"
                            text="Редактировать"
                            textSize="mini"
                            title="Всплывающая подсказка"
                            type="warning"
                        />

                        <AppLabel
                            width=""
                            align="center"
                            text="Закрыть"
                            textSize="mini"
                            title="Всплывающая подсказка"
                            type="danger"
                        />

                        <AppLabel
                            width=""
                            align="center"
                            text="Детали"
                            textSize="mini"
                            title="Всплывающая подсказка"

                        />

                        <!--
                        <AppLabel
                            width=""
                            align="center"
                            text="Американец: 11,5 ч."
                            textSize="mini"
                            title="Трудозатраты, ч"
                            type="info"
                            height="h-[20px]"
                        />

                        <AppLabel
                            width=""
                            align="center"
                            text="Немец: 10,5 ч."
                            textSize="mini"
                            title="Трудозатраты, ч"
                            type="info"
                            height="h-[20px]"
                        />

                        <AppLabel
                            width=""
                            align="center"
                            text="Китаец: 10,5 ч."
                            textSize="mini"
                            title="Трудозатраты, ч"
                            type="info"
                            height="h-[20px]"
                        />
                         -->


                    </div>


                </div>


            </div>
        </div>


    </div>


</template>

<script setup>
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import {ref, watch} from 'vue'


// Получаем ширину ячейки в нужном формате
const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

const machineColWidth = ref(getMachineColWidthCSS(378))         // 378 под стегальную машину (указываем динамический стиль в <style> для загрузки)

const dateColWidth = ref(getMachineColWidthCSS(48))             // под дату
const leftDateColWidth = ref(getMachineColWidthCSS(50))         // под дату слева
const actionsColWidth = ref(getMachineColWidthCSS(150))          // под действия

const fabricColWidth = ref(getMachineColWidthCSS(250))          // название ПС
const amountRollsColWidth = ref(getMachineColWidthCSS(30))      // кол-во в рулонах
const amountMetersColWidth = ref(getMachineColWidthCSS(40))     // кол-во в погонных метрах
const laborsColWidth = ref(getMachineColWidthCSS(30))           // трудозатраты


</script>

<style scoped>

.load-css {

    @apply
    w-[330px]
    w-[20px]
    w-[30px]
    w-[48px]
    w-[51px]
    w-[50px]
    w-[450px]
    w-[500px]
    w-[600px]
    w-[378px]
    w-[80px]
}

</style>
