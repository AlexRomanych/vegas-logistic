// noinspection DuplicatedCode


import type {
    IAmountAndTimeBlock, IBlockManufLine,
    IBlockTask, IBlockTaskArrayDiff, IBlockTaskArrayLineDiffs,
    IBlockTaskLine, IBlockTaskOrder, IBlockTaskOrderLine, IBlockTaskStatus,
    IDay,
    IPlanMatrix,
    IRenderMatrixDiff,
    IRenderMatrixLineDiffs
} from '@/types'
import { BLOCK_MANUF_LINES, BLOCK_TASK_DRAFT, BLOCK_TASK_STATUSES } from '@/app/constants/blocks.ts'
import { round } from '@/app/helpers/helpers_lib.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'


// __ Проблема с draggable
// __ Если день пустой, то перетаскивание не срабатывает
// __ Поэтому добавляем пустое задание в пустой день
export function correctRenderMatrix(matrix: IPlanMatrix) {
    let draftId = -100
    matrix.forEach((week, weekIndex) => {

        week.forEach((day, dayIndex) => {
            const filteredDay = day.filter(item => item.id > -1)      // __ id === 0 (для добавленного СЗ)
            // let filteredDay = day.filter(item => item.id !== BLOCK_TASK_DRAFT.id)
            if (filteredDay.length === 0) {
                const draft = {
                    ...BLOCK_TASK_DRAFT,
                    id         : draftId--,
                    position   : 100,
                    block_lines: [],  /* !!! Тут пустой массив, потому что где-то по ссылке сохраняется  */
                }
                filteredDay.push(draft)
            } else {
                // __ Сортируем по позиции (по порядку)
                // filteredDay = filteredDay.sort((a, b) => a.position - b.position)
            }
            matrix[weekIndex][dayIndex] = filteredDay
            // matrix[weekIndex][dayIndex] = {...filteredDay, fullDay: true}
        })
    })

    return matrix
}


// __ Сортируем задания в матрице рендера по позиции + сортируем строки по позиции
export function sortRenderMatrixByTaskPosition(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            matrix[weekIndex][dayIndex] = day.sort((a, b) => a.position - b.position)
            matrix[weekIndex][dayIndex].forEach(blockTask => {
                blockTask.block_lines = blockTask.block_lines.sort((a: IBlockTaskLine, b: IBlockTaskLine) => a.position - b.position)
            })
        })
    })
    return matrix
}


// __ Очищаем матрицу рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrix(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            matrix[weekIndex][dayIndex] = day.filter(item => item.id > -1) // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
        })
    })
    return matrix
}

// __ Очищаем день матрицы рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrixDay<T extends IDay>(day: T[]): T[] {
    return [...day.filter(item => item.id > -1)] // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
}


// __ Разница между предыдущим и текущим состоянием
// __ Разница по задумке должна быть только в одной Заявке:
// __ Либо перемещение в рамках одного дня, либо из одного дня в другой
// __ Задача найти эти дни и эту Заявку

// --- ------------------------------------------------------------------------------------
/**
 *  __Находим глубокую разницу между массивами СЗ (текущим и копией)__ !!! Используется эта!
 * @param {IBlockTask[]} currentTasks  - __Массив после манипуляций (vuedraggable и т.д.)__
 * @param {IBlockTask[]} originalTasks - __Глубокая копия (tasksCopy)__
 */
