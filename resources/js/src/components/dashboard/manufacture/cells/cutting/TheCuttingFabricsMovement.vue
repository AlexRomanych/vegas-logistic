<template>
    <div class="ml-2 mt-2">
        <!-- __ Выводим табы -->
        <div class="flex flex-row justify-start items-center mb-2">
            <div
                v-for="tab in tabs" :key="tab.id" class="m-0.5"
            >
                <div
                    v-if="tab.enabled"
                    :class="tab.shown ? 'p-1 bg-blue-200 border-2 rounded-lg border-blue-700' : ''"
                >
                    <AppLabelMultiLine
                        :bold="true"
                        :text="tab.name"
                        :type="tab.type"
                        align="center"
                        class="cursor-pointer"
                        width="w-[200px]"
                        @click="changeTab(tab)"
                    />
                </div>
            </div>
        </div>

        <!-- __ Шапка таблицы -->
        <div class="sticky top-0 flex p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">
            <div>
                <!-- __ № рулона  -->
                <AppLabelMultiLine
                    v-if="render.rollNumber.show"
                    :text="render.rollNumber.header"
                    :title="
                        render.rollNumber.title
                        ? typeof render.rollNumber.title === 'function'
                            ? render.rollNumber.title()
                            : render.rollNumber.title
                        : ''
                    "
                    :type="
                        typeof render.rollNumber.type === 'function'
                        ? render.rollNumber.type(true)
                        : render.rollNumber.type
                    "
                    :width="render.rollNumber.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: № рулона -->
                <AppInputText
                    v-if="render.rollNumber.show"
                    id="roll-number-search"
                    v-model.trim="rollNumberFilter"
                    :width="render.rollNumber.width"
                    placeholder="№ рул."
                    text-size="mini"
                    type="primary"
                    @input="handleRollNumberInput"
                />
            </div>

            <div>
                <!-- __ ПС  -->
                <AppLabelMultiLine
                    v-if="render.fabric.show"
                    :text="render.fabric.header"
                    :title="
                        render.fabric.title
                        ? typeof render.fabric.title === 'function'
                            ? render.fabric.title()
                            : render.fabric.title
                        : ''
                    "
                    :type="
                        typeof render.fabric.type === 'function'
                        ? render.fabric.type(true)
                        : render.fabric.type
                    "
                    :width="render.fabric.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: ПС -->
                <AppInputText
                    v-if="render.fabric.show"
                    id="fabric-search"
                    v-model.trim="fabricFilter"
                    :width="render.fabric.width"
                    placeholder="ПС"
                    text-size="mini"
                    type="primary"
                />
            </div>

            <div>
                <div class="flex">

                    <!-- __ Ткань, м.п. -->
                    <AppLabelMultiLine
                        v-if="render.textileLength.show"
                        :text="render.textileLength.header"
                        :title="
                            render.textileLength.title
                            ? typeof render.textileLength.title === 'function'
                                ? render.textileLength.title()
                                : render.textileLength.title
                            : ''
                        "
                        :type="
                            typeof render.textileLength.type === 'function'
                            ? render.textileLength.type(true)
                            : render.textileLength.type
                        "
                        :width="render.textileLength.width"
                        align="center"
                        class="header-item"
                        text-size="mini"
                    />

                    <!-- __ ПС, м.п. -->
                    <AppLabelMultiLine
                        v-if="render.fabricLength.show"
                        :text="render.fabricLength.header"
                        :title="
                            render.fabricLength.title
                            ? typeof render.fabricLength.title === 'function'
                                ? render.fabricLength.title()
                                : render.fabricLength.title
                            : ''
                        "
                        :type="
                            typeof render.fabricLength.type === 'function'
                            ? render.fabricLength.type(true)
                            : render.fabricLength.type
                        "
                        :width="render.fabricLength.width"
                        align="center"
                        class="header-item"
                        text-size="mini"
                    />
                </div>

                <div v-if="tabs.common.shown">
                    <!-- __ Фильтр: Статус рулонов. Показываем только на общей вкладке -->
                    <AppSelectSimple
                        :select-data="rollStatuses"
                        :type="statusFilterType"
                        text-size="mini"
                        width="w-[124px]"
                        @change="filterByStatus"
                    />
                </div>
            </div>

            <div>
                <!-- __ Дата пр-ва -->
                <AppLabelMultiLine
                    v-if="render.finishAt.show"
                    :text="render.finishAt.header"
                    :title="
                        render.finishAt.title
                        ? typeof render.finishAt.title === 'function'
                            ? render.finishAt.title()
                            : render.finishAt.title
                        : ''
                    "
                    :type="
                        typeof render.finishAt.type === 'function'
                        ? render.finishAt.type(true)
                        : render.finishAt.type
                    "
                    :width="render.finishAt.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: Дата пр-ва -->
                <AppInputText
                    v-if="render.finishAt.show"
                    id="finish-at-search"
                    v-model.trim="finishAtFilter"
                    :width="render.finishAt.width"
                    placeholder="дд.мм.гггг"
                    text-size="mini"
                    type="primary"
                    @input="handleFinishAtInput"
                />
            </div>

            <!-- __ Ответственный за производство -->
            <AppLabelMultiLine
                v-if="render.finishBy.show"
                :text="render.finishBy.header"
                :title="
                    render.finishBy.title
                    ? typeof render.finishBy.title === 'function'
                        ? render.finishBy.title()
                        : render.finishBy.title
                    : ''
                "
                :type="
                    typeof render.finishBy.type === 'function'
                    ? render.finishBy.type(true)
                    : render.finishBy.type
                "
                :width="render.finishBy.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!--<div v-if="!tabs.moved.shown" class="flex">-->
            <!-- __ Флаг учета в 1С -->
            <AppLabelMultiLine
                v-if="render.registration_1C_Flag.show"
                :text="render.registration_1C_Flag.header"
                :title="
                    render.registration_1C_Flag.title
                    ? typeof render.registration_1C_Flag.title === 'function'
                        ? render.registration_1C_Flag.title()
                        : render.registration_1C_Flag.title
                    : ''
                "
                :type="
                    typeof render.registration_1C_Flag.type === 'function'
                    ? render.registration_1C_Flag.type(true)
                    : render.registration_1C_Flag.type
                "
                :width="render.registration_1C_Flag.width"
                align="center"
                class="header-item"
                text-size="mini"
            />
            <!--</div>-->

            <div>
                <!-- __ Дата учета в 1С -->
                <AppLabelMultiLine
                    v-if="render.registration_1C_At.show"
                    :text="render.registration_1C_At.header"
                    :title="
                        render.registration_1C_At.title
                        ? typeof render.registration_1C_At.title === 'function'
                            ? render.registration_1C_At.title()
                            : render.registration_1C_At.title
                        : ''
                    "
                    :type="
                        typeof render.registration_1C_At.type === 'function'
                        ? render.registration_1C_At.type(true)
                        : render.registration_1C_At.type
                    "
                    :width="render.registration_1C_At.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: Дата учета в 1С -->
                <!--<div v-if="tabs.registered_1C.shown || tabs.moved.shown">-->
                <AppInputText
                    v-if="render.registration_1C_At.show"
                    id="register-at-search"
                    v-model.trim="register1CAtFilter"
                    :width="render.registration_1C_At.width"
                    placeholder="дд.мм.гггг"
                    text-size="mini"
                    type="primary"
                    @input="handleRegistered1CAtInput"
                />
                <!--</div>-->
            </div>

            <!-- __ Ответственный за учет в 1С -->
            <AppLabelMultiLine
                v-if="render.registration_1C_By.show"
                :text="render.registration_1C_By.header"
                :title="
                    render.registration_1C_By.title
                    ? typeof render.registration_1C_By.title === 'function'
                        ? render.registration_1C_By.title()
                        : render.registration_1C_By.title
                    : ''
                "
                :type="
                    typeof render.registration_1C_By.type === 'function'
                    ? render.registration_1C_By.type(true)
                    : render.registration_1C_By.type
                "
                :width="render.registration_1C_By.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- __ Сейчас показываем везде, раньше - Показываем только на вкладке Перемещенные на закрой -->
            <!--<div v-if="tabs.moved.shown" class="flex">-->

            <!-- __ Флаг перемещения на закрой -->
            <AppLabelMultiLine
                v-if="render.moveToCutFlag.show"
                :text="render.moveToCutFlag.header"
                :title="
                    render.moveToCutFlag.title
                    ? typeof render.moveToCutFlag.title === 'function'
                        ? render.moveToCutFlag.title()
                        : render.moveToCutFlag.title
                    : ''
                "
                :type="
                    typeof render.moveToCutFlag.type === 'function'
                    ? render.moveToCutFlag.type(true)
                    : render.moveToCutFlag.type
                "
                :width="render.moveToCutFlag.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <div>
                <!-- __ Дата перемещения на закрой -->
                <AppLabelMultiLine
                    v-if="render.moveToCutAt.show"
                    :text="render.moveToCutAt.header"
                    :title="
                        render.moveToCutAt.title
                        ? typeof render.moveToCutAt.title === 'function'
                            ? render.moveToCutAt.title()
                            : render.moveToCutAt.title
                        : ''
                    "
                    :type="
                        typeof render.moveToCutAt.type === 'function'
                        ? render.moveToCutAt.type(true)
                        : render.moveToCutAt.type
                    "
                    :width="render.moveToCutAt.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: Дата перемещения на закрой -->
                <AppInputText
                    v-if="render.moveToCutAt.show"
                    id="moved-at-search"
                    v-model.trim="movedAtFilter"
                    :width="render.moveToCutAt.width"
                    placeholder="дд.мм.гггг"
                    text-size="mini"
                    type="primary"
                    @input="handleMovedAtInput"
                />
            </div>

            <!-- __ Ответственный за перемещение на закрой -->
            <AppLabelMultiLine
                v-if="render.moveToCutBy.show"
                :text="render.moveToCutBy.header"
                :title="
                    render.moveToCutBy.title
                    ? typeof render.moveToCutBy.title === 'function'
                        ? render.moveToCutBy.title()
                        : render.moveToCutBy.title
                    : ''
                "
                :type="
                    typeof render.moveToCutBy.type === 'function'
                    ? render.moveToCutBy.type(true)
                    : render.moveToCutBy.type
                "
                :width="render.moveToCutBy.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!--</div>-->

            <div>
                <!-- __ Примечание -->
                <AppLabelMultiLine
                    v-if="render.description.show"
                    :text="render.description.header"
                    :title="
                        render.description.title
                        ? typeof render.description.title === 'function'
                            ? render.description.title()
                            : render.description.title
                        : ''
                    "
                    :type="
                        typeof render.description.type === 'function'
                        ? render.description.type(true)
                        : render.description.type
                    "
                    :width="render.description.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: Примечание -->
                <AppInputText
                    v-if="render.description.show"
                    id="description-search"
                    v-model.trim="descriptionFilter"
                    :width="render.description.width"
                    placeholder="Примечание"
                    text-size="mini"
                    type="primary"
                />
            </div>


            <div>
                <!-- __ Комментарий (пока для данных на стежке) -->
                <AppLabelMultiLine
                    v-if="render.comment.show"
                    :text="render.comment.header"
                    :title="
                        render.comment.title
                        ? typeof render.comment.title === 'function'
                            ? render.comment.title()
                            : render.comment.title
                        : ''
                    "
                    :type="
                        typeof render.comment.type === 'function'
                        ? render.comment.type(true)
                        : render.comment.type
                    "
                    :width="render.comment.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: Примечание -->
                <AppInputText
                    v-if="render.comment.show"
                    id="comment-search"
                    v-model.trim="commentFilter"
                    :width="render.comment.width"
                    placeholder="Комментарий"
                    text-size="mini"
                    type="primary"
                />
            </div>


        </div>

        <!-- __ Сами данные -->
        <div
            v-if="doneRolls.length"
            :key="updateTemplate"
            class="p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-600 max-w-fit"
        >
            <div
                v-for="fabricRolls in doneRolls"
                :key="fabricRolls.fabric.id"
                class="mt-2"
            >
                <div class="flex">
                    <!-- __ +/- -->
                    <AppLabel
                        :text="fabricRolls.fabric.collapsed ? '▲' : '▼'"
                        :width="render.rollNumber.width"
                        align="center"
                        class="cursor-pointer"
                        text-size="small"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />

                    <!-- __ ПС - Название группы -->
                    <AppLabel
                        :text="fabricRolls.fabric.displayName"
                        :title="fabricRolls.fabric.displayName"
                        :width="render.fabric.width"
                        align="left"
                        class="truncate cursor-pointer"
                        text-size="mini"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />

                    <!-- __ Ткань группы (сумма), м.п. -->
                    <AppLabel
                        :text="fabricRolls.fabric.textileLength.toFixed(2)"
                        :width="render.textileLength.width"
                        align="center"
                        class="cursor-pointer"
                        text-size="mini"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />

                    <!-- __ ПС группы (сумма), м.п. -->
                    <AppLabel
                        :text="fabricRolls.fabric.fabricLength.toFixed(2)"
                        :width="render.fabricLength.width"
                        align="center"
                        class="cursor-pointer"
                        text-size="mini"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />
                </div>

                <!-- __ Показываем группу -->
                <div>
                    <div v-if="!fabricRolls.fabric.collapsed">
                        <div
                            v-for="roll in fabricRolls.rolls"
                            :key="roll.id"
                            class="flex"
                        >
                            <!-- __ № рулона  -->
                            <AppLabel
                                v-if="render.rollNumber.show"
                                :text="render.rollNumber.data ? render.rollNumber.data(roll) : ''"
                                :type="
                                    typeof render.rollNumber.type === 'function'
                                    ? render.rollNumber.type(false, roll)
                                    : render.rollNumber.type
                                "
                                :width="render.rollNumber.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- __ ПС  -->
                            <AppLabel
                                v-if="render.fabric.show"
                                :text="render.fabric.data ? render.fabric.data(roll) : ''"
                                :type="
                                    typeof render.fabric.type === 'function'
                                    ? render.fabric.type(false, roll)
                                    : render.fabric.type
                                "
                                :width="render.fabric.width"
                                text-size="micro"
                            />

                            <!-- __ Ткань, м.п. -->
                            <AppLabel
                                v-if="render.textileLength.show"
                                :text="render.textileLength.data ? render.textileLength.data(roll) : ''"
                                :type="
                                    typeof render.textileLength.type === 'function'
                                    ? render.textileLength.type(false, roll)
                                    : render.textileLength.type
                                "
                                :width="render.textileLength.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- __ ПС, м.п. -->
                            <AppLabel
                                v-if="render.fabricLength.show"
                                :text="render.fabricLength.data ? render.fabricLength.data(roll) : ''"
                                :type="
                                    typeof render.fabricLength.type === 'function'
                                    ? render.fabricLength.type(false, roll)
                                    : render.fabricLength.type
                                "
                                :width="render.fabricLength.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- __ Дата пр-ва -->
                            <AppLabel
                                v-if="render.finishAt.show"
                                :text="render.finishAt.data ? render.finishAt.data(roll) : ''"
                                :type="
                                    typeof render.finishAt.type === 'function'
                                    ? render.finishAt.type(false, roll)
                                    : render.finishAt.type
                                "
                                :width="render.finishAt.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- __ Ответственный за производство -->
                            <AppLabel
                                v-if="render.finishBy.show"
                                :text="render.finishBy.data ? render.finishBy.data(roll) : ''"
                                :type="
                                    typeof render.finishBy.type === 'function'
                                    ? render.finishBy.type(false, roll)
                                    : render.finishBy.type
                                "
                                :width="render.finishBy.width"
                                align="center"
                                text-size="micro"
                            />

                            <!--<div v-if="!tabs.moved.shown" class="flex">-->
                            <!-- __ Флаг учета в 1С -->
                            <AppLabel
                                v-if="render.registration_1C_Flag.show"
                                :text="render.registration_1C_Flag.data ? render.registration_1C_Flag.data(roll) : ''"
                                :text-size="typeof render.registration_1C_Flag.textSize === 'function' ? render.registration_1C_Flag.textSize(roll) : 'micro'"
                                :type="
                                    typeof render.registration_1C_Flag.type === 'function'
                                    ? render.registration_1C_Flag.type(false, roll)
                                    : render.registration_1C_Flag.type
                                "
                                :width="render.registration_1C_Flag.width"
                                align="center"
                                class="cursor-pointer"
                                @click="changeRegistrationStatus(roll)"
                            />
                            <!--</div>-->

                            <!-- __ Дата учета в 1С -->
                            <AppLabel
                                v-if="render.registration_1C_At.show"
                                :text="render.registration_1C_At.data ? render.registration_1C_At.data(roll) : ''"
                                :type="
                                    typeof render.registration_1C_At.type === 'function'
                                    ? render.registration_1C_At.type(false, roll)
                                    : render.registration_1C_At.type
                                "
                                :width="render.registration_1C_At.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- __ Ответственный за учет в 1С -->
                            <AppLabel
                                v-if="render.registration_1C_By.show"
                                :text="render.registration_1C_By.data ? render.registration_1C_By.data(roll) : ''"
                                :type="
                                    typeof render.registration_1C_By.type === 'function'
                                    ? render.registration_1C_By.type(false, roll)
                                    : render.registration_1C_By.type
                                "
                                :width="render.registration_1C_By.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- __ Сейчас показываем везде. Раньше - Показываем только на вкладке Перемещенные на закрой -->
                            <!--<div v-if="tabs.moved.shown" class="flex">-->

                            <!-- __ Флаг перемещения на закрой -->
                            <AppLabel
                                v-if="render.moveToCutFlag.show"
                                :text="render.moveToCutFlag.data ? render.moveToCutFlag.data(roll) : ''"
                                :text-size="typeof render.moveToCutFlag.textSize === 'function' ? render.moveToCutFlag.textSize(roll) : 'micro'"
                                :type="
                                    typeof render.moveToCutFlag.type === 'function'
                                    ? render.moveToCutFlag.type(false, roll)
                                    : render.moveToCutFlag.type
                                "
                                :width="render.moveToCutFlag.width"
                                align="center"
                                class="cursor-pointer"
                                @click="changeMovingStatus(roll)"
                            />

                            <!-- __ Дата перемещения на закрой -->
                            <AppLabel
                                v-if="render.moveToCutAt.show"
                                :text="render.moveToCutAt.data ? render.moveToCutAt.data(roll) : ''"
                                :type="
                                    typeof render.moveToCutAt.type === 'function'
                                    ? render.moveToCutAt.type(false, roll)
                                    : render.moveToCutAt.type
                                "
                                :width="render.moveToCutAt.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- __ Ответственный за перемещение на закрой -->
                            <AppLabel
                                v-if="render.moveToCutBy.show"
                                :text="render.moveToCutBy.data ? render.moveToCutBy.data(roll) : ''"
                                :type="
                                    typeof render.moveToCutBy.type === 'function'
                                    ? render.moveToCutBy.type(false, roll)
                                    : render.moveToCutBy.type
                                "
                                :width="render.moveToCutBy.width"
                                align="center"
                                text-size="micro"
                            />

                            <!--</div>-->

                            <!-- __ Примечание -->
                            <AppLabel
                                v-if="render.description.show"
                                :text="render.description.data ? render.description.data(roll) : ''"
                                :type="
                                    typeof render.description.type === 'function'
                                    ? render.description.type(false, roll)
                                    : render.description.type
                                "
                                :width="render.description.width"
                                text-size="micro"
                            />

                            <!-- __ Комментарий -->
                            <!--<AppLabel-->
                            <!--    v-if="render.comment.show"-->
                            <!--    :text="render.comment.data(roll)"-->
                            <!--    :title="render.comment.title"-->
                            <!--    :type="render.comment.type(false, roll)"-->
                            <!--    :width="render.comment.width"-->
                            <!--    text-size="micro"-->
                            <!--/>-->


                            <!-- __ Комментарий -->
                            <AppInputTextTS
                                :id="`comment-label-${roll.id}`"
                                v-model:textValue.trim="roll.comment"
                                :type="
                                    typeof render.comment.type === 'function'
                                    ? render.comment.type(false, roll)
                                    : render.comment.type
                                "
                                :width="render.comment.width"
                                class="mt-[2px]"
                                height="h-[30px]"
                                placeholder=""
                                text-size="micro"
                                @leave-focus="handleComment(roll)"
                                @keyup.enter="handleComment(roll)"
                            />

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="mt-5">
                <!-- __ Примечание -->
                <AppLabel
                    align="center"
                    height="h-[100px]"
                    text="Данные не найдены..."
                    text-size="huge"
                    type="warning"
                    width="w-[300px]"
                />
            </div>
        </div>
    </div>

    <!-- __ Модальное окно для подтверждений -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!-- __ Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />
