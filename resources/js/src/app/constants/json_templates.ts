// info: Шаблоны для валидации JSON, которые используются в API

type IJsonTemplate = number | string | boolean | null | any[]


// __ Шаблон для валидации Заявки при загрузки с диска
export const ORDER_TEMPLATE = {
    client_id:        (val: IJsonTemplate) => typeof val === 'number', //63,
    client_full_name: (val: IJsonTemplate) => typeof val === 'string', //"RAY",
    client_add_name:  (val: IJsonTemplate) => typeof val == 'string', //"",
    client_code:      (val: IJsonTemplate) => typeof val == 'string', //"004033",
    order_code:       (val: IJsonTemplate) => typeof val == 'string', //"0000045235",
    order_no:         (val: IJsonTemplate) => typeof val == 'string', //"5",
    order_no_1c:      (val: IJsonTemplate) => typeof val == 'string', //"Заявка ИП №5",
    manuf_at_1c:      (val: IJsonTemplate) => typeof val == 'string', //"19.12.2025",
    load_at:          (val: IJsonTemplate) => typeof val == 'string', //"21.12.2025",
    load_at_1c:       (val: IJsonTemplate) => typeof val == 'string', //"21.12.2025",
    unload_at:        (val: IJsonTemplate) => typeof val == 'string', //"",
    kb_start:         (val: IJsonTemplate) => typeof val == 'string', //"16.12.2025 11:22:46",
    kb_end:           (val: IJsonTemplate) => typeof val == 'string', //"16.12.2025 11:30:53",
    mg_start:         (val: IJsonTemplate) => typeof val == 'string', //"",
    mg_end:           (val: IJsonTemplate) => typeof val == 'string', //"",
    responsible:      (val: IJsonTemplate) => typeof val == 'string', //"Ольга Кептюха",
    comment:          (val: IJsonTemplate) => typeof val == 'string', //"Заявка ИП №5",
    base:             (val: IJsonTemplate) => typeof val == 'string', //"",
    service:          (val: IJsonTemplate) => typeof val == 'string', //"16.12.2025 11:22:46#16.12.2025 11:30:53#19.12.2025#21.12.2025#Ольга Кептюха#Заявка ИП №5##",
    items:            (val: IJsonTemplate) => Array.isArray(val),     //
}


export const PLAN_LOAD_TEMPLATE = {
    client_id:     (val: IJsonTemplate) => typeof val === 'number',  //32,
    order_no:      (val: IJsonTemplate) => typeof val === 'string',  //"595.1",
    load_at:       (val: IJsonTemplate) => typeof val === 'string',  //"02.12.2025",
    unload_at:     (val: IJsonTemplate) => typeof val === 'string',  //"03.12.2025",
    elements_type: (val: IJsonTemplate) => typeof val === 'string',  //"mattresses",
    amounts:       (val: IJsonTemplate) => val !== null && typeof val === 'object',
}