export function getBlockTasksDiff(currentTasks: IBlockTask[], originalTasks: IBlockTask[]) {
    const diffs: IBlockTaskArrayDiff[] = []

    // console.log(currentTasks)
    // console.log(originalTasks)

    // __ Индексируем оригинал по ID для быстрого доступа
    const originalMap = new Map(originalTasks.map(task => [task.id, task]))
    const currentMap  = new Map(currentTasks.map(task => [task.id, task]))

    currentTasks.forEach((task) => {
        const original = originalMap.get(task.id)

        if (!original) {
            // __ Если задачи не было в исходном массиве

            const lineChanges: IBlockTaskArrayLineDiffs[] = []
            task.block_lines.forEach(line => lineChanges.push({
                lineId   : line.id,
                lineIdRef: line.id_ref,
                type     : 'ADDED',
                amount   : { old: null, new: line.amount },
                position : { old: null, new: line.position },
            }))

            diffs.push({
                taskId   : task.id,
                taskIdRef: task.id_ref,
                type     : 'ADDED',
                // current:     task,
                taskChanges: {
                    action_at: { old: null, new: task.action_at },
                    position : { old: null, new: task.position },
                    status   : { old: null, new: task.current_status.id ?? null },
                },
                lineChanges,

                // __ Добавочный статус, если он есть
                // statusId: task.statusId ?? -1,
            })
            return
        }

        // __ Сравниваем основные поля задачи
        const hasDateChanged     = task.action_at !== original.action_at
        const hasPositionChanged = task.position !== original.position
        const hasStatusChanged   = task.current_status.id !== original.current_status.id

        // __ Сравниваем строки пошива (детально)
        const lineDiffs = getTaskLinesDiff(task.block_lines, original.block_lines)

        // __ Если есть изменения хотя бы в одном месте
        if (hasDateChanged || hasPositionChanged || lineDiffs.length > 0 || hasStatusChanged) {
            diffs.push({
                taskId: task.id,
                type  : 'UPDATED',

                // __ Поля задачи
                taskChanges: {
                    action_at: hasDateChanged ? { old: original.action_at, new: task.action_at } : null,
                    position : hasPositionChanged ? { old: original.position, new: task.position } : null,
                    status   : hasStatusChanged ? { old: original.current_status.id, new: task.current_status.id } : null,
                },
                // __ Массив изменений в строках
                lineChanges: lineDiffs,

            })
        }
    })

    // __ 3. ПРОВЕРКА НА УДАЛЕНИЕ ЗАДАЧ (Новый блок)
    originalTasks.forEach((oldTask) => {
        if (!currentMap.has(oldTask.id)) {
            diffs.push({
                taskId: oldTask.id,
                type  : 'DELETED', // Указываем серверу, что эту задачу нужно удалить
            })
        }
    })

    return diffs
}

/**
 * __ Сравнение внутренних строк IBlockTaskLine
 * @param currentLines
 * @param originalLines
 */
function getTaskLinesDiff(currentLines: IBlockTaskLine[], originalLines: IBlockTaskLine[]) {
    const diffs: IBlockTaskArrayLineDiffs[] = []
    const originalLinesMap                  = new Map(originalLines.map(l => [l.id, l]))

    currentLines.forEach((line) => {
        const originalLine = originalLinesMap.get(line.id)

        if (!originalLine) {
            diffs.push({
                lineId   : line.id,
                lineIdRef: line.id_ref,
                type     : 'ADDED',
                // newPosition: line.position,
                amount  : { old: null, new: line.amount },
                position: { old: null, new: line.position },
            })
        } else {
            const isAmountChanged = line.amount !== originalLine.amount
            const isPosChanged    = line.position !== originalLine.position

            if (isAmountChanged || isPosChanged) {
                diffs.push({
                    lineId  : line.id,
                    type    : 'UPDATED',
                    amount  : isAmountChanged ? { old: originalLine.amount, new: line.amount } : null,
                    position: isPosChanged ? { old: originalLine.position, new: line.position } : null,
                })
            }
        }
    })

    // __ Проверка на удаление строк
    originalLines.forEach(oldLine => {
        if (!currentLines.find(l => l.id === oldLine.id)) {
            diffs.push({ lineId: oldLine.id, type: 'DELETED' })
        }
    })

    return diffs
}