</template>

<script lang="ts" setup>
import type { IRenderData, IRollMove, IRollMoveRender, ISelectData, ISelectDataItem } from '@/types'

import { reactive, ref, watch } from 'vue'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import { FABRIC_ROLL_STATUS } from '@/app/constants/fabrics.js'

import { getFormatFIO } from '@/app/helpers/workers/helpers_workers.js'
import { getTypeByRollStatus } from '@/app/helpers/manufacture/helpers_fabric.js'
import {
    formatDateAndTimeInShortFormat,
    getDateFromDateTimeString,
    validateInputDateHelper
} from '@/app/helpers/helpers_date.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'


interface ITab {
    id: number,
    shown: boolean,
    enabled: boolean,
    name: string[],
    type: string,
    code: number,
}

type ITabs = Record<string, ITab>

type IDataInputObj = {
    newValue: string
    oldValue: string
}


const fabricsStore = useFabricsStore()

// __ Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const COMMON_CODE = -1
const tabs: ITabs = reactive({
    common: {
        id: 1,
        shown: false,
        enabled: true,
        name: ['Учет', 'общий'],
        type: 'warning',
        code: COMMON_CODE,
    },
    done: {
        id: 2,
        shown: false,
        enabled: true,
        name: ['Учет', 'выполненных рулонов'],
        type: FABRIC_ROLL_STATUS.DONE.TYPE,
        code: FABRIC_ROLL_STATUS.DONE.CODE,
    },
    registered_1C: {
        id: 3,
        shown: false,
        enabled: false,
        name: ['Учет поставленных', 'в 1С рулонов'],
        type: FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE,
        code: FABRIC_ROLL_STATUS.REGISTERED_1C.CODE,
    },
    unregistered_1C: {
        id: 4,
        shown: false,
        enabled: false,
        name: ['Учет не поставленных', 'в 1С рулонов'],
        type: FABRIC_ROLL_STATUS.UNREGISTERED_1C.TYPE,
        code: FABRIC_ROLL_STATUS.UNREGISTERED_1C.CODE,
    },
    moved: {
        id: 5,
        shown: false,
        enabled: true,
        name: ['Учет перемещенных', 'на раскрой рулонов'],
        type: FABRIC_ROLL_STATUS.MOVED.TYPE,
        code: FABRIC_ROLL_STATUS.MOVED.CODE,
    },
})

