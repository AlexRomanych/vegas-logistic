<template>

    <div class="mt-2 ml-2">


        <!-- attract: Выводим даты + статусы + сервисные кнопки   -->
        <div class="flex h-[165px]">
            <div v-for="task in taskData" :key="task.date">

                <!-- attract: Рамка выбора даты -->
                <div
                    :class="task.active ? 'bg-blue-200 border-2 border-blue-400 rounded-lg' : ''"
                    class="flex flex-col p-0.5  h-full">

                    <!-- attract: Дата + день недели -->
                    <AppLabelMultiLine
                        :text="[formatDate(task.date), getDayOfWeek(task.date, false)]"
                        :type="dayOfWeekStyle(task.date)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                        @click="changeActiveTask(task)"
                    />

                    <!-- attract: Статусы -->
                    <AppLabel
                        :text="getTitleByFabricTaskStatusCode(task.common.status)"
                        :type="getStyleTypeByFabricTaskStatusCode(task.common.status)"
                        align="center"
                        class="cursor-pointer"
                        width="w-[150px]"
                    />

                    <!-- attract: Первый ряд сервисных кнопок -->
                    <AppLabel
                        v-if="serviceBtnShowCondition(task.common.status)"
                        :text="serviceBtnTitle(task.common.status)"
                        align="center"
                        class="cursor-pointer"
                        textSize="small"
                        type="info"
                        width="w-[150px]"
                        @click="changeTaskStatus(task)"
                    />

                    <!-- attract: Второй ряд сервисных кнопок - кнопка "Удалить" (Только для статуса "Создано") -->
                    <AppLabel
                        v-if="task.common.status === FABRIC_TASK_STATUS.CREATED.CODE"
                        align="center"
                        class="cursor-pointer"
                        text="Удалить"
                        textSize="small"
                        type="danger"
                        width="w-[150px]"
                        @click="changeTaskStatus(task, 2)"
                    />

                </div>

            </div>
        </div>


        <!-- attract Выводим табы -->
        <div class="flex flex-row justify-start items-center m-3">
            <div v-for="tab in tabs" :key="tab.id">

                <AppLabelMultiLine
                    :bold="true"
                    :text="tab.name"
                    :type="tab.shown ? 'primary' : 'dark'"
                    align="center"
                    class="cursor-pointer"
                    width="w-[150px]"
                    @click="changeTab(tab)"
                />

            </div>
        </div>


        <div class="ml-3 mt-3">

            <!--attract: Общее-->
            <div v-if="tabs.common.shown">
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
            </div>

            <!--attract: Американец-->
            <div v-if="tabs.american.shown">
                <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 w-fit">

                    <!--attract: Меню стегальной машины -->
                    <div class="flex">

                        <AppLabelMultiLine
                            :text="['Добавить', 'рулон']"
                            align="center"
                            class="cursor-pointer"
                            height="h-[31px]"
                            type="success"
                            width="w-[170px]"
                        />

                        <AppLabelMultiLine
                            :text="['Оптимизировать', 'трудозатраты']"
                            align="center"
                            class="cursor-pointer"
                            height="h-[31px]"
                            type="warning"
                            width="w-[170px]"
                        />

                        <AppLabelMultiLine
                            :text="['Текущий рисунок:', 'Ж25']"
                            align="center"
                            height="h-[31px]"
                            type="info"
                            width="w-[170px]"
                        />

                        <AppLabelMultiLine
                            :text="['Общие трудозатраты', '08ч. 59м. 59с.']"
                            align="center"
                            height="h-[31px]"
                            type="danger"
                            width="w-[200px]"
                        />

                        <AppCheckbox
                            id="active"
                            :checkboxData="checkboxData"
                            dir="horizontal"
                            inputType="radio"
                            legend="Выбор ПС:"
                            type="secondary"
                            width="w-[350px]"
                            @checked="checkedHandler"
                        />

                    </div>

                    <!--attract: Разделительная линия -->
                    <div class="mt-2 mb-2 bg-slate-400 min-h-[4px] rounded-lg"></div>

                    <!--attract: Добавляем информацию о рулонах -->
                    <div class="flex">

                        <AppLabelMultiLine
                            :text="['Номер', 'рулона']"
                            align="center"
                            width="w-[80px]"
                        />

                        <AppLabelMultiLine
                            :text="['Полотно', 'стеганное']"
                            align="center"
                            type="primary"
                            width="w-[300px]"
                        />

                        <AppLabelMultiLine
                            :text="['Ср. длина', 'ткани (м.п.)']"
                            align="center"
                            width="w-[100px]"
                        />

                        <AppLabelMultiLine
                            :text="['Кол-во,', '(рул.)']"
                            align="center"
                            type="primary"
                            width="w-[70px]"
                        />

                        <AppLabelMultiLine
                            :text="['Кол-во,', '(м.п.)']"
                            align="center"
                            type="primary"
                            width="w-[70px]"
                        />

                        <AppLabelMultiLine
                            :text="['Труд-ты', '(мин.)']"
                            align="center"
                            width="w-[80px]"
                        />

                        <AppLabelMultiLine
                            :text="['Комментарий', '']"
                            align="center"
                            type="primary"
                            width="w-[300px]"
                        />

                    </div>


                    <!--attract: Добавляем информацию о рулонах -->

                    <!-- attract: Режим отображения -->
                    <div class="flex">
                        <AppLabel
                            align="center"
                            text="00023548"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <AppLabel
                            align="center"
                            text="ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)"
                            text-size="mini"
                            type="primary"
                            width="w-[300px]"
                        />

                        <AppLabel
                            align="center"
                            text="100.8"
                            text-size="mini"
                            width="w-[100px]"
                        />

                        <AppLabel
                            align="center"
                            text="2"
                            text-size="mini"
                            type="primary"
                            width="w-[70px]"
                        />

                        <AppLabel
                            align="center"
                            text="208.16"
                            text-size="mini"
                            type="primary"
                            width="w-[70px]"
                        />

                        <AppLabel
                            align="center"
                            text="208"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <AppLabel
                            class="truncate"
                            text="Рулон был создан на американской СМ, но не прокручен до конца"
                            text-size="mini"
                            type="primary"
                            width="w-[300px]"
                        />

                        <AppLabel
                            align="center"
                            class="cursor-pointer font-bold"
                            text="Ред."
                            text-size="mini"
                            type="warning"
                            width="w-[50px]"
                        />

                        <AppLabel
                            align="center"
                            class="cursor-pointer font-bold"
                            text="Х"
                            text-size="mini"
                            type="danger"
                            width="w-[50px]"
                        />

                    </div>

                    <!-- attract: Заглушка "Пусто" -->
                    <AppLabel
                        align="center"
                        text="Пусто"
                        type="info"
                    />

                    <!-- attract: Режим редактирования -->
                    <div class="flex">
                        <AppLabelMultiLine
                            align="center"
                            :text="['00023548', '']"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <!--                        <AppLabel-->
                        <!--                            align="center"-->
                        <!--                            text="ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)"-->
                        <!--                            text-size="mini"-->
                        <!--                            type="primary"-->
                        <!--                            width="w-[300px]"-->
                        <!--                        />-->

                        <AppSelect
                            :multiple="false"
                            :selectData="selectData"
                            label="Выберите ПС:"
                            type="primary"
                            text-size="mini"
                            width="w-[304px]"
                            @change="selectHandler"
                        />

                        <AppLabelMultiLine
                            align="center"
                            :text="['100.8', '']"
                            text-size="mini"
                            width="w-[100px]"
                        />

                        <AppLabelMultiLine
                            align="center"
                            :text="['2', '']"
                            text-size="mini"
                            type="primary"
                            width="w-[70px]"
                        />

                        <AppLabelMultiLine
                            align="center"
                            :text="['208.16', '']"
                            text-size="mini"
                            type="primary"
                            width="w-[70px]"
                        />

                        <AppLabelMultiLine
                            align="center"
                            :text="['208', '']"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <AppInputTextArea
                            class="cursor-pointer"
                            id="comment"
                            value="Рулон был создан на американской СМ, но не прокручен до конца"
                            type="primary"
                            rows="2"
                            width="w-[304px]"
                            height="min-h-[50px]"
                            text-size="mini"

                        />

                        <AppLabelMultiLine
                            align="center"
                            class="cursor-pointer font-bold"
                            :text="['V', '']"
                            text-size="mini"
                            type="success"
                            width="w-[50px]"
                        />

                    </div>


                </div>
            </div>

            <!--attract: Немец-->
            <div v-if="tabs.german.shown">
                <div>Немец</div>
            </div>

            <!--attract: Китаец-->
            <div v-if="tabs.china.shown">
                <div>Китаец</div>
            </div>

            <!--attract: Китаец-->
            <div v-if="tabs.korean.shown">
                <div>Кореец</div>
            </div>

        </div>

    </div>


    <!--    <AppModal-->
    <!--        :show="modalShow"-->
    <!--        type="danger"-->
    <!--        mode="confirm"-->
    <!--        @close-modal="getModalAnswer"-->
    <!--    />-->

    <AppModalAsync
        ref="appModalAsync"
        :text="modalText"
        mode="confirm"
        type="danger"
    />

