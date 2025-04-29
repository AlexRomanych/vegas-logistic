// info: Тут все, что касается рабочего персонала

// descr: Получить ФИО сотрудника с корректными заглавными буквами
// descr: из 3-х строк: name = "ИвАн", surname = "ИВаноВ" и patronymic = "ИваНОВИЧ" делает следующую строку: "Иванов И. И."
export function getFormatFIO({surname, name, patronymic}) {
    const normalizedSurname = surname.charAt(0).toUpperCase() + surname.slice(1).toLowerCase()
    const normalizedNameInitial = name.charAt(0).toUpperCase() + '.'
    const normalizedPatronymicInitial = patronymic.charAt(0).toUpperCase() + '.'

    return `${normalizedSurname} ${normalizedNameInitial} ${normalizedPatronymicInitial}`
}
