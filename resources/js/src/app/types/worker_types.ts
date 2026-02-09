// Info: Все, что касается персонала - Workers

// --- -----------------------------------------------------------------------
// --- ------------------- Тип Сотрудник -------------------------------------
// --- -----------------------------------------------------------------------

export interface IWorker {
    id: number
    active: boolean
    surname: string
    name: string
    patronymic: string | null
    address: string | null
    email: string | null
    phone: string | null
    description: string | null
    color?: string
    cell_item?: {
        id: number
        name: string
        no: number
    }
}