// __ переключаем выбранную вкладку
const changeTab = (selectedTab: ITab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].id === selectedTab.id
        }
    }
    // console.log(selectedTab)
}

// __ Задаем активную вкладку
tabs.common.shown = true // делаем вкладку "общие данные" активной, чтобы запустить реактивность

// __ Префикс для сохранения состояния в localStorage
const FABRIC_COLLAPSE_PREFIX = 'fabric-collapse-'
const updateTemplate = ref(0)

// __ Получаем название поля для хранилища для сворачивания/разворачивания по ПС
const getFabricStorageName = (fabricId: number) => `${FABRIC_COLLAPSE_PREFIX}${fabricId.toString()}`

// __ Получаем отображаемые рулоны в зависимости от статуса группируя по ПС
const getRollsByStatus = (inRolls: IRollMove[], status = COMMON_CODE) => {
    // копируем массив рулонов, чтобы не менять оригинал
    let $rolls: IRollMove[] = JSON.parse(JSON.stringify(inRolls))

    // __ Учет в 1С - признак
    // __ если передан признак учета в 1С, то фильтруем массив рулонов по признаку учета в 1С
    switch (status) {
        case COMMON_CODE:
            break
        case FABRIC_ROLL_STATUS.REGISTERED_1C.CODE:
            $rolls = $rolls.filter(roll => roll.isRegistered_1C)
            break
        case FABRIC_ROLL_STATUS.UNREGISTERED_1C.CODE:
            $rolls = $rolls.filter(roll => !roll.isRegistered_1C)
            break
        default:
            $rolls = $rolls.filter(roll => roll.status === status)
    }

    // if (status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) {
    //     $rolls = $rolls.filter(roll => roll.isRegistered_1C)
    // } else if (status !== COMMON_CODE) {
    //     $rolls = $rolls.filter((roll) => roll.status === status)
    // }

    // __ Старый код, где учет в 1С - статус
    // __ если передан статус, то фильтруем массив рулонов по статусу иначе убираем для общей вкладки перемещенные на закрой
    // if (status !== COMMON_CODE) {
    //     $rolls = $rolls.filter((roll) => roll.status === status)
    // } else {
    //     $rolls = $rolls.filter((roll) => roll.status !== FABRIC_ROLL_STATUS.MOVED.CODE)
    // }

    // создаем массив уникальных id ПС
    const uniqueFabricIds = new Set<number>()
    $rolls.forEach((roll) => {
        uniqueFabricIds.add(roll.fabric.fabric_id)
    })

    // const uniqueFabricIdsArray = Array.from(uniqueFabricIds)

    // __ создаем массив объектов с рулонами по ПС с группировкой по ПС и сортировкой по id
    const resultArray: IRollMoveRender[] = []

    for (const fabricId of uniqueFabricIds) {
        const fabricRolls = $rolls.filter((roll) => roll.fabric.fabric_id === fabricId).sort((a, b) => a.id - b.id)
        const fabricTextileLength = fabricRolls.reduce((amount, roll) => amount + roll.textile_length, 0)
        // const fabricLength = fabricRolls.reduce((amount, roll) => amount + roll.textile_length / roll.rate, 0)
        const fabricLength = fabricRolls.reduce((amount, roll) => amount + roll.fabric_length, 0)

        resultArray.push({
            fabric: {
                id: fabricId,
                displayName: fabricRolls[0].fabric.display_name,
                textileLength: fabricTextileLength,
                fabricLength: fabricLength,
                collapsed: localStorage.getItem(getFabricStorageName(fabricId)) === 'true', // для сворачивания/разворачивания, запоминаем состояние
            },

            rolls: fabricRolls,
        })
    }

    return resultArray
}