// --- ------------------------------------------------------------------------------------
//__  Функция поиска различий с детальными данными по позициям
export function getDiffsWithPositions(currentMatrix: IPlanMatrix, copyMatrix: IPlanMatrix) {
    const diffs: IRenderMatrixDiff[] = []

    // __ 1. Индексируем копию (старые данные)
    const copyMap = new Map()
    copyMatrix.forEach((week, weekIdx) => {
        week.forEach((dayTasks, dayIdx) => {
            const dayOffset = weekIdx * 7 + dayIdx
            dayTasks.forEach(task => {

                // __ Сохраняем "слепок" состояния для сравнения
                copyMap.set(task.id, {
                    dayOffset,
                    position: task.position,
                    lines   : JSON.parse(JSON.stringify(task.block_lines)), // __ глубокая копия строк
                })
            })
        })
    })

    // __ 2. Сравниваем с текущим состоянием
    currentMatrix.forEach((week, weekIdx) => {
        week.forEach((dayTasks, dayIdx) => {
            const currentDayOffset = weekIdx * 7 + dayIdx

            dayTasks.forEach((task) => {
                const old = copyMap.get(task.id)

                if (!old) {

                    // __ Обработка совершенно новой задачи (если такое возможно)
                    diffs.push({ type: 'NEW_TASK', taskId: task.id, newPosition: task.position })
                    return
                }

                const isMoved      = old.dayOffset !== currentDayOffset
                const isPosChanged = old.position !== task.position

                // __ Проверяем детальные изменения в строках (block_lines)
                const lineDiffs = getLinesDetailedDiff(old.lines, task.block_lines)

                // __ Если хоть что-то изменилось — фиксируем
                if (isMoved || isPosChanged || lineDiffs.length > 0) {
                    diffs.push({
                        taskId: task.id,

                        // __ Информация по датам
                        dayFromOffset: old.dayOffset,
                        dayToOffset  : currentDayOffset,

                        // __ Информация по позиции самой задачи
                        oldTaskPosition  : old.position,
                        newTaskPosition  : task.position,
                        isPositionChanged: isPosChanged,
                        isMoved          : isMoved,

                        // __ Детализация по строкам
                        lineDiffs: lineDiffs,
                    })
                }
            })
        })
    })

    return diffs
}

// __ Вспомогательная функция для детального сравнения позиций и данных строк
function getLinesDetailedDiff(oldLines: IBlockTaskLine[], newLines: IBlockTaskLine[]) {
    const changes: IRenderMatrixLineDiffs[] = []

    newLines.forEach((newLine) => {
        const oldLine = oldLines.find(l => l.id === newLine.id)

        if (!oldLine) {
            changes.push({ lineId: newLine.id, type: 'ADDED', newPosition: newLine.position })
        } else {
            const isAmountChanged = oldLine.amount !== newLine.amount
            const isPosChanged    = oldLine.position !== newLine.position

            if (isAmountChanged || isPosChanged) {
                changes.push({
                    lineId           : newLine.id,
                    type             : 'UPDATED',
                    oldPosition      : oldLine.position,
                    newPosition      : newLine.position,
                    oldAmount        : oldLine.amount,
                    newAmount        : newLine.amount,
                    isPositionChanged: isPosChanged,
                    isAmountChanged  : isAmountChanged,
                })
            }
        }
    })

    // __ Проверка на удаление (если нужно для БД)
    oldLines.forEach(oldLine => {
        if (!newLines.find(l => l.id === oldLine.id)) {
            changes.push({ lineId: oldLine.id, type: 'DELETED' })
        }
    })

    return changes
}

// --- ----------------------------------------------------------------------------------

// __ Создаем сам объект данных с ключами из BLOCK_MANUF_LINES и {time: 0, amount: 0} и инициализируем его нулями
export function createAmountAndTimeObj() {
    return Object.values(BLOCK_MANUF_LINES).reduce((acc, value) => {
        acc[value] = { time: 0, amount: 0 }
        return acc
    }, {} as IAmountAndTimeBlock)
}


