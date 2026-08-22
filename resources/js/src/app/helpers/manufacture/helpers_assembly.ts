import type {
    IAmountAndTimeAssembly,
    IAssemblySectorKeys,
    IAssemblyTask,
    IAssemblyTaskArrayDiff,
    IAssemblyTaskArrayLineDiffs,
    IAssemblyTaskChangeKeys,
    IAssemblyTaskLine,
    IAssemblyTaskLineSector,
    IAssemblyTaskStatusKeys,
    IDay,
    IPlanMatrix,
    IRenderMatrixDiff,
    IRenderMatrixLineDiffs,
    IRenderOrderLineAssemblyLineSector,
    IStatItemAssembly,
    IAssemblyTaskStatus,
    IAssemblyTaskOrder,
    IAssemblyLineKeys,
    IAmountAndTimeAssemblyLines,
    IAssemblyTaskOrderLine,
    IPeriod,
    IAssemblyManipulateDay,
    IStats,
    IAssemblyModelManufactureGroup,
    IMatrixManufactureGroup, IMatrixManufactureGroupLine, IMatrixManufactureTask,
} from '@/types'
import {
    ASSEMBLY_LINE_UNDEFINED,
    ASSEMBLY_LINES,
    ASSEMBLY_SECTORS,
    ASSEMBLY_TASK_DRAFT,
    ASSEMBLY_TASK_SECTOR_LAMIT, ASSEMBLY_TASK_SECTOR_TABLE,
    ASSEMBLY_TASK_STATUSES,
    CHANGES
} from '@/app/constants/assembly.ts'
import { CHANGE_1, CHANGE_2 } from '@/app/constants/assembly.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'
import { getColorByPercent } from '@/app/helpers/helpers.ts'
import { round } from '@/app/helpers/helpers_lib.ts'