// __ Получаем с API все выполненные рулоны + рулоны в 1С + рулоны на закрой
const getNotAcceptedToCutRolls = async () => {
    return await fabricsStore.getNotAcceptedToCutRolls()
    // const rolls = await fabricsStore.getNotAcceptedToCutRolls()
    // return rolls
    // return rolls.filter((roll) => roll.status !== FABRIC_ROLL_STATUS.MOVED.CODE) //  рулоны в 1С + рулоны на закрой
}

// __ Получаем все рулоны c API
const allRolls = ref<IRollMove[]>([])
allRolls.value = await getNotAcceptedToCutRolls() as IRollMove[]

// __ преобразуем в структуру для отображения
const doneRolls = ref<IRollMoveRender[]>([])
doneRolls.value = getRollsByStatus(allRolls.value)
console.log('allRolls: ', allRolls.value)
console.log('doneRolls: ', doneRolls.value)


// __ Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref<any>(null)
const modalText = ref<string[]>([])
const modalType = ref('danger')

// __ Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => (calloutShow.value = false), delay) // закрываем callout

// __ Задаем глобальный объект для унификации отображения рулонов
const FRACTION_DIGITS = 2

const getTypeOfRoll = (roll: IRollMove, flag = false) => {
    if (flag) return 'primary'
    return getTypeByRollStatus(roll?.status, roll.isRegistered_1C)
}