// __ Получаем трудозатраты по Заявке или массиву строк (Содержимого) в формате объекта
export function getBlockTaskAmountAndTime(item: IBlockTask | IBlockTaskLine[]) {

    //  __ Создаем сам объект данных с ключами из BLOCK_MANUF_LINES и {time: 0, amount: 0} и инициализируем его нулями
    // const amountAndTimeObj = createAmountAndTimeObj()

    // __ Проверяем, что пришло на вход
    let itemArr = []
    if (Array.isArray(item)) {
        itemArr = item
    } else {
        itemArr = item.block_lines
    }

    const data = Object.groupBy(itemArr, item => item.manuf_line)

    return Object.values(BLOCK_MANUF_LINES).reduce((acc, value) => {
        const findTableKey = Object.keys(data).find(key => key === value)
        if (findTableKey) {
            const key    = findTableKey as IBlockManufLine
            const amount = data[key]!.reduce((acc, line) => acc + line.amount, 0)
            const time   = data[key]!.reduce((acc, line) => acc + line.time, 0)
            acc[value]   = { time, amount }
        } else {
            acc[value] = { time: 0, amount: 0 }
        }

        return acc
    }, {} as IAmountAndTimeBlock)

}


// --- ------------------------------------------------------------------------------------
// __ Превращаем массив объектов IBlockTask [{}, {}, ...] в массив массивов объектов [[...], [...]]
// __ которые сгруппированы по одинаковой Заявке с возможностью учитывать статусы Заявок в определенном дне
export function getBlockTasksGroupedByOrder(blockTasks: IBlockTask[], applyStatus: boolean = true) {
    const clearDay = clearRenderMatrixDay(blockTasks) // __ Возвращаем новый массив без пустых элементов
    const grouped  = clearDay.reduce((acc, item) => {

        // __ Создаем уникальный составной ключ
        let key: string
        if (applyStatus) {
            key = `${item.order.id}-${item.current_status.id}`
        } else {
            key = `${item.order.id}`
        }

        // __ Если такого task_id еще нет в аккумуляторе, создаем пустой массив
        if (!acc[key]) {
            acc[key] = []
        }
        // __ Добавляем текущий объект в массив соответствующего task_id
        acc[key].push(item)
        return acc
    }, {} as IBlockTask)

    // __ Превращаем объект { 1: [...], 2: [...] } в массив массивов [[...], [...]]
    return Object.values(grouped)
}


// --- ------------------------------------------------------------------------------------
// __ Проверяем, есть ли в конкретном дне СЗ для какой-то конкретной Заявки
// __ Если передан entity типа IBlockTask и applyStatus = true, то проверяем еще на одинаковость статусов
export function getBlockTasksSameOrderInDay(
    entity: IBlockTask | IBlockTaskOrder | number,
    tasksList: IBlockTask[],
    date: string | null  = null,
    applyStatus: boolean = false) {

    let item
    if (isBlockTask(entity)) {
        item = entity.order.id
        if (!date) date = entity.action_at

    } else if (isBlockTaskOrder(entity)) {
        item = entity.id
    } else if (typeof entity === 'number') {
        item = entity
    } else {
        return []
        // throw new Error('Invalid entity type')
    }

    if (!date) {
        return []
    }

    if (isBlockTask(entity) && applyStatus) {
        return tasksList.filter(task =>
            task.action_at === date &&
            task.order.id === item &&
            task.current_status.id === entity.current_status.id)
    }

    return tasksList.filter(task => task.action_at === date && task.order.id === item)
}

// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли СЗ расчетным (AVERAGE) или нет
export function isTaskAverage(entity: IBlockTask | IBlockTaskLine[]) {

    let items: IBlockTaskLine[]
    if (isBlockTask(entity)) {
        items = entity.block_lines
    } else if (Array.isArray(entity)) {
        items = entity
    } else {
        throw new Error('isTaskAverage: unknown incoming data type')
    }

    for (let i = 0; i < items.length; i++) {
        if (items[i].is_average) return true
    }

    return false
}

// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли Статус СЗ "Создано" или "Создано при закрытии"
export function isTaskStatusCreated(entity: IBlockTask | IBlockTaskStatus | number): boolean {
    let item: number
    if (isBlockTask(entity)) {
        item = entity.current_status.id
    } else if (isBlockTaskStatus(entity)) {
        item = entity.id
    } else if (typeof entity === 'number') {
        item = entity
    } else {
        throw new Error('Invalid entity type')
    }
    return item === BLOCK_TASK_STATUSES.CREATED.ID || item === BLOCK_TASK_STATUSES.ROLLING.ID
}

// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли Статус СЗ "Выполняется"
export function isTaskStatusRunning(entity: IBlockTask | IBlockTaskStatus | number): boolean {
    let item: number
    if (isBlockTask(entity)) {
        item = entity.current_status.id
    } else if (isBlockTaskStatus(entity)) {
        item = entity.id
    } else if (typeof entity === 'number') {
        item = entity
    } else {
        throw new Error('Invalid entity type')
    }
    return item === BLOCK_TASK_STATUSES.RUNNING.ID
}

// --- -------------------------------------------------------------------------------------
// __ Устанавливаем необходимы порядок (пронумеровываем заявки в массиве)
// __ в соответствии со статусами
// __ 1. Сначала статус СЗ - Done по возрастанию даты
// __ 2. Потом статус СЗ   - Running по возрастанию даты
// __ 3. Потом статус СЗ   - Pending по возрастанию даты
// __ 4. Потом статус СЗ   - Created по возрастанию даты
export const orderBlockTasksByStatus = (tasks: IBlockTask[]): IBlockTask[] => {
    // 1. Описываем веса для статусов.
    // Чем меньше значение, тем выше (раньше) элемент в массиве.
    const statusPriority: Record<number, number> = {
        [BLOCK_TASK_STATUSES.DONE.ID]   : 1,
        [BLOCK_TASK_STATUSES.RUNNING.ID]: 2,
        [BLOCK_TASK_STATUSES.PENDING.ID]: 3,
        [BLOCK_TASK_STATUSES.ROLLING.ID]: 4,
        [BLOCK_TASK_STATUSES.CREATED.ID]: 5,
    }

    // Создаем копию массива, чтобы не мутировать оригинал (важно для Vue)
    const result = [...tasks]
        .sort((a, b) => {
            const weightA = statusPriority[a.current_status.id] ?? 999
            const weightB = statusPriority[b.current_status.id] ?? 999

            // Сначала сравниваем по весу статуса
            if (weightA !== weightB) {
                return weightA - weightB
            }

            // Если статусы одинаковые, сравниваем по дате (action_at)
            // Преобразуем строки даты в числа (timestamp) для вычитания
            const dateA = new Date(a.action_at).getTime()
            const dateB = new Date(b.action_at).getTime()

            return dateA - dateB
        })

    result.forEach((_, index, array) => {
        // console.log(_ , index)
        array[index].position = index + 1
    })

    // console.log(result)

    return result
}


// --- ------------------------------------------------------------------------------------
// __ Пересчитываем позиции СЗ в массиве СЗ на определенный день
export function repositionBlockTaskInDay(tasks: IBlockTask[], action_at: string) {
    tasks
        // __ Отбираем только объекты на нужную дату
        .filter(item => item.action_at === action_at)
        // __ Сортируем их по возрастанию текущей позиции (включая x.1)
        .sort((a, b) => a.position - b.position)
        // __ Мутируем каждый объект, присваивая новый порядковый номер
        .forEach((item, index) => {
            item.position = index + 1
        })
    return tasks
}


// __ Пересчитываем позицию по порядку записей в массиве строк (BlockTaskLine[]) по ссылке
// __ Пересчитываем позицию именно в том порядке, в котором они находятся в исходно массиве
// __ (как определил специалист ОПП при перетаскивании строк или упорядочивании или сортировке)
export function repositionBlockTaskLines(entity: IBlockTask | IBlockTaskLine[]) {
    let items
    if (isBlockTask(entity)) {
        items = entity.block_lines
    } else if (Array.isArray(entity)) {
        items = entity
    } else {
        return []
    }

    items.forEach((_, index, arr) => {
        arr[index].position = index + 1
    })
    return items
}