// __ Проблема с draggable
// __ Если день пустой, то перетаскивание не срабатывает
// __ Поэтому добавляем пустое задание в пустой день
export function correctRenderMatrix(matrix: IPlanMatrix) {
    let draftId = -100
    matrix.forEach((week, weekIndex) => {

        week.forEach((day, dayIndex) => {
            day.forEach((change, changeIndex) => {


                const filteredDay = change.filter((item: IAssemblyTask) => item.id > -1)      // __ id === 0 (для добавленного СЗ)
                // let filteredDay = day.filter(item => item.id !== ASSEMBLY_TASK_DRAFT.id)
                if (filteredDay.length === 0) {
                    const draft = {
                        ...ASSEMBLY_TASK_DRAFT,
                        id            : draftId--,
                        position      : 100,
                        assembly_lines: [],  /* !!! Тут пустой массив, потому что где-то по ссылке сохраняется  */
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

                matrix[weekIndex][dayIndex][changeIndex] = change.sort((a: IAssemblyTask, b: IAssemblyTask) => a.position - b.position)
                matrix[weekIndex][dayIndex][changeIndex].forEach((assemblyTask: IAssemblyTask) => {
                    assemblyTask.assembly_lines = assemblyTask.assembly_lines.sort((a: IAssemblyTaskLine, b: IAssemblyTaskLine) => a.position - b.position)
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
                matrix[weekIndex][dayIndex][changeIndex] = change.filter((item: IAssemblyTask) => item.id > -1) // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
            })
        })
    })
    return matrix
}

// __ Очищаем день матрицы рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrixDay<T extends IDay>(day: T[]): T[] {
    const change_1 = day[0]?.filter((item: IAssemblyTask) => item.id > -1) || []
    const change_2 = day[1]?.filter((item: IAssemblyTask) => item.id > -1) || []

    return [change_1, change_2] // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
    // return [...day.filter(item => item.id > -1)] // __ id пустых заданий меньше нуля + id = 0 (для добавленного СЗ)
}

// __ Очищаем смену дня матрицы рендера от пустых сменных заданий, которые добавляем для рендеринга
export function clearRenderMatrixDayChange<T extends IDay>(day: T[], change: IAssemblyTaskChangeKeys): T[] {
    const idx     = getIndexByChange(change)
    const cleared = day[idx]?.filter((item: IAssemblyTask) => item.id > -1) || []
    switch (change) {
        case CHANGE_1:
            return [cleared, day[1]]
        case CHANGE_2:
            return [day[0], cleared]
    }
}


// __ Возвращаем индекс по смене
export function getIndexByChange(change: IAssemblyTaskChangeKeys): number {
    switch (change) {
        case CHANGE_1:
            return 0
        case CHANGE_2:
            return 1
        default: {
            throw new Error('Undefined Assembly Task Change')
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
 * @param {IAssemblyTask[]} currentTasks  - __Массив после манипуляций (vuedraggable и т.д.)__
 * @param {IAssemblyTask[]} originalTasks - __Глубокая копия (tasksCopy)__
 */
export function getAssemblyTasksDiff(currentTasks: IAssemblyTask[], originalTasks: IAssemblyTask[]) {
    const diffs: IAssemblyTaskArrayDiff[] = []

    // console.log(currentTasks)
    // console.log(originalTasks)

    // __ Индексируем оригинал по ID для быстрого доступа
    const originalMap = new Map(originalTasks.map(task => [task.id, task]))
    const currentMap  = new Map(currentTasks.map(task => [task.id, task]))

    currentTasks.forEach((task) => {
        const original = originalMap.get(task.id)

        if (!original) {
            // __ Если задачи не было в исходном массиве

            const lineChanges: IAssemblyTaskArrayLineDiffs[] = []
            task.assembly_lines.forEach(line => lineChanges.push({
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
        const lineDiffs = getTaskLinesDiff(task.assembly_lines, original.assembly_lines)

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
 * __ Сравнение внутренних строк IAssemblyTaskLine
 * @param currentLines
 * @param originalLines
 */
function getTaskLinesDiff(currentLines: IAssemblyTaskLine[], originalLines: IAssemblyTaskLine[]) {
    const diffs: IAssemblyTaskArrayLineDiffs[] = []
    const originalLinesMap                     = new Map(originalLines.map(l => [l.id, l]))

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
                change.forEach((task: IAssemblyTask) => {
                    // __ Сохраняем "слепок" состояния для сравнения
                    copyMap.set(task.id, {
                        dayOffset,
                        position : task.position,
                        change   : task.change,
                        changeIdx: changeIdx,
                        lines    : JSON.parse(JSON.stringify(task.assembly_lines)), // __ глубокая копия строк
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
                change.forEach((task: IAssemblyTask) => {
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

                    // __ Проверяем детальные изменения в строках (assembly_lines)
                    const lineDiffs = getLinesDetailedDiff(old.lines, task.assembly_lines)

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
function getLinesDetailedDiff(oldLines: IAssemblyTaskLine[], newLines: IAssemblyTaskLine[]) {
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


// __ Получаем Участок (Сектор) по Названию
export function getSectorByName(assemblySector: IAssemblySectorKeys) {
    return Object.values(ASSEMBLY_SECTORS).find(value => value.NAME === assemblySector)
}

// __ Получаем Расход по материалу Участка
export function getSectorExpense(assemblySector: IRenderOrderLineAssemblyLineSector | IAssemblyTaskLineSector) {
    return assemblySector.total_per_pic * assemblySector.amount
}

// __ Получаем Расход по материалу Участка
export function getSectorAmount(assemblySector: IRenderOrderLineAssemblyLineSector | IAssemblyTaskLineSector) {
    return assemblySector.amount
}

// __ Получаем Трудозатраты по материалу Участка
export function getSectorTime(assemblySector: IRenderOrderLineAssemblyLineSector | IAssemblyTaskLineSector) {
    return assemblySector.time
}


// __ Получаем Размер Детальки по материалу Участка
export function getSectorSize(assemblySector: IRenderOrderLineAssemblyLineSector | IAssemblyTaskLineSector) {
    let width  = 0
    let length = 0
    let height = 0

    if (isAssemblyTaskLineSector(assemblySector)) {
        width  = assemblySector.detail_dims.width / 10
        length = assemblySector.detail_dims.length / 10
        height = assemblySector.detail_dims.height / 10
    } else if (isAssemblyTaskLineSectorOrder(assemblySector)) {
        width  = assemblySector.detail_width / 10
        length = assemblySector.detail_length / 10
        height = assemblySector.detail_height / 10
    }

    let size = `${width}x${length}`

    if (height) {
        size = `${size}x${height}`
    }
    return size
}


// --- -------------------------------------------------------------------------------------
// __ Получаем статус СЗ по его ID
export function getTaskStatusById(id: number) {
    const statusKey = Object.keys(ASSEMBLY_TASK_STATUSES).find(key => ASSEMBLY_TASK_STATUSES[key as IAssemblyTaskStatusKeys].ID === id)
    if (statusKey) {
        return ASSEMBLY_TASK_STATUSES[statusKey as IAssemblyTaskStatusKeys]
    }
    return null
}


// --- -------------------------------------------------------------------------------------
// --- ----------------------- Подсчет количества и Трудозатрат ----------------------------
// --- -------------------------------------------------------------------------------------
// __ Создаем сам объект данных с ключами из ASSEMBLY_SECTORS и {time: 0, amount: 0} и инициализируем его нулями
export function createAmountAndTimeObj() {
    return Object.values(ASSEMBLY_SECTORS).reduce((acc, value) => {
        acc[value.NAME as IAssemblySectorKeys] = {
            time  : 0,
            amount: 0,
        }
        return acc
    }, {} as IAmountAndTimeAssembly)
}

export function createAmountAndTimeObjLine() {
    return Object.values(ASSEMBLY_LINES).reduce((acc, value) => {
        acc[value as IAssemblyLineKeys] = {
            time  : 0,
            amount: 0,
        }
        return acc
    }, {} as IAmountAndTimeAssemblyLines)
}


// __ Получаем трудозатраты по Заявке или массиву строк (Содержимого) в формате объекта
export function getAssemblyTaskAmountAndTime(item: IAssemblyTask | IAssemblyTaskLine[]) {

    // __ Проверяем, что пришло на вход
    let itemArr = []
    if (Array.isArray(item)) {
        itemArr = item
    } else {
        itemArr = item.assembly_lines
    }

    //  __ Создаем сам объект данных с ключами из ASSEMBLY_SECTORS и {time: 0, amount: 0} и инициализируем его нулями
    const groupedSectors = createAmountAndTimeObj()

    // __ Собираем все Записи по участкам Производства
    itemArr.forEach(line => {
        line.sector_lines?.forEach(sector => {
            groupedSectors[sector.sector as IAssemblySectorKeys].amount += sector.amount
            groupedSectors[sector.sector as IAssemblySectorKeys].time += sector.time
        })

        groupedSectors[line.assembly_line as IAssemblySectorKeys].amount += line.amount
        groupedSectors[line.assembly_line as IAssemblySectorKeys].time += line.time
    })

    // console.log('groupedSectors:', groupedSectors)

    return groupedSectors
}


// __ Получаем трудозатраты по Заявке или массиву строк (Содержимого) в формате объекта
export function getAssemblyTaskAmountAndTimeLines(item: IAssemblyTask | IAssemblyTaskLine[]) {

    // __ Проверяем, что пришло на вход
    let itemArr = []
    if (Array.isArray(item)) {
        itemArr = item
    } else {
        itemArr = item.assembly_lines
    }

    //  __ Создаем сам объект данных с ключами из ASSEMBLY_SECTORS и {time: 0, amount: 0} и инициализируем его нулями
    const groupedSectors = createAmountAndTimeObjLine()

    // __ Собираем все Записи по участкам Производства
    itemArr.forEach(line => {
        groupedSectors[line.assembly_line as IAssemblyLineKeys].amount += line.amount
        groupedSectors[line.assembly_line as IAssemblyLineKeys].time += line.time
    })
    return groupedSectors
}


// __ Получаем Общие трудозатраты по Заявке или массиву строк (Содержимого) в формате объекта
export function getAssemblyTaskAmountAndTimeTotal(item: IAssemblyTask | IAssemblyTaskLine[]) {
    const totals = getAssemblyTaskAmountAndTime(item)

    // console.log('totals:', totals)

    return Object.values(totals).reduce((acc, item) => {
        acc.amount += item.amount
        acc.time += item.time
        return acc
    }, { amount: 0, time: 0 } as IStatItemAssembly)
}


// __ Получаем трудозатраты в текстовом представлении '05ч. 30м. 18с.'
// __ twoLines = true - Если больше часа, то выводим часы и минуты (обрезаем секунды)
export function getTimeString(blockLine: IAssemblyTaskLine, twoLines: boolean = false, timeType = 'hour') {
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


// __ Возвращает Смену по имени
export function getChangeByName(task: IAssemblyTask | IAssemblyTaskChangeKeys) {
    let compareKey = null
    if (isAssemblyTask(task)) {
        compareKey = task.change
    } else {
        compareKey = task
    }

    const changeKey = Object.keys(CHANGES).find(key => CHANGES[key as keyof typeof CHANGES].NAME === compareKey)
    return changeKey ? CHANGES[changeKey as keyof typeof CHANGES] : null
}


// --- -------------------------------------------------------------------------------------
// __ Получаем статистику по выполнению СЗ
// export function getExecuteTaskStatistics(item: IAssemblyTask | IAssemblyTaskLine[]) {
//
//     const statistics: IAssemblyTaskExecuteStatistics = {
//         amount: {
//             finished  : 0,
//             unfinished: 0,
//             total     : 0,
//         },
//         time  : {
//             finished  : 0,
//             unfinished: 0,
//             total     : 0,
//         },
//     }
//
//     // __ Проверяем, что пришло на вход
//     let itemArr = []
//     if (Array.isArray(item)) {
//         itemArr = item
//     } else {
//         itemArr = item.assembly_lines
//     }
//
//     // __ Получаем суммарное количество и трудозатраты
//     const totalAmountAndTimeObj = getAssemblyTaskAmountAndTime(itemArr)
//
//     // __ Общее Количество
//     statistics.amount.total = Object.values(totalAmountAndTimeObj).reduce((acc, item) => item.amount + acc, 0)
//
//     // __ Общее Трудозатраты
//     statistics.time.total = Object.values(totalAmountAndTimeObj).reduce((acc, item) => item.time + acc, 0)
//
//     // __ Выполненные
//     const finished = itemArr.filter(line => line.finished_at)
//
//     // __ Получаем суммарное количество и трудозатраты для выполненных
//     const finishedAmountAndTimeObj = getAssemblyTaskAmountAndTime(finished)
//
//     // __ Выполненные Количество
//     statistics.amount.finished = Object.values(finishedAmountAndTimeObj).reduce((acc, item) => item.amount + acc, 0)
//
//     // __ Выполненные Трудозатраты
//     statistics.time.finished = Object.values(finishedAmountAndTimeObj).reduce((acc, item) => item.time + acc, 0)
//
//     // __ Не Выполненные
//     const unfinished = itemArr.filter(line => !line.finished_at)
//
//     // __ Получаем суммарное количество и трудозатраты для Не выполненных
//     const unfinishedAmountAndTimeObj = getAssemblyTaskAmountAndTime(unfinished)
//
//     // __ Не Выполненные Количество
//     statistics.amount.unfinished = Object.values(unfinishedAmountAndTimeObj).reduce((acc, item) => item.amount + acc, 0)
//
//     // __ Не Выполненные Трудозатраты
//     statistics.time.unfinished = Object.values(unfinishedAmountAndTimeObj).reduce((acc, item) => item.time + acc, 0)
//
//     return statistics
// }


// --- ------------------------------------------------------------------------------------
// __ Пересчитываем позиции СЗ в массиве СЗ на определенный день
export function repositionAssemblyTaskInDay(tasks: IAssemblyTask[], action_at: string) {
    const parsedDate = action_at.split(' ')[0]
    tasks
        // __ Отбираем только объекты на нужную дату
        .filter(item => item.action_at.split(' ')[0] === parsedDate)
        // __ Сортируем их по возрастанию текущей позиции (включая x.1)
        .sort((a, b) => a.position - b.position)
        // __ Мутируем каждый объект, присваивая новый порядковый номер
        .forEach((item, index) => {
            item.position = index + 1
        })
    return tasks
}


// __ Пересчитываем позицию по порядку записей в массиве строк (AssemblyTaskLine[]) по ссылке
// __ Пересчитываем позицию именно в том порядке, в котором они находятся в исходно массиве
// __ (как определил специалист ОПП при перетаскивании строк или упорядочивании или сортировке)
export function repositionAssemblyTaskLines(entity: IAssemblyTask | IAssemblyTaskLine[]) {
    let items
    if (isAssemblyTask(entity)) {
        items = entity.assembly_lines
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


// --- ------------------------------------------------------------------------------------
// __ Проверяем, есть ли в массиве изменений хотя бы одна сущность для создания в БД
export function isAddItemsInDiffsPresents(diffs: IAssemblyTaskArrayDiff[]) {
    return diffs.some(taskDiff => {

        // __ 1. Проверяем саму задачу
        if (taskDiff.type === 'ADDED') return true

        // __ 2. Безопасно проверяем строки (используем опциональную цепочку ?. )
        // __ Проверяем, есть ли среди изменений строк хотя бы одно с типом 'ADDED'
        return taskDiff.lineChanges?.some(lineDiff => lineDiff.type === 'ADDED') ?? false
    })
}


// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли СЗ расчетным (AVERAGE) или нет
export function isTaskAverage(entity: IAssemblyTask | IAssemblyTaskLine[]) {

    let items: IAssemblyTaskLine[]
    if (isAssemblyTask(entity)) {
        items = entity.assembly_lines
    } else if (Array.isArray(entity)) {
        items = entity
    } else {
        throw new Error('isTaskAverage: unknown incoming data type')
    }

    for (let i = 0; i < items.length; i++) {
        if (items[i]?.is_average) return true
    }

    return false
}

// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли Статус СЗ "Создано" или "Создано при закрытии"
export function isTaskStatusCreated(entity: IAssemblyTask | IAssemblyTaskStatus | number): boolean {
    let item: number
    if (isAssemblyTask(entity)) {
        item = entity.current_status.id
    } else if (isAssemblyTaskStatus(entity)) {
        item = entity.id
    } else if (typeof entity === 'number') {
        item = entity
    } else {
        throw new Error('Invalid entity type')
    }
    return item === ASSEMBLY_TASK_STATUSES.CREATED.ID || item === ASSEMBLY_TASK_STATUSES.ROLLING.ID
}

// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли Статус СЗ "Выполняется"
export function isTaskStatusRunning(entity: IAssemblyTask | IAssemblyTaskStatus | number): boolean {
    let item: number
    if (isAssemblyTask(entity)) {
        item = entity.current_status.id
    } else if (isAssemblyTaskStatus(entity)) {
        item = entity.id
    } else if (typeof entity === 'number') {
        item = entity
    } else {
        throw new Error('Invalid entity type')
    }
    return item === ASSEMBLY_TASK_STATUSES.RUNNING.ID
}

// __ Возвращает Название Заявки
export function getOrderTitle(task: IAssemblyTask) {
    return `${task.order.client.short_name} №${task.order.order_no_str}`
}


// --- ----------------------------------------------------------------------------------
// __ Ищем Приориет Статусов движения Заявки
export function getTaskPriority(task: IAssemblyTask): number {
    const statusKeyId = task.current_status.id

    // __ Ищем подходящий статус в вашем справочнике BLOCK_TASK_STATUSES
    const statusConfig = Object.values(ASSEMBLY_TASK_STATUSES).find(
        s => s.ID === statusKeyId
    )

    return statusConfig ? statusConfig.PRIORITY : 999 // 999 для неизвестных статусов
}


// __ Пересчитываем позиции СЗ в матрице рендера после перетаскивания мышью
export function setTaskPositionInRenderMatrix(matrix: IPlanMatrix): IPlanMatrix {
    matrix.forEach((week, weekIndex) => {
        week.forEach((day, dayIndex) => {

            // __ Собираем все задачи за день в один плоский массив и проставляем им смены
            const allDayTasks: IAssemblyTask[] = []

            day.forEach((change, changeIndex) => {
                const currentChange = changeIndex === 0 ? CHANGE_1 : CHANGE_2

                change.forEach((task: IAssemblyTask) => {
                    allDayTasks.push({
                        ...(task as IAssemblyTask),
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


// --- ------------------------------------------------------------------------------------
// __ Проверяем, есть ли в конкретном дне СЗ для какой-то конкретной Заявки
// __ Если передан entity типа IAssemblyTask и applyStatus = true, то проверяем еще на одинаковость статусов
export function getAssemblyTasksSameOrderInDay(
    entity: IAssemblyTask | IAssemblyTaskOrder | number,
    tasksList: IAssemblyTask[],
    date: string | null   = null,
    change: string | null = null,
    applyStatus: boolean  = false) {

    let item
    if (isAssemblyTask(entity)) {
        item = entity.order.id
        if (!date) date = entity.action_at

    } else if (isAssemblyTaskOrder(entity)) {
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

    if (isAssemblyTask(entity) && applyStatus) {
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
// __ Объединяем СЗ с одинаковыми Заявками (Заявки, к которым принадлежит СЗ)
export function mergeAssemblyTasks(tasks: IAssemblyTask[]): IAssemblyTask[] {
    const grouped = tasks.reduce((acc, task) => {
        const orderId = task.order.id

        if (!acc[orderId]) {

            // __ Если заказа еще нет в словаре, клонируем объект задачи
            // __ Используем структурированное клонирование, чтобы не мутировать исходный массив
            acc[orderId] = JSON.parse(JSON.stringify(task))

        } else {

            // __ Если заказ уже есть, объединяем его assembly_lines
            const targetTask = acc[orderId]

            task.assembly_lines.forEach((newLine) => {
                const existingLine = targetTask.assembly_lines.find(
                    (l) =>
                        // __ Ищем строку с тем же блоком и производственной линии
                        l.order_line.id === newLine.order_line.id &&
                        l.assembly_line === newLine.assembly_line,
                )

                if (existingLine) {
                    // __ Если такая строка заказа уже есть — суммируем количество
                    existingLine.amount += newLine.amount
                } else {
                    // __ Если такой строки еще нет — добавляем её целиком
                    targetTask.assembly_lines.push(JSON.parse(JSON.stringify(newLine)))
                }
            })
        }

        return acc
    }, {} as Record<number, IAssemblyTask>)

    // __Возвращаем массив, сохраняя порядок первого появления каждого order.id
    return Object.values(grouped)
}

// --- ------------------------------------------------------------------------------------
// __ Превращаем массив объектов IAssemblyTask [{}, {}, ...] в массив массивов объектов [[...], [...]]
// __ которые сгруппированы по одинаковой Заявке с возможностью учитывать статусы Заявок в определенном дне
export function getAssemblyTasksGroupedByOrder(assemblyTasks: IAssemblyTask[], applyStatus: boolean = true) {
    // const clearDay = clearRenderMatrixDay(assemblyTasks) // __ Возвращаем новый массив без пустых элементов
    // const grouped  = clearDay.reduce((acc, item) => {
    const grouped = assemblyTasks.reduce((acc, item) => {

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
    }, {} as IAssemblyTask)

    // __ Превращаем объект { 1: [...], 2: [...] } в массив массивов [[...], [...]]
    return Object.values(grouped)
}

// --- -------------------------------------------------------------------------------------
// __ Устанавливаем необходимы порядок (пронумеровываем заявки в массиве)
// __ в соответствии со статусами
// __ 1. Сначала статус СЗ - Done по возрастанию даты
// __ 2. Потом статус СЗ   - Running по возрастанию даты
// __ 3. Потом статус СЗ   - Pending по возрастанию даты
// __ 4. Потом статус СЗ   - Created по возрастанию даты
export const orderAssemblyTasksByStatus = (day: IDay): IDay => {

    // __ Собираем все задачи за день в один плоский массив и проставляем им смены
    const allDayTasks: IAssemblyTask[] = []

    day.forEach((change: IAssemblyTask[], changeIndex: number) => {
        const currentChange = changeIndex === 0 ? CHANGE_1 : CHANGE_2

        change.forEach((task: IAssemblyTask) => {
            allDayTasks.push({
                ...(task as IAssemblyTask),
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
}


// --- ------------------------------------------------------------------------------------
// __ Проверяем, является ли СЗ со столом Undefined
export function hasTaskUnknownAssemblyLine(entity: IAssemblyTask | IAssemblyTaskLine[]) {

    let items: IAssemblyTaskLine[]
    if (isAssemblyTask(entity)) {
        items = entity.block_lines
    } else if (Array.isArray(entity)) {
        items = entity
    } else {
        throw new Error('hasTaskUnknownAssemblyLine: unknown incoming data type')
    }

    for (let i = 0; i < items.length; i++) {
        if (items[i].assembly_line === ASSEMBLY_LINE_UNDEFINED) return true
    }

    return false
}

// __ Получаем трудозатраты на один блок
export function getAssemblyTimePerPic(assemblyLine: IAssemblyTaskLine): number {
    return assemblyLine.amount !== 0 ? assemblyLine.time / assemblyLine.amount : 0
}

/**
 * __ Функция, которая возвращает высчитанный объект количества при разделении строки на количество
 * __ Возвращает новый экземпляр с пересчитанными данными
 * @param assemblyLine      __Входная строка__
 * @param newAmount         __Новое количество__
 */
export function calculateDividedAmountAndTime(assemblyLine: IAssemblyTaskLine, newAmount: number): IAssemblyTaskLine {

    // __ Создаем копию строки (референсная)
    const refAssemblyLine = { ...assemblyLine }

    const timePerPic       = getAssemblyTimePerPic(assemblyLine)
    // const timePerPic    = assemblyLine.amount !== 0 ? assemblyLine.time / assemblyLine.amount : 0
    refAssemblyLine.time   = timePerPic * newAmount
    refAssemblyLine.amount = newAmount

    return refAssemblyLine
}

// --- ------------------------------------------------------------------------------------
// __ Объединяем строки СЗ с принадлежностью к одной и той же строке Заявки
export function mergeAssemblyLines(lines: IAssemblyTaskLine[]): IAssemblyTaskLine[] {

    const grouped = lines.reduce((acc, line) => {

        // __ Создаем составной ключ: ID + Линия Сборки
        const assembly_line = line.assembly_line
        const groupKey      = `${line.order_line.id}_${assembly_line}`

        if (!acc[groupKey]) {
            acc[groupKey] = JSON.parse(JSON.stringify(line))
        } else {

            // __ Складываем количество
            acc[groupKey].amount += line.amount

            // __ Складываем трудозатраты (time)
            acc[groupKey].time += line.time

            // if (acc[groupKey].time && line.time) {
            //     for (const key in line.time) {
            //         acc[groupKey].time[key] = (acc[groupKey].time[key] || 0) + line.time[key];
            //     }
            // }
        }

        return acc
    }, {} as Record<string, IAssemblyTaskLine>)

    return Object.values(grouped)
}

// --- ------------------------------------------------------------------------------------
// __ Получаем Размер модели
export function getOrderLineSize(line: IAssemblyTaskLine | IAssemblyTaskOrderLine): string {
    let target = null
    if (isIAssemblyTaskLine(line)) {
        target = line.order_line
    } else if (isIAssemblyTaskOrderLine(line)) {
        target = line
    } else {
        throw new Error('Invalid line type in assembly_helpers/getOrderLineSize')
    }

    return `${target.dims.width}x${target.dims.length}x${target.dims.height}`
}

// --- ------------------------------------------------------------------------------------

// __ Сортируем массив строк по размерам
export function sortAssemblyTaskLinesBySize(
    item: IAssemblyTask | IAssemblyTaskLine[],
    direction: 'asc' | 'desc' = 'asc',
): IAssemblyTaskLine[] {

    // __ Проверяем, что пришло на вход
    let sourceArray: IAssemblyTaskLine[] = []
    if (Array.isArray(item)) {
        sourceArray = item
    } else {
        sourceArray = item.cutting_lines
    }

    const dir = direction === 'asc' ? 1 : -1

    return sourceArray.toSorted((a, b) => {
        if (a.order_line.dims.width !== b.order_line.dims.width) {
            return (a.order_line.dims.width - b.order_line.dims.width) * dir
        }

        if (a.order_line.dims.length !== b.order_line.dims.length) {
            return (a.order_line.dims.length - b.order_line.dims.length) * dir
        }

        return (a.order_line.dims.height - b.order_line.dims.height) * dir
    })
}


// __ Возвращаем подготовленный объект для отображения в Манипуляции СЗ Сборки
// __ Выносим в отдельную функцию, чтобы не таскать портянку
// __ Оставляем 5 дней до текущей даты и 7 после последней непустой
export function getAssemblyManipulationRenderTasks(tasks: IAssemblyTask[], planPeriod: IPeriod): IAssemblyManipulateDay[] {
    // __ Создаем массив
    const grouped = Object.groupBy(tasks, task => task.action_at)

    const renderTasks: IAssemblyManipulateDay[] = Object.entries(grouped)
        .map(([key, value]) => {
            return {
                action_at: key.split(' ')[0],
                tasks    : value as IAssemblyTask[],
            }
        })
        .toSorted((a, b) => (new Date(a.action_at)).getTime() - (new Date(b.action_at)).getTime())

    // __ Дополняем отсутствующими датами и пустыми массивами
    const taskMap = new Map(
        renderTasks.map(item => [item.action_at, item.tasks])
    )

    let filledTasks = []

    const startDateStr = planPeriod.start.split(' ')[0] // '2026-08-01'
    const startDate    = new Date(startDateStr)

    // __ Считаем текущую дату минус 5 дней (обнуляем время для корректного сравнения)
    const fiveDaysAgo = new Date()
    fiveDaysAgo.setDate(fiveDaysAgo.getDate() - 4)
    fiveDaysAgo.setHours(0, 0, 0, 0)

    // __ Берём максимальный timestamp (то, что позже)
    const current = new Date(Math.max(startDate.getTime(), fiveDaysAgo.getTime()))
    // const current = new Date(startDateStr)

    const endDateStr = planPeriod.end.split(' ')[0]
    const end        = new Date(endDateStr)

    while (current <= end) {
        // Форматируем текущую дату в 'YYYY-MM-DD'
        const dateStr = current.toISOString().split('T')[0]

        filledTasks.push({
            action_at: dateStr,
            tasks    : taskMap.get(dateStr) || [],
            collapsed: !Boolean(taskMap.get(dateStr)),
        })

        // Прибавляем +1 день
        current.setDate(current.getDate() + 1)
    }

    // __ Находим с конца последний непустой день
    let lastNonEmptyIndex = -1
    for (let i = filledTasks.length - 1; i >= 0; i--) {
        if (filledTasks[i].tasks && filledTasks[i].tasks.length > 0) {
            lastNonEmptyIndex = i
            break
        }
    }

    // __ Отрезаем пустой хвост, оставляя ровно 7 дней после последнего непустого
    if (lastNonEmptyIndex !== -1) {
        filledTasks = filledTasks.slice(0, lastNonEmptyIndex + 1 + 7)
    } else {
        filledTasks = filledTasks.slice(0, 7)
    }

    return filledTasks
}


// __ Объект отображения данных для каждого участка для одного СЗ
export function getDataArray(taskSource: IAssemblyTask, short: boolean = false) {

    const task = JSON.parse(JSON.stringify(taskSource))

    const lamitObj = {
        total_amount   : 0,
        finished_amount: 0,
    }

    const tableObj = {
        total_amount   : 0,
        finished_amount: 0,
    }

    task.assembly_lines.forEach((line: IAssemblyTaskLine) => {
        if (line.assembly_line === ASSEMBLY_LINES.ASSEMBLY_LINE_LAMIT) {
            lamitObj.total_amount += line.amount
            lamitObj.finished_amount += line.finished_at ? line.amount : 0
        }
        if (line.assembly_line === ASSEMBLY_LINES.ASSEMBLY_LINE_TABLE) {
            tableObj.total_amount += line.amount
            tableObj.finished_amount += line.finished_at ? line.amount : 0
        }
    })

    task.stats.push({
        sector         : ASSEMBLY_TASK_SECTOR_LAMIT,
        total_amount   : lamitObj.total_amount,
        finished_amount: lamitObj.finished_amount
    })

    task.stats.push({
        sector         : ASSEMBLY_TASK_SECTOR_TABLE,
        total_amount   : tableObj.total_amount,
        finished_amount: tableObj.finished_amount
    })
    const data: IStats[] = []

    Object.values(ASSEMBLY_SECTORS).forEach(value => {
        const stats = task.stats.find((s: IAssemblyTaskLineSector) => s.sector === value.NAME)

        const total   = stats?.total_amount || 0
        const done    = stats?.finished_amount || 0
        const percent = total > 0 ? (done / total) * 100 : 0

        let color = getColorByPercent(percent)
        let title = percent.toFixed(0) + '%'

        if (!short) {
            title = title + ' ' + `(${done}/${total})`
        }

        if (total === 0) {
            color = '#e1f5fe'
            // color = '#67748B'
            title = '✗'
        } else if (round(percent) === 100) {
            title = '✓'
        }

        data.push({
            id  : value.ID,
            name: value.NAME,
            total,
            done,
            percent,
            title,
            color,
        })
    })

    return data.toSorted((a, b) => a.id - b.id)

}


// __ Объект отображения данных для каждого участка для группы СЗ
export function getDataArrayTotal(tasksSource: IAssemblyTask[], short: boolean = false) {
    const totalArray: IStats[][] = []

    tasksSource.forEach(task => totalArray.push(getDataArray(task, short)))

    const totalData: IStats[] = []

    Object.values(ASSEMBLY_SECTORS).forEach(value => {
        const summary: IStats = {
            id     : value.ID,
            name   : value.NAME,
            total  : 0,
            done   : 0,
            percent: 0,
            color  : '',
            title  : '',
        }

        totalArray.forEach(items => {
            const findItem = items.find(item => item.id === value.ID)
            if (findItem) {
                summary.done += findItem.done
                summary.total += findItem.total
            }
        })

        const total   = summary.total
        const done    = summary.done
        const percent = total > 0 ? (done / total) * 100 : 0

        let color = getColorByPercent(percent)
        let title = percent.toFixed(0) + '%'

        if (!short) {
            title = title + ' ' + `(${done}/${total})`
        }

        if (total === 0) {
            color = '#e1f5fe'
            // color = '#67748B'
            title = '✗'
        } else if (round(percent) === 100) {
            title = '✓'
        }

        summary.percent = percent
        summary.color   = color
        summary.title   = title

        totalData.push(summary)
    })

    return totalData
}


// __ Фильтруем СЗ по участкам, причем убираем из результата то СЗ, где нет таких участков
export function filterTaskBySectors(entity: IAssemblyTask[] | IAssemblyManipulateDay, sectorFilter: IAssemblySectorKeys[] | IAssemblySectorKeys) {
    let tasks = []
    if (Array.isArray(entity)) {
        tasks = entity
    } else {
        tasks = entity.tasks
    }

    let sectorList = []
    if (Array.isArray(sectorFilter)) {
        sectorList = sectorFilter
    } else {
        sectorList = [sectorFilter]
    }

    return tasks.filter(task => {
        const filteredLines = task.assembly_lines.filter(line => {
            const filteredSectors = line.sector_lines.filter(sector => sectorList.includes(sector.sector))
            if (filteredSectors.length > 0) {
                line.sector_lines = filteredSectors
                return true
            }
            return false
        })

        if (filteredLines.length > 0) {
            task.assembly_lines = filteredLines
            return true
        }
        return false
    })
}


// __ Возвращаем Матрицу для отображения Материалов и Самих Изделий, как в ЕПС для Группы СЗ
export function getSectorMaterialsMatrixTasks(entity: IAssemblyTask | IAssemblyTask[]): IMatrixManufactureTask[] {
    let tasks = []
    if (Array.isArray(entity)) {
        tasks = entity
    } else if (isAssemblyTask(entity)) {
        tasks = [entity]
    } else {
        throw new Error('Недопустимый тип - assembly_sectors/getSectorMaterialsMatrixTasks')
    }

    return tasks
        .map(task => getSectorMaterialsMatrixTask(task))
        .toSorted((a, b) => a.task.position - b.task.position)
}


// __ Возвращаем Матрицу для отображения Материалов и Самих Изделий, как в ЕПС для Одного СЗ
export function getSectorMaterialsMatrixTask(entity: IAssemblyTask | IAssemblyTaskLine[]): IMatrixManufactureTask {
    let taskLines = []
    let task      = null

    if (Array.isArray(entity)) {
        taskLines = entity
        task      = {
            ...ASSEMBLY_TASK_DRAFT,
            assembly_lines: taskLines,
        }
    } else if (isAssemblyTask(entity)) {
        taskLines = entity.assembly_lines
        task      = entity
    } else {
        throw new Error('Недопустимый тип - assembly_sectors/getSectorMaterialsMatrixTask')
    }

    // console.log('taskLines: ', taskLines)

    const groupsCache    = new Map()    // __ Все Группы Сортировки
    const materialsCache = new Map()    // __ Все Материалы

    taskLines.forEach(line => {
        groupsCache.set(line.order_line.model.manufacture_group.id, line.order_line.model.manufacture_group)
        line.sector_lines.forEach(sectorLine => materialsCache.set(sectorLine.material_code_1c, {
            code_1c: sectorLine.material_code_1c,
            name   : sectorLine.material_name
        }))
    })

    // console.log('groupsCache: ', groupsCache)
    // console.log('materialsCache: ', materialsCache)

    const grouped = Map.groupBy(taskLines, item => {
        const group = item.order_line.model.manufacture_group

        // Если объект с таким ID уже встретился, возвращаем ЕГО ссылку
        // if (!groupsCache.has(group.id)) {
        //     groupsCache.set(group.id, group)
        // }

        return groupsCache.get(group.id)
    })

    // console.log('grouped: ', grouped)

    // __ Переводим в массив для сортировки Map с Материалами
    const materialsCacheArray = Array.from(materialsCache.values()).sort((a, b) => a.name.localeCompare(b.name))

    // __ И Обращаем обратно в Map для скорости досупа
    const materialsCacheArrayMap = new Map()
    materialsCacheArray.forEach((material, index) => {
        materialsCacheArrayMap.set(material.code_1c, { code_1c: material.code_1c, name: material.name, index })
    })

    const groups: IMatrixManufactureGroup[] = []

    // __ Проходимся по Кэшу Групп Группировки и собираем массив
    for (const [, manufactureGroup] of groupsCache) {

        // __ Получаем Массив Строк СЗ для данной Группы Сортировки
        let manufactureGroupLines = grouped.get(manufactureGroup) || []

        // __ Тут же сортируем его по убыванию Размера в Заявке
        manufactureGroupLines = manufactureGroupLines.toSorted((a, b) => {
            if (a.order_line.dims.width != b.order_line.dims.width) {
                return b.order_line.dims.width - a.order_line.dims.width
            }
            if (a.order_line.dims.length != b.order_line.dims.length) {
                return b.order_line.dims.length - a.order_line.dims.length
            }
            return b.order_line.dims.height - a.order_line.dims.height
        })

        const groupLines: IMatrixManufactureGroupLine[] = []

        // __ Перебираем все Модели
        manufactureGroupLines.forEach(assemblyLine => {

            // __ Создаем массив Длины Всех Материалов
            const matrix = Array(materialsCacheArray.length).fill(null)

            // __ Перебираем все Материалы и Запихиваем в нужную ячейку массива
            assemblyLine.sector_lines.forEach(sector => {
                const material         = materialsCacheArrayMap.get(sector.material_code_1c)
                matrix[material.index] = sector
            })

            groupLines.push({
                order_line     : assemblyLine.order_line,
                materials_array: matrix,
            })

        })

        const group = {
            group      : manufactureGroup as IAssemblyModelManufactureGroup,
            group_lines: groupLines,
        }

        groups.push(group)
    }

    // console.log('groups: ', groups)

    // __ Сортируем Группы
    return {
        task,
        groups: groups.toSorted((a, b) => a.group.group_number - b.group.group_number)
    }
}


// --- -------------------------------------------------------------------------------------
// __ Функция-помощник: говорит TS, является ли item типом IAssemblyTaskLineSector (для функционала)
function isAssemblyTaskLineSector(item: unknown): item is IAssemblyTaskLineSector {
    return !!item && typeof item === 'object' && 'detail_dims' in item && 'dims' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IAssemblyTaskLine (для функционала)
function isIAssemblyTaskLine(item: unknown): item is IAssemblyTaskLine {
    return !!item && typeof item === 'object' && 'assembly_line' in item && 'order_line' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IAssemblyTaskOrderLine (для функционала)
function isIAssemblyTaskOrderLine(item: unknown): item is IAssemblyTaskOrderLine {
    return !!item && typeof item === 'object' && 'dims' in item && 'model' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IRenderOrderLineAssemblyLineSector (для вывода заявки)
function isAssemblyTaskLineSectorOrder(item: unknown): item is IRenderOrderLineAssemblyLineSector {
    return !!item && typeof item === 'object' && 'detail_width' in item && 'detail_length' in item && 'detail_height' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IAssemblyTask
function isAssemblyTask(item: unknown): item is IAssemblyTask {
    return !!item && typeof item === 'object' && 'order' in item && 'assembly_lines' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IAssemblyaskStatus
function isAssemblyTaskStatus(item: unknown): item is IAssemblyTaskStatus {
    return !!item && typeof item === 'object' && 'id' in item && 'color' in item && 'name' in item && 'pivot' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IAssemblyTaskOrder
function isAssemblyTaskOrder(item: unknown): item is IAssemblyTaskOrder {
    return !!item && typeof item === 'object' && 'client' in item && 'order_type' in item
}
