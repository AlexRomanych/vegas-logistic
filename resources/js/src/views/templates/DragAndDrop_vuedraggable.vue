<template>
    <div>
        <div>
            <button @click="sort">
                To original order
            </button>
        </div>

        <div>
            <h3>Transition</h3>
            <draggable
                v-model="list"
                :component-data="{ tag: 'ul', name: 'flip-list', type: 'transition' }"
                class="list-group"
                item-key="order"

                tag="ul"
                :="dragOptions"
                @end="console.log(list)"
                @start="isDragging = true"
            >
                <template #item="{ element }">
                    <li class="list-group-item">
                        {{ element.name }}
                    </li>
                </template>
            </draggable>

        </div>

    </div>
</template>

<script setup>
import draggable from "vuedraggable";
import {computed, ref} from "vue";

const message = [
    "vue.draggable",
    "draggable",
    "component",
    "for",
    "vue.js 2.0",
    "based",
    "on",
    "Sortablejs"
];

const order = ref(message.length);

const list = ref(message.map((name, index) => {
    return {name, order: index + 1, fixed: false}
}))


const sort = () => {
    list.value = list.value.sort((a, b) => {
            return a.order - b.order
        }
    )
}

const dragOptions = computed(() => {
    return {
        animation: 300,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
    };
})


const isDragging = ref(false);


// export default {
// name: "transition-example",
// display: "Transition",
// order: 6,
// components: {
//     draggable
// },
// data() {
//     return {
//         list: message.map((name, index) => {
//             return { name, order: index + 1 };
//         })
//     };
// },
// methods: {
//     sort() {
//         this.list = this.list.sort((a, b) => a.order - b.order);
//     }
// },
// computed: {
//     dragOptions() {
//         return {
//             animation: 0,
//             group: "description",
//             disabled: false,
//             ghostClass: "ghost"
//         };
//     }
// }
// };
</script>

<style>
.button {
    margin-top: 35px;
}

.flip-list-move {
    transition: transform 0.5s;
}

.no-move {
    transition: transform 0s;
}

.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

.list-group {
    min-height: 20px;
}

.list-group-item {
    cursor: move;
}

.list-group-item div {
    cursor: pointer;
}
</style>

<!--<template>-->


<!--    <draggable-->
<!--        v-model="myItems"-->
<!--        item-key="id"-->
<!--        @start="log"-->
<!--        @end="log"-->
<!--    >-->
<!--        <template #item="{ element }">-->
<!--            <div class="list-item">-->
<!--                {{ element.name }}-->
<!--                {{ element.id}}-->
<!--            </div>-->
<!--        </template>-->
<!--    </draggable>-->
<!--</template>-->


<!--<script setup>-->
<!--import draggable from 'vuedraggable'-->
<!--import { ref } from 'vue'-->

<!--const myItems = ref([-->
<!--    { id: 1, name: 'Пункт 1' },-->
<!--    { id: 2, name: 'Пункт 2' },-->
<!--    { id: 3, name: 'Пункт 3' },-->
<!--])-->

<!--function log(event) {-->
<!--    console.log(myItems.value)-->
<!--}-->
<!--</script>-->


<!--<style scoped>-->
<!--.list-item {-->
<!--    padding: 10px;-->
<!--    margin: 5px 0;-->
<!--    background-color: #f0f0f0;-->
<!--    border: 1px solid #ccc;-->
<!--    cursor: grab;-->
<!--}-->
<!--</style>-->
