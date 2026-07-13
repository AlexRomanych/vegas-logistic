// noinspection DuplicatedCode


import type {
    IAmountAndTimeBlock, IBlockCollectionTime,
    IBlockDay,
    IBlockManufLine,
    IBlockTask,
    IBlockTaskArrayDiff,
    IBlockTaskArrayLineDiffs,
    IBlockTaskChangeKeys,
    IBlockTaskExecuteStatistics,
    IBlockTaskLine,
    IBlockTaskLinesGroupData,
    IBlockTaskLinesSubgroup, IBlockTaskOrder,
    IBlockTaskOrderLine,
    IBlockTaskStatus,
    IBlockTaskStatusKeys, IColorTypes,
    IDay, IOptimizeType,
    IPlanMatrix,
    IRenderMatrixDiff,
    IRenderMatrixLineDiffs
} from '@/types'
import {
    BLOCK_MANUF_LINES,
    BLOCK_TASK_DRAFT,
    BLOCK_TASK_STATUSES,
    CHANGES, CHANGE_1, CHANGE_2, TUNING_TIME_LINES_SUBGROUP_DRAFT, OPTIMIZE_BY_PRIORITY, OPTIMIZE_BY_TUNING_TIME,
    // LINE_0_NAME, LINE_1_NAME, LINE_2_NAME
} from '@/app/constants/blocks.ts'
// import { round } from '@/app/helpers/helpers_lib.ts'
import { formatTimeWithLeadingZeros, splitDate } from '@/app/helpers/helpers_date'


// __ Проблема с draggable
// __ Если день пустой, то перетаскивание не срабатывает
// __ Поэтому добавляем пустое задание в пустой день
export function correctRenderMatrix(matrix: IPlanMatrix) {
    let draftId = -100
    matrix.forEach((week, weekIndex) => {

        week.forEach((day, dayIndex) => {
            day.forEach((change, changeIndex) => {


                const filteredDay = change.filter((item: IBlockTask) => item.id > -1)      // __ id === 0 (для добавленного СЗ)
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
                matrix[weekIndex][dayIndex][changeIndex] = filteredDay
                // matrix[weekIndex][dayIndex] = {...filteredDay, fullDay: true}
            })
        })
    })

    return matrix
}


// __ Сортируем задания в матрице рендера по позиции + сортируем строки по позиции
export function sortRenderMatrixByTaskPosition(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            day.forEach((change, changeIndex) => {

                matrix[weekIndex][dayIndex][changeIndex] = change.sort((a: IBlockTask, b: IBlockTask) => a.position - b.position)
                matrix[weekIndex][dayIndex][changeIndex].forEach((blockTask: IBlockTask) => {
                    blockTask.block_lines = blockTask.block_lines.sort((a: IBlockTaskLine, b: IBlockTaskLine) => a.position - b.position)
                })
            })
        })
    })
    return matrix
}


// __ Очищаем матрицу рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrix(matrix: IPlanMatrix) {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {
            day.forEach((change, changeIndex) => {
                matrix[weekIndex][dayIndex][changeIndex] = change.filter((item: IBlockTask) => item.id > -1) // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
            })
        })
    })
    return matrix
}

// __ Очищаем день матрицы рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrixDay<T extends IDay>(day: T[]): T[] {
    const change_1 = day[0]?.filter((item: IBlockTask) => item.id > -1) || []
    const change_2 = day[1]?.filter((item: IBlockTask) => item.id > -1) || []

    return [change_1, change_2] // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
    // return [...day.filter(item => item.id > -1)] // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
}

// __ Очищаем смену дня матрицы рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrixDayChange<T extends IDay>(day: T[], change: IBlockTaskChangeKeys): T[] {
    const idx     = getIndexByChange(change)
    const cleared = day[idx]?.filter((item: IBlockTask) => item.id > -1) || []
    switch (change) {
        case CHANGE_1:
            return [cleared, day[1]]
        case CHANGE_2:
            return [day[0], cleared]
    }
}