// __ Пересчитываем позиции СЗ в матрице рендера после перетаскивания мышью
export function setTaskPositionInRenderMatrix(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {

            // __ Новая сортировка по позиции (СЗ статусы, которые не равны "Создано" - остаются на своих местах)
            const nonCreatedStatusTasks: IBlockTask[] = []
            const createdStatusTasks: IBlockTask[]    = []
            const resultDay: IBlockTask[]             = []

            // __ Фильтруем по статусу
            for (let i = 0; i < day.length; i++) {
                if (isTaskStatusCreated(day[i] as IBlockTask)) {
                    createdStatusTasks.push(day[i] as IBlockTask)
                } else {
                    nonCreatedStatusTasks.push(day[i] as IBlockTask)
                }
            }

            nonCreatedStatusTasks.forEach(task => resultDay.push(task))
            createdStatusTasks.forEach(task => resultDay.push(task))

            // console.log('nonCreatedStatusTasks: ', nonCreatedStatusTasks)
            // console.log('createdStatusTasks: ', createdStatusTasks)
            // console.log('resultDay: ', resultDay)
            // matrix[weekIndex][dayIndex] = resultDay

            matrix[weekIndex][dayIndex] = resultDay
                .map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля
            // .sort((a, b) => a.position - b.position)
            // __ Старая сортировка по позиции без учета всплытия статусов - без первой части
            // matrix[weekIndex][dayIndex] = day.map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля
        })
    })
    return matrix
}

// --- ------------------------------------------------------------------------------------
// __ Объединяем СЗ с одинаковыми Заявками (Заявки, к которым принадлежит СЗ)
export function mergeBlockTasks(tasks: IBlockTask[]): IBlockTask[] {
    const grouped = tasks.reduce((acc, task) => {
        const orderId = task.order.id

        if (!acc[orderId]) {

            // __ Если заказа еще нет в словаре, клонируем объект задачи
            // __ Используем структурированное клонирование, чтобы не мутировать исходный массив
            acc[orderId] = JSON.parse(JSON.stringify(task))

        } else {

            // __ Если заказ уже есть, объединяем его cutting_lines
            const targetTask = acc[orderId]

            task.block_lines.forEach((newLine) => {
                const existingLine = targetTask.block_lines.find(
                    (l) =>
                        // __ Ищем строку с тем же блоком и производственной линии
                        l.block.id === newLine.block.id &&
                        l.manuf_line === newLine.manuf_line,
                )

                if (existingLine) {
                    // __ Если такая строка заказа уже есть — суммируем количество
                    existingLine.amount += newLine.amount
                } else {
                    // __ Если такой строки еще нет — добавляем её целиком
                    targetTask.block_lines.push(JSON.parse(JSON.stringify(newLine)))
                }
            })
        }

        return acc
    }, {} as Record<number, IBlockTask>)

    // __Возвращаем массив, сохраняя порядок первого появления каждого order.id
    return Object.values(grouped)
}


// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли СЗ со столом Undefined
export function hasTaskUnknownManufLine(entity: IBlockTask | IBlockTaskLine[]) {

    let items: IBlockTaskLine[]
    if (isBlockTask(entity)) {
        items = entity.block_lines
    } else if (Array.isArray(entity)) {
        items = entity
    } else {
        throw new Error('hasTaskUnknownManufLine: unknown incoming data type')
    }

    for (let i = 0; i < items.length; i++) {
        if (items[i].manuf_line === BLOCK_MANUF_LINES.LINE_0) return true
    }

    return false
}

// --- ------------------------------------------------------------------------------------
// __ Проверяем, есть ли в массиве изменений хотя бы одна сущность для создания в БД
export function isAddItemsInDiffsPresents(diffs: IBlockTaskArrayDiff[]) {
    return diffs.some(taskDiff => {

        // __ 1. Проверяем саму задачу
        if (taskDiff.type === 'ADDED') return true

        // __ 2. Безопасно проверяем строки (используем опциональную цепочку ?. )
        // __ Проверяем, есть ли среди изменений строк хотя бы одно с типом 'ADDED'
        return taskDiff.lineChanges?.some(lineDiff => lineDiff.type === 'ADDED') ?? false
    })
}

// __ Получаем трудозатраты на один блок
export function getBlockTimePerPic(blockLine: IBlockTaskLine): number {
    return blockLine.block.collection.productivity !== 0
        ? blockLine.square / blockLine.block.collection.productivity
        : 0
}

