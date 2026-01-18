// Константы размеров шрифта в ui компонентах

const fontPico   = 'pico'
const fontNano   = 'nano'
const fontMicro  = 'micro'
const fontMini   = 'mini'
const fontSmall  = 'small'
const fontNormal = 'normal'
const fontLarge  = 'large'
const fontHuge   = 'huge'

const fontSizesList = [fontPico, fontNano, fontMicro, fontMini, fontSmall, fontNormal, fontLarge, fontHuge]
export { fontPico, fontNano, fontMicro, fontMini, fontSmall, fontNormal, fontLarge, fontHuge, fontSizesList }

export type IFontsType =
    typeof fontPico
    | typeof fontNano
    | typeof fontMicro
    | typeof fontMini
    | typeof fontSmall
    | typeof fontNormal
    | typeof fontLarge
    | typeof fontHuge
