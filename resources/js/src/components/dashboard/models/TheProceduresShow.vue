<template>

    <div>
        <div v-for="procedure of procedures">
            <div class="w-max-[400px] border-2 m-1">
                {{ procedure.code_1c + ' | ' + procedure.name }}
                <pre><code contenteditable="true">{{ procedure.text }}</code></pre>
            </div>
        </div>

    </div>

</template>

<script lang="ts" setup>
import { useModelsStore } from '@/stores/ModelsStore.ts'
import { ref } from 'vue'

const modelsStore = useModelsStore()
const procedures = ref(await modelsStore.getProcedures()) // даем запрос на получение данных по моделям

procedures.value = procedures.value.sort((a, b) => parseInt(a.code_1c) - parseInt(b.code_1c))

console.log('procedures: ', procedures.value)

</script>

<style scoped>
pre {
    @apply
    p-3
    max-w-[1024px]
    min-h-[200px]
    text-xs;
    /* Ограничиваем ширину контейнера, чтобы он не растягивался бесконечно */
    /* max-width: 30%;*/
    /*    max-width: 1024px;
        max-height: 600px;*/

    /* Делаем содержимое прокручиваемым по горизонтали, если оно выходит за max-width */
    overflow-x: auto;
    /*overflow-y: auto;*/

    /* Это важно: отключает автоматический перенос строк, чтобы текст прокручивался */
    /* white-space: pre;*/

    /* Сохраняет пробелы, но позволяет строкам переноситься, когда они достигают границы */
    white-space: pre-wrap;

    /* Необязательно: если слово (например, очень длинное имя файла) не помещается,
       оно будет "разбито" для переноса (работает в паре с pre-wrap). */
    word-wrap: break-word;


}

code {
    font-family: monospace; /* Использование моноширинного шрифта */
    color: #333; /* Цвет текста */
}
</style>