</template>

<script setup>
import {ref, reactive} from 'vue'
import {useRoute} from 'vue-router'

import {
    FABRIC_TASK_STATUS,
    FABRIC_MACHINES,
} from '/resources/js/src/app/constants/fabrics.js'

import {
    getTitleByFabricTaskStatusCode,
    getStyleTypeByFabricTaskStatusCode
} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'

import {
    getDayOfWeek,
    formatDate,
    isToday,
    isWorkingDay,
} from '/resources/js/src/app/helpers/helpers_date.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsync from '/resources/js/src/components/ui/modals/AppModalAsync.vue'
import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'
import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'
import AppModal from '/resources/js/src/components/ui/modals/AppModal.vue'
// import AppModalAsync from '/resources/js/src/components/ui/modals/AppModalAsync.vue'
// import AppButton from '/resources/js/src/components/ui/buttons/AppButton.vue'

const route = useRoute()


// attract: Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const tabs = reactive({
    common: {id: 1, shown: false, name: ['Общие', 'данные']},
    american: {id: 2, shown: false, name: ['Американец', 'LEGACY-4']},
    german: {id: 3, shown: false, name: ['Немец', 'CHAINTRONIC']},
    china: {id: 4, shown: false, name: ['Китаец', 'HY-W-DGW']},
    korean: {id: 5, shown: false, name: ['Кореец', 'МТ-94']},
    // oneNeedle: {id: 6, shown: false, name: ['Одноиголка', '']},
})

