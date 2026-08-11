import type {
    IAmountAndTimeAssembly,
    IAmountAndTimeBlock,
    IAssemblySectorKeys,
    IAssemblyTask,
    IAssemblyTaskArrayDiff,
    IAssemblyTaskArrayLineDiffs,
    IAssemblyTaskChangeKeys,
    IAssemblyTaskLine, IAssemblyTaskLineSector, IAssemblyTaskStatusKeys, IBlockManufLine, IBlockTask, IBlockTaskLine,
    IDay,
    IPlanMatrix,
    IRenderMatrixDiff, IRenderMatrixLineDiffs, IRenderOrderLineAssemblyLineSector, IStatItemAssembly
} from '@/types'
import { ASSEMBLY_SECTORS, ASSEMBLY_TASK_DRAFT, ASSEMBLY_TASK_STATUSES } from '@/app/constants/assembly.ts'
import { CHANGE_1, CHANGE_2 } from '@/app/constants/assembly.ts'
import { BLOCK_MANUF_LINES } from '@/app/constants/blocks.ts'
import { getBlockTaskLineSquare } from '@/app/helpers/manufacture/helpers_blocks.ts'


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
// __ Функция-помощник: говорит TS, является ли item типом IAssemblyTaskLineSector (для функционала)
function isAssemblyTaskLineSector(item: unknown): item is IAssemblyTaskLineSector {
    return !!item && typeof item === 'object' && 'detail_dims' in item && 'dims' in item
}

// __ Функция-помощник: говорит TS, является ли item типом IRenderOrderLineAssemblyLineSector (для вывода заявки)
function isAssemblyTaskLineSectorOrder(item: unknown): item is IRenderOrderLineAssemblyLineSector {
    return !!item && typeof item === 'object' && 'detail_width' in item && 'detail_length' in item && 'detail_height' in item
}






// --- -------------------------------------------------------------------------------------
// --- ----------------------- Подсчет количества и Трудозатрат ----------------------------
// --- -------------------------------------------------------------------------------------
// __ Создаем сам объект данных с ключами из BLOCK_MANUF_LINES и {time: 0, amount: 0} и инициализируем его нулями
export function createAmountAndTimeObj() {
    return Object.values(ASSEMBLY_SECTORS).reduce((acc, value) => {
        acc[value.NAME as keyof typeof ASSEMBLY_SECTORS] = {
            time  : 0,
            amount: 0,
        }
        return acc
    }, {} as IAmountAndTimeAssembly)
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
        line.sector_lines.forEach(sector => {
            groupedSectors[sector.sector as keyof typeof ASSEMBLY_SECTORS].amount += sector.amount
            groupedSectors[sector.sector as keyof typeof ASSEMBLY_SECTORS].time += sector.time
        })
    })

    // console.log('groupedSectors:', groupedSectors)

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
    }, {amount: 0, time: 0} as IStatItemAssembly)
}