// __ Возвращаем индекс по смене
export function getIndexByChange(change: IBlockTaskChangeKeys): number {
    switch (change) {
        case CHANGE_1:
            return 0
        case CHANGE_2:
            return 1
        default: {
            throw new Error('Undefined Block Task Change')
        }
    }
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
                    change   : { old: null, new: task.change },
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
        const hasChangeChanged   = task.change !== original.change

        // __ Сравниваем строки пошива (детально)
        const lineDiffs = getTaskLinesDiff(task.block_lines, original.block_lines)

        // __ Если есть изменения хотя бы в одном месте
        if (hasDateChanged || hasPositionChanged || lineDiffs.length > 0 || hasStatusChanged || hasChangeChanged) {
            diffs.push({
                taskId: task.id,
                type  : 'UPDATED',

                // __ Поля задачи
                taskChanges: {
                    action_at: hasDateChanged ? { old: original.action_at, new: task.action_at } : null,
                    position : hasPositionChanged ? { old: original.position, new: task.position } : null,
                    status   : hasStatusChanged ? { old: original.current_status.id, new: task.current_status.id } : null,
                    change   : hasChangeChanged ? { old: original.change, new: task.change } : null,
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
        week.forEach((day, dayIdx) => {
            const dayOffset = weekIdx * 7 + dayIdx
            day.forEach((change, changeIdx) => {
                change.forEach((task: IBlockTask) => {
                    // __ Сохраняем "слепок" состояния для сравнения
                    copyMap.set(task.id, {
                        dayOffset,
                        position : task.position,
                        change   : task.change,
                        changeIdx: changeIdx,
                        lines    : JSON.parse(JSON.stringify(task.block_lines)), // __ глубокая копия строк
                    })
                })
            })
        })
    })

    // __ 2. Сравниваем с текущим состоянием
    currentMatrix.forEach((week, weekIdx) => {
        week.forEach((day, dayIdx) => {
            const currentDayOffset = weekIdx * 7 + dayIdx

            day.forEach((change, changeIdx) => {
                change.forEach((task: IBlockTask) => {
                    const old = copyMap.get(task.id)

                    if (!old) {

                        // __ Обработка совершенно новой задачи (если такое возможно)
                        diffs.push({ type: 'NEW_TASK', taskId: task.id, newPosition: task.position, newChange: task.change })
                        return
                    }

                    const isMoved         = old.dayOffset !== currentDayOffset
                    const isPosChanged    = old.position !== task.position
                    const isChangeChanged = old.changeIdx !== changeIdx
                    // const isChangeChanged = old.change !== task.change

                    // __ Проверяем детальные изменения в строках (block_lines)
                    const lineDiffs = getLinesDetailedDiff(old.lines, task.block_lines)

                    // __ Если хоть что-то изменилось — фиксируем
                    if (isMoved || isPosChanged || isChangeChanged || lineDiffs.length > 0) {
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

                            // __ Информация по смене (Change)
                            oldChange      : old.change,
                            newChange      : task.change,
                            isChangeChanged: isChangeChanged,

                            // __ Детализация по строкам
                            lineDiffs: lineDiffs,
                        })
                    }
                })
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
    date: string | null   = null,
    change: string | null = null,
    applyStatus: boolean  = false) {

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
            task.change === change &&
            task.order.id === item &&
            task.current_status.id === entity.current_status.id
        )
    }

    return tasksList.filter(task => task.action_at === date && task.order.id === item && task.change === change)
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
export const orderBlockTasksByStatus = (day: IDay): IDay => {

    // __ Собираем все задачи за день в один плоский массив и проставляем им смены
    const allDayTasks: IBlockTask[] = []

    day.forEach((change: IBlockTask[], changeIndex: number) => {
        const currentChange = changeIndex === 0 ? CHANGE_1 : CHANGE_2

        change.forEach((task: IBlockTask) => {
            allDayTasks.push({
                ...(task as IBlockTask),
                change: currentChange // Мутируем или создаем копию в зависимости от архитектуры
            })
        })
    })

    // __ Сортируем задачи дня по вашему бизнес-правилу:
    // __ Сначала Смена 1, затем Смена 2. Внутри смены — по PRIORITY из справочника.
    allDayTasks.sort((a, b) => {
        if (a.change !== b.change) {
            return a.change === CHANGE_1 ? -1 : 1
        }

        // __ Сортируем по убыванию
        return getTaskPriority(b) - getTaskPriority(a)
    })

    // __ Проставляем сквозную позицию (index + 1)
    const processedTasks = allDayTasks.map((task, index) => ({
        ...task,
        position: index + 1
    }))

    // __ Распределяем обратно по сменам, сохраняя сортировку по position
    // __ Так как массив уже отсортирован, filter вернет элементы в правильном порядке.
    const changeTasks1 = processedTasks.filter(task => task.change === CHANGE_1)
    const changeTasks2 = processedTasks.filter(task => task.change === CHANGE_2)

    // __ Записываем обратно в матрицу
    const resultDay = JSON.parse(JSON.stringify(day))
    resultDay[0]    = changeTasks1
    resultDay[1]    = changeTasks2

    return resultDay

    // __ Старый код
    // // 1. Описываем веса для статусов.
    // // Чем меньше значение, тем выше (раньше) элемент в массиве.
    // const statusPriority: Record<number, number> = {
    //     [BLOCK_TASK_STATUSES.DONE.ID]   : 1,
    //     [BLOCK_TASK_STATUSES.RUNNING.ID]: 2,
    //     [BLOCK_TASK_STATUSES.PENDING.ID]: 3,
    //     [BLOCK_TASK_STATUSES.ROLLING.ID]: 4,
    //     [BLOCK_TASK_STATUSES.CREATED.ID]: 5,
    // }
    //
    // // Создаем копию массива, чтобы не мутировать оригинал (важно для Vue)
    // const result = [...tasks]
    //     .sort((a, b) => {
    //         const weightA = statusPriority[a.current_status.id] ?? 999
    //         const weightB = statusPriority[b.current_status.id] ?? 999
    //
    //         // Сначала сравниваем по весу статуса
    //         if (weightA !== weightB) {
    //             return weightA - weightB
    //         }
    //
    //         // Если статусы одинаковые, сравниваем по дате (action_at)
    //         // Преобразуем строки даты в числа (timestamp) для вычитания
    //         const dateA = new Date(a.action_at).getTime()
    //         const dateB = new Date(b.action_at).getTime()
    //
    //         return dateA - dateB
    //     })
    //
    // result.forEach((_, index, array) => {
    //     array[index].position = index + 1
    // })
    //
    // return result
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


// __ Ищем Приориет Статусов движения Заявки
export function getTaskPriority(task: IBlockTask): number {
    const statusKeyId = task.current_status.id

    // __ Ищем подходящий статус в вашем справочнике BLOCK_TASK_STATUSES
    const statusConfig = Object.values(BLOCK_TASK_STATUSES).find(
        s => s.ID === statusKeyId
    )

    return statusConfig ? statusConfig.PRIORITY : 999 // 999 для неизвестных статусов
}


// __ Пересчитываем позиции СЗ в матрице рендера после перетаскивания мышью
export function setTaskPositionInRenderMatrix(matrix: IPlanMatrix): IPlanMatrix {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {

            // __ Собираем все задачи за день в один плоский массив и проставляем им смены
            const allDayTasks: IBlockTask[] = []

            day.forEach((change, changeIndex) => {
                const currentChange = changeIndex === 0 ? CHANGE_1 : CHANGE_2

                change.forEach((task: IBlockTask) => {
                    allDayTasks.push({
                        ...(task as IBlockTask),
                        change: currentChange // Мутируем или создаем копию в зависимости от архитектуры
                    })
                })
            })

            // __ Сортируем задачи дня по вашему бизнес-правилу:
            // __ Сначала Смена 1, затем Смена 2. Внутри смены — по PRIORITY из справочника.
            allDayTasks.sort((a, b) => {
                if (a.change !== b.change) {
                    return a.change === CHANGE_1 ? -1 : 1
                }

                // __ Сортируем по убыванию
                return getTaskPriority(b) - getTaskPriority(a)
            })

            // __ Проставляем сквозную позицию (index + 1)
            const processedTasks = allDayTasks.map((task, index) => ({
                ...task,
                position: index + 1
            }))

            // __ Распределяем обратно по сменам, сохраняя сортировку по position
            // __ Так как массив уже отсортирован, filter вернет элементы в правильном порядке.
            const changeTasks1 = processedTasks.filter(task => task.change === CHANGE_1)
            const changeTasks2 = processedTasks.filter(task => task.change === CHANGE_2)

            // __ Записываем обратно в матрицу
            //@ts-expect-error Recently missing
            matrix[weekIndex][dayIndex][0] = changeTasks1
            //@ts-expect-error Recently missing
            matrix[weekIndex][dayIndex][1] = changeTasks2
        })
    })

    return matrix
}


export function setTaskPositionInRenderMatrix_old_1(matrix: IPlanMatrix) {

    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {

            // __ Массивы для Первой смены ('1')
            const change1NonCreated: IBlockTask[] = []
            const change1Created: IBlockTask[]    = []

            // __ Массивы для Второй смены ('2')
            const change2NonCreated: IBlockTask[] = []
            const change2Created: IBlockTask[]    = []

            day.forEach((change, changeIndex) => {

                const compareChange = changeIndex === 0 ? CHANGE_1 : CHANGE_2

                // __ 1. Распределяем задачи по сменам и статусам за один проход
                for (let i = 0; i < change.length; i++) {

                    const task      = change[i] as IBlockTask
                    const isCreated = isTaskStatusCreated(task)

                    if (compareChange === CHANGE_1) {
                        task.change = CHANGE_1
                        if (isCreated) {
                            change1Created.push(task)
                        } else {
                            change1NonCreated.push(task)
                        }
                    } else if (compareChange === CHANGE_2) {
                        task.change = CHANGE_2
                        if (isCreated) {
                            change2Created.push(task)
                        } else {
                            change2NonCreated.push(task)
                        }
                    } else {
                        task.change = CHANGE_1
                        if (isCreated) {
                            change1Created.push(task)
                        } else {
                            change1NonCreated.push(task)
                        }
                    }


                    // if (task.change === CHANGE_1) {
                    //     if (isCreated) {
                    //         change1Created.push(task)
                    //     } else {
                    //         change1NonCreated.push(task)
                    //     }
                    // } else if (task.change === CHANGE_2) {
                    //     if (isCreated) {
                    //         change2Created.push(task)
                    //     } else {
                    //         change2NonCreated.push(task)
                    //     }
                    // } else {
                    //     // На случай, если change пустой или имеет другое значение,
                    //     // пушим в первую смену или обрабатываем отдельно
                    //     if (isCreated) {
                    //         change1Created.push(task)
                    //     } else {
                    //         change1NonCreated.push(task)
                    //     }
                    // }
                }

            })

            // __ 2. Собираем результирующий день в строгом порядке:
            // Смена 1 (В работе -> Создано) ПОТОМ Смена 2 (В работе -> Создано)
            let resultDay: IBlockTask[] = [
                ...change1NonCreated,
                ...change1Created,
                ...change2NonCreated,
                ...change2Created
            ]

            // __ 3. Пересчитываем сквозные позиции (index + 1) для всего дня
            resultDay = resultDay.map((item, index) => ({
                ...item,
                position: index + 1
            }))


            // __ 4. Разбиваем на смены
            const changeTasks1 = resultDay.filter(task => task.change === CHANGE_1).toSorted((a, b) => a.position - b.position)
            const changeTasks2 = resultDay.filter(task => task.change === CHANGE_2).toSorted((a, b) => a.position - b.position)

            //@ts-expect-error Recently missing
            matrix[weekIndex][dayIndex][0] = changeTasks1
            //@ts-expect-error Recently missing
            matrix[weekIndex][dayIndex][1] = changeTasks2
        })
    })

    return matrix
}