// переключаем выбранную вкладку
const changeTab = (selectedTab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].id === selectedTab.id
        }
    }
    console.log(tabs)
}

// сбрасываем все вкладки в false, потому что в некоторых ситуациях появляется мультивыбор
const resetTabs = () => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = false
        }
    }
    // console.log(tabs)
}

resetTabs()                                 // сбрасываем все табы
tabs.common.shown = true                    // делаем вкладку "общие данные" активной, чтобы запустить реактивность

// attract: Определяем объект с данными чекбокса (доступность тканей - основные или все доступные)
const checkboxData = {
    name: 'Availability',
    data: [
        {id: 1, name: 'Основные ПС', checked: true},
        {id: 2, name: 'Все доступные ПС', checked: false},
    ]
}

// Обрабатываем клик по чек боксу
const checkedHandler = (item) => console.log(item)

// attract: Определяем объект с данными селекта для ПС
const selectData = {
    name: 'numbers',
    data: [
        {id: 1, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
        {id: 2, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
        {id: 3, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)', selected: true},
        {id: 4, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)', disabled: true},
        {id: 5, name: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
    ]
}

const selectHandler = (item) => console.log(item)



// формируем тестовые данные
const taskData = reactive([
    {
        date: '2025-03-24',
        active: true,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.DONE.CODE,
        },
        machines: {
            american: {
                // id: FABRIC_MACHINES.AMERICAN,
                rolls: [
                    {num: 27001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            german: {
                // id: FABRIC_MACHINES.GERMAN,
                rolls: [
                    {num: 28004, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28005, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28006, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            china: {
                // id: FABRIC_MACHINES.CHINA,
                rolls: [
                    {num: 29007, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29008, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29009, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            korean: {
                // id: FABRIC_MACHINES.KOREAN,
                rolls: [
                    {num: 31001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 32002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 33003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
        }
    },
    {
        date: '2025-03-25',
        active: false,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.RUNNING.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {num: 27001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            german: {
                rolls: [
                    {num: 28004, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28005, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28006, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            china: {
                rolls: [
                    {num: 29007, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29008, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29009, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            korean: {
                rolls: [
                    {num: 31001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 32002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 33003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
        }
    },
    {
        date: '2025-03-26',
        active: false,
        common: {
            team: 2,
            status: FABRIC_TASK_STATUS.PENDING.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {num: 27001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            german: {
                rolls: [
                    {num: 28004, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28005, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28006, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            china: {
                rolls: [
                    {num: 29007, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29008, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29009, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            korean: {
                rolls: [
                    {num: 31001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 32002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 33003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
        }
    },
    {
        date: '2025-03-27',
        active: false,
        common: {
            team: 2,
            status: FABRIC_TASK_STATUS.CREATED.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {num: 27001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            german: {
                rolls: [
                    {num: 28004, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28005, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28006, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            china: {
                rolls: [
                    {num: 29007, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29008, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29009, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            korean: {
                rolls: [
                    {num: 31001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 32002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 33003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
        }
    },
    {
        date: '2025-03-28',
        active: false,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {num: 27001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            german: {
                rolls: [
                    {num: 28004, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28005, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28006, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            china: {
                rolls: [
                    {num: 29007, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29008, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29009, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            korean: {
                rolls: [
                    {num: 31001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 32002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 33003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
        }
    },
    {
        date: '2025-03-29',
        active: false,
        common: {
            team: 1,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {num: 27001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            german: {
                rolls: [
                    {num: 28004, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28005, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28006, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            china: {
                rolls: [
                    {num: 29007, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29008, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29009, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            korean: {
                rolls: [
                    {num: 31001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 32002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 33003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
        }
    },
    {
        date: '2025-03-30',
        active: false,
        common: {
            team: 2,
            status: FABRIC_TASK_STATUS.UNKNOWN.CODE,
        },
        machines: {
            american: {
                rolls: [
                    {num: 27001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 27003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            german: {
                rolls: [
                    {num: 28004, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28005, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 28006, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            china: {
                rolls: [
                    {num: 29007, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29008, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 29009, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
            korean: {
                rolls: [
                    {num: 31001, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 32002, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                    {num: 33003, fabric: 'ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)'},
                ]
            },
        }
    },
])

// attract Получаем тип метки в зависимости от типа дня недели (выходной или рабочий)
const dayOfWeekStyle = (date) => {
    if (isToday(date)) return 'success'
    if (isWorkingDay(date)) return 'dark'
    return 'danger'
}

// attract Условие на отображение сервисных кнопок под статусами
const serviceBtnShowCondition = (status) => {
    return !(status === FABRIC_TASK_STATUS.DONE.CODE || status === FABRIC_TASK_STATUS.RUNNING.CODE)
}

// attract Получаем название сервисной кнопки в зависимости от статуса
const serviceBtnTitle = (status) => {
    const titles = ['Создать', 'На стежку', 'Вернуть статус', '', '']
    return titles[status]
}

const appModalAsync = ref(null)         // Получаем ссылку на модальное окно
const modalText = ref('')


// attract Меняем статус СЗ по сервисной кнопке
const changeTaskStatus = async (task, btnRow = 1) => {

    if (!task.active) return

    // Удалить сменное задание
    if (btnRow === 2 && task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {
        modalText.value = 'Вы уверены?'
        const result = await appModalAsync.value.show()
        if (result) {
            task.common.status = FABRIC_TASK_STATUS.UNKNOWN.CODE
        }
        return
    }

    if (task.common.status === FABRIC_TASK_STATUS.UNKNOWN.CODE) {
        task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
        return
    }

    if (task.common.status === FABRIC_TASK_STATUS.PENDING.CODE) {
        task.common.status = FABRIC_TASK_STATUS.CREATED.CODE
        return
    }

    if (task.common.status === FABRIC_TASK_STATUS.CREATED.CODE) {
        task.common.status = FABRIC_TASK_STATUS.PENDING.CODE
    }

}

// attract Меняем активный день по клику на нем
const changeActiveTask = (task) => taskData.forEach((t) => t.active = t.date === task.date)


</script>

<style scoped>

</style>





