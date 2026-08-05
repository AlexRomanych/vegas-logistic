// __ Все, что касается Сборки


// --- --------------------------------------------------------------------
// --- ----------- Для рендера Групп Моделей для Сортировки ---------------
// --- --------------------------------------------------------------------
export interface IAssemblyModelManufactureGroup {
    id: number
    name: string
    active: boolean
    group_number: number
    color: string
    description: string | null
}
