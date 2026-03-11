// Info Получаем ссылку на Яндекс.Карту

// https://yandex.ru/maps/?pt=**долгота**,**широта**

type IArgs = number | string
type IMap = 'Y' | 'G'

const LINK_YA = 'https://yandex.ru/maps/?ll='
// const LINK_YA_2 = 'https://yandex.ru/maps/?pt='
// const LINK_GOOGLE = 'https://www.google.com/maps/place//@'

// ___ Поиск
const LINK_GOOGLE = 'https://www.google.com/maps/search/?api=1&query='
// https://www.google.com/maps/search/?api=1&query=52.0935780%2C23.7010480

// ___ Карта с центрированием
// https://www.google.com/maps/@?api=1&map_action=map&center=52.093578%2C23.701048&zoom=18&basemap=roadmap
// https://www.google.com/maps/@?api=1&map_action=map&center=52.093578%2C23.701048&zoom=19&basemap=roadmap&layer=transit
// basemap (необязательно): определяет тип отображаемой карты. Значением может быть roadmap (по умолчанию), satellite или terrain.

// ___ Карта тоже работает
// https://www.google.com/maps/place//@52.0935780,23.7010480,17z 17z - зум на схеме, 146m - карта спутника с масштабом

export function getLocationLink(lat: IArgs, lon: IArgs, map: IMap = 'G'): string {
    let wLat: string, wLon: string, link: string

    if (typeof lat === 'string') {
        wLat = lat
    } else if (typeof lat === 'number') {
        wLat = lat.toFixed(7)
    } else {
        throw new Error('Unknown type of latitude')
    }

    if (typeof lon === 'string') {
        wLon = lon
    } else if (typeof lon === 'number') {
        wLon = lon.toFixed(7)
    } else {
        throw new Error('Unknown type of longitude')
    }

    link = map === 'G'
        ? `${LINK_GOOGLE}${wLat.replace(',','.')}%2C${wLon.replace(',','.')}`
        : `${LINK_YA}${wLat.replace(',','.')},${wLon.replace(',','.')}`

    // console.log(link)

    return link
}