const render: IRenderData = reactive({
    rollNumber: {
        header: ['№', 'рул.'],
        width: 'w-[60px]',
        show: true,
        title: 'Учетный номер рулона',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => roll.id.toString(),
    },
    fabric: {
        header: ['Полотно', 'стеганное'],
        width: 'w-[250px]',
        show: true,
        title: 'Полотно стеганное',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => roll.fabric.display_name,
    },
    textileLength: {
        header: ['Ткань', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        title: 'Ткань, м.п.',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => roll.textile_length.toFixed(FRACTION_DIGITS),
    },
    fabricLength: {
        header: ['ПС', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        title: 'ПС, м.п.',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => roll.fabric_length.toFixed(FRACTION_DIGITS),
        // data: (roll) => (roll.textile_length / roll.rate).toFixed(FRACTION_DIGITS),
    },
    finishAt: {
        header: ['Дата', 'пр-ва'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата производства',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => formatDateAndTimeInShortFormat(roll.finish_at, false),
    },
    finishBy: {
        header: ['Ответственный за', 'производство'],
        width: 'w-[150px]',
        show: true,
        title: 'Дата производства',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => getFormatFIO(roll.finish_by),
    },
    registration_1C_Flag: {
        header: ['Учет', 'в 1С'],
        width: 'w-[60px]',
        show: true,
        title: 'Учет в 1С',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        textSize: (roll) => (roll.registration_1C_by.id !== 0 ? 'large' : 'mini'),
        data: (roll) => (roll.isRegistered_1C ? '✅' : '❌'),
        // data: (roll) => (roll.registration_1C_by.id !== 0 ? '✅' : '❌'),
        // textSize: (roll) => roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE ? 'large' : 'mini',
        // data: (roll) => roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE ? '✅' : '❌',
    },
    registration_1C_At: {
        header: ['Дата', 'учета в 1С'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата учета в 1С',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => formatDateAndTimeInShortFormat(roll.registration_1C_at, false),
    },
    registration_1C_By: {
        header: ['Ответственный за', 'учет в 1С'],
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный за учет в 1С',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => (roll.registration_1C_by.id !== 0 ? roll.registration_1C_by.name : ''),
        // data: (roll) => roll.registration_1C_by.id !== 0 ?  getFormatFIO(roll.registration_1C_by) : '',
    },
    moveToCutFlag: {
        header: ['--->', 'закрой'],
        width: 'w-[60px]',
        show: true,
        title: 'Перемещение на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        textSize: (roll) => (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE ? 'large' : 'mini'),
        data: (roll) => (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE ? '✅' : '❌'),
    },
    moveToCutAt: {
        header: ['Дата --->', 'закрой'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата перемещения на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => formatDateAndTimeInShortFormat(roll.move_to_cut_at, false),
    },
    moveToCutBy: {
        header: ['Ответственный за', '---> на закрой'],
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный за перемещение на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => (roll.move_to_cut_by.id !== 0 ? roll.move_to_cut_by.name : ''),
        // data: (roll) => roll.move_to_cut_by.id !== 0 ? getFormatFIO(roll.move_to_cut_by) : '',
    },
    description: {
        header: ['Примечание', ''],
        width: 'w-[400px]',
        show: true,
        title: 'Примечание',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => roll.descr,
    },
    comment: {
        header: ['Комментарий', ''],
        width: 'w-[400px]',
        show: true,
        title: 'Комментарий',
        type: (flag = false, roll) => getTypeOfRoll(roll, flag),
        data: (roll) => roll.comment,
    },
})

// __ Создаем статусы рулонов для фильтра
const rollStatuses: ISelectData = {
    name: 'statuses',
    data: [
        {
            id: 0,
            name: 'Все статусы',
            selected: true,
            disabled: false
        },
        {
            id: FABRIC_ROLL_STATUS.DONE.CODE,
            name: FABRIC_ROLL_STATUS.DONE.TITLE,
            selected: false,
            disabled: false
        },
        {
            id: FABRIC_ROLL_STATUS.MOVED.CODE,
            name: FABRIC_ROLL_STATUS.MOVED.TITLE,
            selected: false,
            disabled: false,
        },
        // {
        //     id: FABRIC_ROLL_STATUS.REGISTERED_1C.CODE,
        //     name: FABRIC_ROLL_STATUS.REGISTERED_1C.TITLE,
        //     selected: false,
        //     disabled: false,
        // },
        // {
        //     id: FABRIC_ROLL_STATUS.UNREGISTERED_1C.CODE,
        //     name: FABRIC_ROLL_STATUS.UNREGISTERED_1C.TITLE,
        //     selected: false,
        //     disabled: false,
        // },

    ],
}

// __ Ставим на учет в 1С
// __ Здесь как признак
const changeRegistrationStatus = async (roll: IRollMove) => {
    return
    // if (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE) {
    //     calloutType.value = 'danger'
    //     calloutText.value = 'Рулон уже перемещен на закрой.'
    //     calloutShow.value = true
    //     calloutClose()
    //     return
    // }
    /*
        if (!roll.isRegistered_1C) {
            modalText.value = ['Будет установлен признак учета рулона в 1С.', 'Продолжить?']
            modalType.value = FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE

            const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
            if (answer) {
                roll.status = true
                // roll.status = FABRIC_ROLL_STATUS.REGISTERED_1C.CODE
                const res = await fabricsStore.setRollRegisteredStatus(roll.id, roll.status)
                allRolls.value = await getNotAcceptedToCutRolls()
                // console.log(res)
            }
        } else {
            modalText.value = ['Будет снят признак учета рулона в 1С.', 'Продолжить?']
            modalType.value = FABRIC_ROLL_STATUS.DONE.TYPE

            const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
            if (answer) {
                roll.status = false
                // roll.status = FABRIC_ROLL_STATUS.DONE.CODE
                const res = await fabricsStore.setRollRegisteredStatus(roll.id, roll.status)
                allRolls.value = await getNotAcceptedToCutRolls()
                // console.log(res)
            }
        }
    */
}

// Здесь реализован как статус
/**
 const changeRegistrationStatus_Old = async (roll: IRollMove) => {
 // console.log(roll)
 if (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE) {
 calloutType.value = 'danger'
 calloutText.value = 'Рулон уже перемещен на закрой.'
 calloutShow.value = true
 calloutClose()
 return
 }

 if (roll.status === FABRIC_ROLL_STATUS.DONE.CODE) {
 modalText.value = ['Будет установлен статус учета рулона в 1С.', 'Продолжить?']
 modalType.value = FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE

 const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
 if (answer) {
 roll.status = FABRIC_ROLL_STATUS.REGISTERED_1C.CODE
 const res = await fabricsStore.setRollRegisteredStatus(roll.id, roll.status)
 allRolls.value = await getNotAcceptedToCutRolls()
 // console.log(res)
 }
 } else if (roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) {
 modalText.value = ['Будет снят статус учета рулона в 1С.', 'Продолжить?']
 modalType.value = FABRIC_ROLL_STATUS.DONE.TYPE

 const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
 if (answer) {
 roll.status = FABRIC_ROLL_STATUS.DONE.CODE
 const res = await fabricsStore.setRollRegisteredStatus(roll.id, roll.status)
 allRolls.value = await getNotAcceptedToCutRolls()
 // console.log(res)
 }
 }
 }
 */
//__ +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// __ Перемещаем на закрой

const changeMovingStatus = async (roll: IRollMove) => {
    // log(roll)

    if (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE) {
        calloutType.value = 'danger'
        calloutText.value = 'Рулон уже перемещен на закрой.'
        calloutShow.value = true
        calloutClose()
        return
    }

    // __ roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE - чтобы сохранить старый функционал, когда учет в 1С - часть статуса
    if (roll.status === FABRIC_ROLL_STATUS.DONE.CODE || roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) {
        modalText.value = ['Рулон:', `№ ${roll.id}`, `ПС: ${roll.fabric.display_name}`, 'будет перемещен на закрой.', 'Продолжить?']
        modalType.value = FABRIC_ROLL_STATUS.MOVED.TYPE

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            roll.status = FABRIC_ROLL_STATUS.MOVED.CODE
            /*const res = */
            await fabricsStore.setRollMovedStatus(roll.id, roll.status)
            allRolls.value = await getNotAcceptedToCutRolls()
            // console.log(res)
        }
    }
}

// __ Здесь реализован как статус

// const changeMovingStatus_Old = async (roll: IRollMove) => {
//     // console.log(roll)
//
//     // Проверяем что рулон учтен в 1С
//     if (roll.registration_1C_by.id === 0) {
//         calloutType.value = 'danger'
//         calloutText.value = 'Рулон не может быть перемещен на закрой без учета в 1С.'
//         calloutShow.value = true
//         calloutClose()
//         return
//     }
//
//     if (roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) {
//         modalText.value = ['Рулон будет перемещен на закрой.', 'Продолжить?']
//         modalType.value = FABRIC_ROLL_STATUS.MOVED.TYPE
//
//         const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
//         if (answer) {
//             roll.status = FABRIC_ROLL_STATUS.MOVED.CODE
//             /*const res = */await fabricsStore.setRollMovedStatus(roll.id, roll.status)
//             allRolls.value = await getNotAcceptedToCutRolls()
//             // console.log(res)
//         }
//     } else if (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE) {
//         modalText.value = ['Рулон будет перемещен на стежку.', 'Продолжить?']
//         modalType.value = FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE
//
//         const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
//         if (answer) {
//             roll.status = FABRIC_ROLL_STATUS.REGISTERED_1C.CODE
//             /*const res = */await fabricsStore.setRollMovedStatus(roll.id, roll.status)
//             allRolls.value = await getNotAcceptedToCutRolls()
//             // console.log(res)
//         }
//     }
// }

// __ ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// __ Фильтры
const fabricFilter = ref('')
const rollNumberFilter = ref('')
const statusFilter = ref(0)
const statusFilterType = ref('light')
const descriptionFilter = ref('')
const commentFilter = ref('')
const finishAtFilter = ref('')
const movedAtFilter = ref('')
const register1CAtFilter = ref('')

// __ Обработка выбора фильтра статуса
const filterByStatus = (item: ISelectDataItem) => {
    statusFilter.value = item.id
    // console.log(item)

    if (item.id === 0) {
        statusFilterType.value = 'light'
        return
    }

    const selectedStatus = Object.keys(FABRIC_ROLL_STATUS)
        .find(rollStatus => FABRIC_ROLL_STATUS[rollStatus].CODE === item.id)

    statusFilterType.value = selectedStatus ? FABRIC_ROLL_STATUS[selectedStatus].TYPE : 'light'
}

// __ Сворачиваем/разворачиваем ПС
const toggleCollapse = (fabricGroup: IRollMoveRender) => {
    fabricGroup.fabric.collapsed = !fabricGroup.fabric.collapsed
    localStorage.setItem(getFabricStorageName(fabricGroup.fabric.id), fabricGroup.fabric.collapsed.toString()) // сохраняем в локальное хранилище
}


// __ Обработчик ввода номера рулона для фильтра
const handleRollNumberInput = (event: InputEvent) => {
    const target = event.target as HTMLInputElement     // Утверждаем, что event.target является HTMLInputElement
    rollNumberFilter.value = target.value.replace(/[^0-9]/g, '')  // Оставляем только цифры (0-9)
}

// __ Обработчик ввода даты пр-ва
// Объект для манипуляции с вводом и выводом даты
const handleFinishAtInputObj: IDataInputObj = {
    newValue: '',
    oldValue: '',
}
const handleFinishAtInput = (event: InputEvent) => {
    const target = event.target as HTMLInputElement
    handleFinishAtInputObj.newValue = target.value
    validateInputDateHelper(handleFinishAtInputObj)  // вся логика изменения объекта будет внутри функции
    finishAtFilter.value = handleFinishAtInputObj.newValue
}


// __ Обработчик ввода даты перемещения на закрой
// Объект для манипуляции с вводом и выводом даты
const handleMovedAtInputObj: IDataInputObj = {
    newValue: '',
    oldValue: '',
}
const handleMovedAtInput = (event: InputEvent) => {
    const target = event.target as HTMLInputElement
    handleMovedAtInputObj.newValue = target.value
    validateInputDateHelper(handleMovedAtInputObj)  // вся логика изменения объекта будет внутри функции
    movedAtFilter.value = handleMovedAtInputObj.newValue
}


// __ Обработчик ввода даты учета в 1С
// Объект для манипуляции с вводом и выводом даты
const handleRegistered1CAtInputObj: IDataInputObj = {
    newValue: '',
    oldValue: '',
}
const handleRegistered1CAtInput = (event: InputEvent) => {
    const target = event.target as HTMLInputElement
    handleRegistered1CAtInputObj.newValue = target.value
    validateInputDateHelper(handleRegistered1CAtInputObj)  // вся логика изменения объекта будет внутри функции
    register1CAtFilter.value = handleRegistered1CAtInputObj.newValue
}


// __ Вспомогалочка: преобразует исходные данные в нужную структуру для отображения в шаблоне
const reformatData = (rolls: IRollMove[], tabs: ITabs) => {
    const activeTab = Object.values(tabs).find((tab) => tab.shown)
    doneRolls.value = getRollsByStatus(rolls, activeTab!.code)   // преобразуем в структуру для отображения
}


// __ Поле обновляем поле ввода комментария
const handleComment = async (roll: IRollMove) => {
    await fabricsStore.updateRollComment(roll.id, roll.comment)
}

// __ Применяем фильтры
const filtersApply = (
    {
        fabricFilter,
        rollNumberFilter,
        finishAtFilter,
        statusFilter,
        descriptionFilter,
        commentFilter,
        movedAtFilter,
        register1CAtFilter
    }: {
        fabricFilter: string,
        rollNumberFilter: string,
        finishAtFilter: string,
        statusFilter: number,
        descriptionFilter: string,
        commentFilter: string,
        movedAtFilter: string,
        register1CAtFilter: string
    }
) => {

    console.log('filter allRolls.value: ', allRolls.value)

    let filteredAllRolls = allRolls.value
        .filter(roll => roll.id.toString().includes(rollNumberFilter))
        .filter(roll => roll.fabric.display_name.toLowerCase().includes(fabricFilter.toLowerCase()))
        .filter(roll => roll.descr.toLowerCase().includes(descriptionFilter.toLowerCase()))
        .filter(roll => roll.comment.toLowerCase().includes(commentFilter.toLowerCase()))
        .filter(roll => getDateFromDateTimeString(roll.finish_at).includes(finishAtFilter))
        .filter(roll => getDateFromDateTimeString(roll.move_to_cut_at).includes(movedAtFilter))
        .filter(roll => getDateFromDateTimeString(roll.registration_1C_at).includes(register1CAtFilter))

    if (
        [
            FABRIC_ROLL_STATUS.DONE.CODE,
            FABRIC_ROLL_STATUS.REGISTERED_1C.CODE,
            FABRIC_ROLL_STATUS.UNREGISTERED_1C.CODE,
            FABRIC_ROLL_STATUS.MOVED.CODE
        ].includes(statusFilter)) {
        switch (statusFilter) {
            case FABRIC_ROLL_STATUS.REGISTERED_1C.CODE:
                filteredAllRolls = filteredAllRolls.filter(roll => roll.isRegistered_1C)
                break
            case FABRIC_ROLL_STATUS.UNREGISTERED_1C.CODE:
                filteredAllRolls = filteredAllRolls.filter(roll => !roll.isRegistered_1C)
                break
            default:
                filteredAllRolls = filteredAllRolls.filter(roll => roll.status === statusFilter)
        }

    }

    return filteredAllRolls
}

// __ Следим за изменением активной вкладки
watch(
    () => tabs,
    (newActiveTabs) => {

        // __ Сбрасываем не общие фильтры

        statusFilter.value = 0          // Сбрасываем фильтр статуса
        statusFilterType.value = 'light'

        register1CAtFilter.value = ''   // Сбрасываем фильтр даты регистрации в 1С
        movedAtFilter.value = ''        // Сбрасываем фильтр даты перемещения

        // __ Применяем фильтры, меняем doneRolls.value
        const filteredAllRolls = filtersApply({
            fabricFilter: fabricFilter.value,
            rollNumberFilter: rollNumberFilter.value,
            finishAtFilter: finishAtFilter.value,
            statusFilter: statusFilter.value,
            descriptionFilter: descriptionFilter.value,
            commentFilter: commentFilter.value,
            movedAtFilter: movedAtFilter.value,
            register1CAtFilter: register1CAtFilter.value
        })

        reformatData(filteredAllRolls, newActiveTabs)
    },
    {deep: true /* immediate: true*/}
)

// __ Следим за изменением самих данных (важны статусы после изменения/сохранения)
watch(
    () => allRolls.value,
    (newAllRolls) => {
        allRolls.value = newAllRolls

        // __ Применяем фильтры, меняем doneRolls.value
        const filteredAllRolls = filtersApply({
            fabricFilter: fabricFilter.value,
            rollNumberFilter: rollNumberFilter.value,
            finishAtFilter: finishAtFilter.value,
            statusFilter: statusFilter.value,
            descriptionFilter: descriptionFilter.value,
            commentFilter: commentFilter.value,
            movedAtFilter: movedAtFilter.value,
            register1CAtFilter: register1CAtFilter.value
        })

        reformatData(filteredAllRolls, tabs)
    },
    {deep: true /*immediate: true*/}
)

//__ Реализация фильтров
watch(
    [
        () => fabricFilter.value,
        () => rollNumberFilter.value,
        () => finishAtFilter.value,
        () => statusFilter.value,
        () => descriptionFilter.value,
        () => commentFilter.value,
        () => movedAtFilter.value,
        () => register1CAtFilter.value,
    ],
    (
        [
            newFabricFilter,
            newRollNumberFilter,
            newFinishAtFilter,
            newStatusFilter,
            newDescriptionFilter,
            newCommentFilter,
            newMovedAtFilter,
            newRegister1CAtFilter,
        ], /*[
            oldFabricFilter,
            oldRollNumberFilter,
            oldFinishAtFilter,
            oldStatusFilter,
            oldDescriptionFilter,
            oldCommentFilter,
            oldMovedAtFilter,
            oldRegister1CAtFilter
        ]*/) => {

        const filteredAllRolls = filtersApply({
            fabricFilter: newFabricFilter,
            rollNumberFilter: newRollNumberFilter,
            finishAtFilter: newFinishAtFilter,
            statusFilter: newStatusFilter,
            descriptionFilter: newDescriptionFilter,
            commentFilter: newCommentFilter,
            movedAtFilter: newMovedAtFilter,
            register1CAtFilter: newRegister1CAtFilter
        })

        reformatData(filteredAllRolls, tabs) // __ Применяем фильтры, меняем doneRolls.value
    }
)

</script>

<style scoped></style>