/**
 * __ Функция, которая возвращает высчитанный объект количества при разделении строки на количество
 * __ Возвращает новый экземпляр с пересчитанными данными
 * @param blockLine    __Входная строка__
 * @param newAmount    __Новое количество__
 */
export function calculateDividedAmountAndTime(blockLine: IBlockTaskLine, newAmount: number): IBlockTaskLine {

    // __ Создаем копию строки (референсная)
    const refBlockLine = { ...blockLine }

    const timePerPic    = getBlockTimePerPic(blockLine)
    // const timePerPic    = blockLine.amount !== 0 ? blockLine.time / blockLine.amount : 0
    refBlockLine.time   = timePerPic * newAmount
    refBlockLine.amount = newAmount

    return refBlockLine
}

// __ Дополнительно проверяем, является ли модель расчетной
export function isAverage(element: IBlockTaskLine | string) {
    if (isBlockTaskLine(element)) return element.is_average
    return element.toLowerCase().includes('average')
}

// --- ------------------------------------------------------------------------------------
// __ Объединяем строки СЗ с принадлежностью к одной и той же строке Заявки
export function mergeBlockLines(lines: IBlockTaskLine[]): IBlockTaskLine[] {

    const grouped = lines.reduce((acc, line) => {

        // __ Создаем составной ключ: ID + Раскройный Стол
        const manufLine = line.manuf_line
        const groupKey  = `${line.id}_${manufLine}`

        if (!acc[groupKey]) {
            acc[groupKey] = JSON.parse(JSON.stringify(line))
        } else {

            // __ Складываем количество
            acc[groupKey].amount += line.amount

            // __ Складываем трудозатраты (time)
            // if (acc[groupKey].time && line.time) {
            //     for (const key in line.time) {
            //         acc[groupKey].time[key] = (acc[groupKey].time[key] || 0) + line.time[key];
            //     }
            // }
        }

        return acc
    }, {} as Record<string, IBlockTaskLine>)

    return Object.values(grouped)
}


// __ Получаем трудозатраты в текстовом представлении '05ч. 30м. 18с.'
// __ twoLines = true - Если больше часа, то выводим часы и минуты (обрезаем секунды)
export function getTimeString(cuttingLine: IBlockTaskLine, twoLines: boolean = false, timeType = 'hour') {
    const time = cuttingLine.time

    // __ Если больше часа, то выводим часы и минуты (обрезаем секунды)
    if (twoLines) {
        // __ Получаем время. Если больше часа, то выводим часы и минуты (обрезаем секунды)
        if (time >= 60 * 60) {
            const timeStrArr = formatTimeWithLeadingZeros(time, timeType).split(' ')
            if (timeStrArr[0] !== undefined && timeStrArr[1] !== undefined) {
                return timeStrArr[0] + ' ' + timeStrArr[1]
            } else {
                return formatTimeWithLeadingZeros(time, timeType)
            }
        }
    }

    return formatTimeWithLeadingZeros(time, timeType)
}


// __ Функция-помощник: говорит TS, является ли item типом IBlockTask
function isBlockTask(item: unknown): item is IBlockTask {
    return !!item && typeof item === 'object' && 'order' in item && 'block_lines' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IBlockTaskOrder
function isBlockTaskOrder(item: unknown): item is IBlockTaskOrder {
    return !!item && typeof item === 'object' && 'client' in item && 'order_type' in item
}


// __ Функция-помощник: говорит TS, является ли item типом IBlockaskStatus
function isBlockTaskStatus(item: unknown): item is IBlockTaskStatus {
    return !!item && typeof item === 'object' && 'id' in item && 'color' in item && 'name' in item && 'pivot' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IBlockTaskOrderLine
function isBlockTaskOrderLine(item: unknown): item is IBlockTaskOrderLine {
    return !!item && typeof item === 'object' && 'models' in item && 'dims' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IBlockTaskLine
function isBlockTaskLine(item: unknown): item is IBlockTaskLine {
    return !!item && typeof item === 'object' && 'order_lines' in item
}