// export function setTaskPositionInRenderMatrix_Old(matrix: IPlanMatrix) {
//     matrix.forEach((week, weekIndex) => {
//         week.forEach((day, dayIndex) => {
//
//             // __ Новая сортировка по позиции (СЗ статусы, которые не равны "Создано" - остаются на своих местах)
//             const nonCreatedStatusTasks: IBlockTask[] = []
//             const createdStatusTasks: IBlockTask[]    = []
//             const resultDay: IBlockTask[]             = []
//
//             // __ Фильтруем по статусу
//             for (let i = 0; i < day.length; i++) {
//                 if (isTaskStatusCreated(change[i] as IBlockTask)) {
//                     createdStatusTasks.push(change[i] as IBlockTask)
//                 } else {
//                     nonCreatedStatusTasks.push(change[i] as IBlockTask)
//                 }
//             }
//
//             nonCreatedStatusTasks.forEach(task => resultDay.push(task))
//             createdStatusTasks.forEach(task => resultDay.push(task))
//
//             // console.log('nonCreatedStatusTasks: ', nonCreatedStatusTasks)
//             // console.log('createdStatusTasks: ', createdStatusTasks)
//             // console.log('resultDay: ', resultDay)
//             // matrix[weekIndex][dayIndex] = resultDay
//
//             matrix[weekIndex][dayIndex] = resultDay
//                 .map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля
//             // .sort((a, b) => a.position - b.position)
//             // __ Старая сортировка по позиции без учета всплытия статусов - без первой части
//             // matrix[weekIndex][dayIndex] = day.map((item, index) => ({ ...item, position: index + 1 })) // __ id пустых заданий меньше нуля
//         })
//         })
//     })
//     return matrix
// }

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

            // __ Если заказ уже есть, объединяем его block_lines
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
    const map = new Map<string, IBlockTaskLine>()

    for (const line of lines) {
        // 1. Извлекаем ID или признак заявки.
        const manufLineId = line.manuf_line || BLOCK_MANUF_LINES.LINE_0
        const blockCode1c = line.block?.code_1c || 'no-code'

        // 2. Создаем уникальный составной ключ для группировки
        const key = `${manufLineId}_${blockCode1c}`

        if (map.has(key)) {
            // Элемент с таким ключом уже есть — объединяем данные
            const existing = map.get(key)!

            // Суммируем числовые показатели
            existing.amount += line.amount
            existing.time += line.time
            existing.square += line.square

            // Если нужно усреднить продуктивность, а не складывать:
            // existing.productivity = Math.max(existing.productivity, line.productivity);

            // Объединяем массивы связей (исключая дубликаты по ID, если необходимо)
            existing.order_lines    = [...existing.order_lines, ...line.order_lines]
            existing.order_line_ids = [...existing.order_line_ids, ...line.order_line_ids]

            // Опционально: если у строк разные position, можно оставить минимальный
            // existing.position = Math.min(existing.position, line.position);

        } else {
            // Элемента с таким ключом еще нет — клонируем текущий, чтобы не мутировать исходный массив
            map.set(key, JSON.parse(JSON.stringify(line)))
        }
    }

    // Возвращаем сгруппированный массив
    return Array.from(map.values())
}


