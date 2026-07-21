import type { IEventLogLevel, IEventLogObj } from '@/types'
import { EVENT_LOG_LEVELS } from '@/app/constants/log.ts'


// __ Получаем Константный Объект Лога
export function getEventLogObjectByLevel(level: IEventLogLevel): IEventLogObj | null {
    const found = Object.values(EVENT_LOG_LEVELS).find(item => item.LEVEL === level);
    return found || null;
}


// __ Получаем название модуля
export function getModuleTitle(module: string): string {
    switch (module) {
        case 'Cut': return 'Крой'
        case 'CuttingTask': return 'СЗ Раскрой'
        case 'Expense': return 'СВПМ'
        case 'ModelsUpdate': return 'Обновление Справочников'
    }
    return ''
}