// export function mergeBlockLines(lines: IBlockTaskLine[]): IBlockTaskLine[] {
//
//     const grouped = lines.reduce((acc, line) => {
//
//         // __ Создаем составной ключ: ID + Раскройный Стол
//         const manufLine = line.manuf_line
//         const groupKey  = `${line.id}_${manufLine}`
//
//         if (!acc[groupKey]) {
//             acc[groupKey] = JSON.parse(JSON.stringify(line))
//         } else {
//
//             // __ Складываем количество
//             acc[groupKey].amount += line.amount
//
//             // __ Складываем трудозатраты (time)
//             // if (acc[groupKey].time && line.time) {
//             //     for (const key in line.time) {
//             //         acc[groupKey].time[key] = (acc[groupKey].time[key] || 0) + line.time[key];
//             //     }
//             // }
//         }
//
//         return acc
//     }, {} as Record<string, IBlockTaskLine>)
//
//     return Object.values(grouped)
// }


// __ Получаем трудозатраты в текстовом представлении '05ч. 30м. 18с.'
// __ twoLines = true - Если больше часа, то выводим часы и минуты (обрезаем секунды)
export function getTimeString(blockLine: IBlockTaskLine, twoLines: boolean = false, timeType = 'hour') {
    const time = blockLine.time

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

// __ Возвращает Название Заявки
export function getOrderTitle(task: IBlockTask) {
    return `${task.order.client.short_name} №${task.order.order_no_str}`
}


// __ Возвращает Смену по имени
export function getChangeByName(task: IBlockTask | IBlockTaskChangeKeys) {
    let compareKey = null
    if (isBlockTask(task)) {
        compareKey = task.change
    } else {
        compareKey = task
    }

    const changeKey = Object.keys(CHANGES).find(key => CHANGES[key as keyof typeof CHANGES].NAME === compareKey)
    return changeKey ? CHANGES[changeKey as keyof typeof CHANGES] : null
}


// --- -------------------------------------------------------------------------------------
// __ Получаем статус СЗ по его ID
export function getTaskStatusById(id: number) {
    const statusKey = Object.keys(BLOCK_TASK_STATUSES).find(key => BLOCK_TASK_STATUSES[key as IBlockTaskStatusKeys].ID === id)
    if (statusKey) {
        return BLOCK_TASK_STATUSES[statusKey as IBlockTaskStatusKeys]
    }
    return null
}


// --- -------------------------------------------------------------------------------------
// __ Получаем статистику по выполнению СЗ
export function getExecuteTaskStatistics(item: IBlockTask | IBlockTaskLine[]) {

    const statistics: IBlockTaskExecuteStatistics = {
        amount: {
            finished  : 0,
            unfinished: 0,
            total     : 0,
        },
        time  : {
            finished  : 0,
            unfinished: 0,
            total     : 0,
        },
    }

    // __ Проверяем, что пришло на вход
    let itemArr = []
    if (Array.isArray(item)) {
        itemArr = item
    } else {
        itemArr = item.block_lines
    }

    // __ Получаем суммарное количество и трудозатраты
    const totalAmountAndTimeObj = getBlockTaskAmountAndTime(itemArr)

    // __ Общее Количество
    statistics.amount.total = Object.values(totalAmountAndTimeObj).reduce((acc, item) => item.amount + acc, 0)

    // __ Общее Трудозатраты
    statistics.time.total = Object.values(totalAmountAndTimeObj).reduce((acc, item) => item.time + acc, 0)

    // __ Выполненные
    const finished = itemArr.filter(line => line.finished_at)

    // __ Получаем суммарное количество и трудозатраты для выполненных
    const finishedAmountAndTimeObj = getBlockTaskAmountAndTime(finished)

    // __ Выполненные Количество
    statistics.amount.finished = Object.values(finishedAmountAndTimeObj).reduce((acc, item) => item.amount + acc, 0)

    // __ Выполненные Трудозатраты
    statistics.time.finished = Object.values(finishedAmountAndTimeObj).reduce((acc, item) => item.time + acc, 0)

    // __ Не Выполненные
    const unfinished = itemArr.filter(line => !line.finished_at)

    // __ Получаем суммарное количество и трудозатраты для Не выполненных
    const unfinishedAmountAndTimeObj = getBlockTaskAmountAndTime(unfinished)

    // __ Не Выполненные Количество
    statistics.amount.unfinished = Object.values(unfinishedAmountAndTimeObj).reduce((acc, item) => item.amount + acc, 0)

    // __ Не Выполненные Трудозатраты
    statistics.time.unfinished = Object.values(unfinishedAmountAndTimeObj).reduce((acc, item) => item.time + acc, 0)

    return statistics
}


// --- -------------------------------------------------------------------------------------
// __ Получаем массив дней дат, на которые есть СЗ
export function getBlockDates(tasks: IBlockTask[]) {
    if (tasks.length > 0) {
        const days = tasks.map(item => item.action_at.split(' ')[0])
        return [...new Set(days)]
    } else {
        return []
    }
}

// --- -------------------------------------------------------------------------------------
// __ Получаем на вход массив СЗ и массив дат и делаем из них массив дат
// __ с добавлением туда массива СЗ на соответствующую дату
// __ добавляем по ссылке + заодно и сортируем
export function unionDatesWithBlockTasks(days: IBlockDay[], tasks: IBlockTask[]) {
    days.sort((a, b) => {
        const timeA = new Date(a.action_at).getTime()
        const timeB = new Date(b.action_at).getTime()

        if (timeA !== timeB) {
            return timeA - timeB
        }

        return a.change.localeCompare(b.change)
    })

    tasks.sort((a, b) => new Date(a.action_at).getTime() - new Date(b.action_at).getTime())

    tasks.forEach(task => {
        task.block_lines.sort((a, b) => a.position - b.position)
    })

    for (const day of days) {
        day.block_tasks = []
        for (const task of tasks) {
            if (splitDate(task.action_at) === splitDate(day.action_at) && task.change === day.change) {
                day.block_tasks.push(task)
            }
        }

        day.block_tasks.sort((a, b) => a.position - b.position)
    }
}

// __ Получаем заголовок СЗ
export function getBlockTaskTitle(task: IBlockTask, includePosition: boolean = true) {
    if (includePosition) {
        return `${task.position}. ${task.order.client.short_name} №${task.order.order_no_num}`
    }
    return `${task.order.client.short_name} №${task.order.order_no_num}`
}

// __ Возвращаем числовое значение времени по Записи в СЗ (blockLine) для конкретных матрасов (не расчетных),
export function getBlockTaskLineTime(line: IBlockTaskLine): number {
    return line.time
}

// __ Возвращаем числовое значение Площади по Записи в СЗ (blockLine) для конкретных матрасов (не расчетных),
export function getBlockTaskLineSquare(line: IBlockTaskLine): number {
    return line.amount * line.block.width * line.block.length / 100 / 100
}

// __ Возвращаем подготовленный массив групп для отображения в выполнении СЗ
export function groupTaskLinesForExecute(
    lines: IBlockTaskLine[],
    orderTitle: string | null           = null,
    tuningTimes: IBlockCollectionTime[] = [],
    optimizationType: IOptimizeType     = OPTIMIZE_BY_PRIORITY,
    optimizedData: number[]             = []        // Массив оптимизации, полученный с сервера
): IBlockTaskLinesGroupData[] {

    // const X_LAT = 'x'

    // console.log('tuningTimes: ', tuningTimes)
    const getSquarePerPic = (blockTaskLine: IBlockTaskLine) => blockTaskLine.block.width * blockTaskLine.block.length / 100 / 100
    const getCollectionId = (collectionName: string) => {
        const findCollection = tuningTimes.find(collection => collection.name === collectionName)
        return findCollection ? findCollection.id : 0
    }

    const groupedManufLines = Object.groupBy(lines, line => line.manuf_line)

    // console.log('groupedManufLines: ', groupedManufLines)

    const groupedManufLinesArray: IBlockTaskLinesGroupData[] = []
    for (const [keyManufLine, valueManufLine] of Object.entries(groupedManufLines)) {

        const groupedBlockCollection = Object.groupBy(valueManufLine, line => line.block.collection.name)

        let groupedBlockCollectionArray: IBlockTaskLinesSubgroup[] = []
        for (const [keyBlockCollection, valueBlockCollection] of Object.entries(groupedBlockCollection)) {

            groupedBlockCollectionArray.push({
                subgroupName      : keyBlockCollection,
                subgroupId        : getCollectionId(keyBlockCollection),
                subgroupOrderTitle: orderTitle,
                collapsed         : true,
                subgroupType      : 'dark',
                hasData           : true,
                lines             : (valueBlockCollection ?? []).toSorted((a, b) => getSquarePerPic(b) - getSquarePerPic(a)),
                square            : {
                    total     : (valueBlockCollection || []).reduce((acc, line) => acc + line.amount * getSquarePerPic(line), 0),
                    done      : (valueBlockCollection || []).reduce((acc, line) => isTaskLineDone(line) ? acc + line.amount * getSquarePerPic(line) : acc, 0),
                    incomplete: (valueBlockCollection || []).reduce((acc, line) => !isTaskLineDone(line) ? acc + line.amount * getSquarePerPic(line) : acc, 0),
                },
                amount            : {
                    total     : (valueBlockCollection || []).reduce((acc, line) => acc + line.amount, 0),
                    done      : (valueBlockCollection || []).reduce((acc, line) => isTaskLineDone(line) ? acc + line.amount : acc, 0),
                    incomplete: (valueBlockCollection || []).reduce((acc, line) => !isTaskLineDone(line) ? acc + line.amount : acc, 0),
                },
                time              : {
                    total     : (valueBlockCollection || []).reduce((acc, line) => acc + getBlockTaskLineTime(line), 0),
                    done      : (valueBlockCollection || []).reduce((acc, line) => isTaskLineDone(line) ? acc + getBlockTaskLineTime(line) : acc, 0),
                    incomplete: (valueBlockCollection || []).reduce((acc, line) => !isTaskLineDone(line) ? acc + getBlockTaskLineTime(line) : acc, 0),
                },
                priority          : keyManufLine === BLOCK_MANUF_LINES.LINE_1
                    ? (valueBlockCollection?.[0].block.collection.priority_1 || 999)
                    : (valueBlockCollection?.[0].block.collection.priority_2 || 999),
                isTuning          : false
            })
        }

        // !!!!!!!!! --- Тут Логика Сортировки с добавлением Времени Переналадки
        // !!!!!!!!! --- Сначала сортировка, потом добавление Переналадки

        // __ Сортируем по Коллекцию блоков:
        if (optimizationType === OPTIMIZE_BY_PRIORITY) {
            groupedBlockCollectionArray.sort((a, b) => a.priority - b.priority) // __ по приоритету
        } else if (optimizationType === OPTIMIZE_BY_TUNING_TIME) {
            // console.log(optimizedData)

            // 1. Создаем карту, где ключ — id, а значение — его позиция
            const idPositionMap = new Map(optimizedData.map((id, index) => [id, index]))

            // 2. Сортируем с поиском по карте за O(1)
            groupedBlockCollectionArray.sort((a, b) => {
                const posA = idPositionMap.get(a.subgroupId) ?? Infinity
                const posB = idPositionMap.get(b.subgroupId) ?? Infinity
                return posA - posB
            })

        } else {
            groupedBlockCollectionArray.sort((a, b) => a.subgroupName.localeCompare(b.subgroupName)) // __ по алфавиту
        }

        // __ Добавляем Время переналадки, если оно передано
        if (tuningTimes.length > 0) {
            const groupedBlockCollectionArrayTimes = []
            for (let i = 0; i < groupedBlockCollectionArray.length; i++) {

                groupedBlockCollectionArrayTimes.push(groupedBlockCollectionArray[i])

                // __ Пропускаем последний элемент
                if (i !== groupedBlockCollectionArray.length - 1) {

                    const tuningGroup = JSON.parse(JSON.stringify(TUNING_TIME_LINES_SUBGROUP_DRAFT))

                    const findCollectionFrom = tuningTimes.find(collection => collection.name === groupedBlockCollectionArray[i].subgroupName)
                    if (findCollectionFrom) {

                        const findCollectionTo = findCollectionFrom.collections_to?.find(collection => collection.name === groupedBlockCollectionArray[i + 1].subgroupName)
                        if (findCollectionTo) {

                            // __ Не добавляем Нулевое время
                            if (findCollectionTo.tuning_time !== 0) {
                                tuningGroup.subgroupName =
                                    `${groupedBlockCollectionArray[i].subgroupName} --> ${groupedBlockCollectionArray[i + 1].subgroupName}`
                                tuningGroup.time.total   = findCollectionTo.tuning_time
                            }

                        } else {

                            tuningGroup.subgroupName =
                                `${groupedBlockCollectionArray[i].subgroupName} --> ${groupedBlockCollectionArray[i + 1].subgroupName} ??`
                            tuningGroup.subgroupType = 'danger'
                        }
                    } else {

                        tuningGroup.subgroupName = `Данные ${groupedBlockCollectionArray[i].subgroupName} не найдены`
                        tuningGroup.subgroupType = 'danger'
                    }
                    groupedBlockCollectionArrayTimes.push(tuningGroup)
                }

            }

            groupedBlockCollectionArray = groupedBlockCollectionArrayTimes
        }

        // console.log('groupedBlockCollectionArrayTimes: ', groupedBlockCollectionArrayTimes)
        // --- ----------


        // __ И Получаем название и раскраску Линии
        const manufName: IBlockManufLine = keyManufLine as IBlockManufLine
        // let manufName: IBlockTaskLinesGroupNames = LINE_0_NAME
        let manufType: IColorTypes       = 'danger'
        switch (keyManufLine) {
            case BLOCK_MANUF_LINES.LINE_1:
                // manufName = LINE_1_NAME
                manufType = 'orange'
                break
            case BLOCK_MANUF_LINES.LINE_2:
                // manufName = LINE_2_NAME
                manufType = 'indigo'
                break
        }

        groupedManufLinesArray.push({
            groupName: manufName,
            groupType: manufType,
            subgroups: groupedBlockCollectionArray,
            // subgroups      : groupedBlockCollectionArrayTimes,
            hasData        : true,
            collapsed      : true,
            square         : {
                total     : groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.square.total, 0),
                done      : groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.square.done, 0),
                incomplete: groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.square.incomplete, 0),
            },
            amount         : {
                total     : groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.amount.total, 0),
                done      : groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.amount.done, 0),
                incomplete: groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.amount.incomplete, 0),
            },
            time           : {
                total     : groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.time.total, 0),
                done      : groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.time.done, 0),
                incomplete: groupedBlockCollectionArray.reduce((acc, subgroup) => acc + subgroup.time.incomplete, 0),
            },
            tuningTimeTotal: groupedBlockCollectionArray.reduce((acc, subgroup) => subgroup.isTuning ? acc + subgroup.time.total : acc, 0),
            // tuningTimeTotal: groupedBlockCollectionArrayTimes.reduce((acc, subgroup) => subgroup.isTuning ? acc + subgroup.time.total : acc, 0),
        })
    }


    // __ Сортируем по названию Производственных Линий по Возрастанию
    groupedManufLinesArray.sort((a, b) => a.groupName.localeCompare(b.groupName))
    // console.log('groupedManufLinesArray: ', groupedManufLinesArray)
    return groupedManufLinesArray
}


// __ Получаем КДЧ
// export function getKDB(item: IBlockTaskLine): string {
//     return ''
// }


// __ Проверяем, является ли строка СЗ Выполненной
export function isTaskLineDone(line: IBlockTaskLine) {
    return !!line.finished_at
}

// __ Проверяем, является ли строка СЗ Не Выполненной
export function isTaskLineFalse(line: IBlockTaskLine) {
    return !!line.false_at
}

// __ Проверяем, является ли строка СЗ со сброшенным статусом
export function isTaskLineReset(line: IBlockTaskLine) {
    return !(isTaskLineDone(line) || isTaskLineFalse(line))
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
