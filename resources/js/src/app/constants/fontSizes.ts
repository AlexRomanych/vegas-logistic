// Константы размеров шрифта в ui компонентах


const fontMicro = 'micro'
const fontMini = 'mini'
const fontSmall = 'small'
const fontNormal = 'normal'
const fontLarge = 'large'
const fontHuge = 'huge'

const fontSizesList = [fontMicro, fontMini, fontSmall, fontNormal, fontLarge, fontHuge]
export { fontMicro, fontMini, fontSmall, fontNormal, fontLarge, fontHuge, fontSizesList }

export type IFontsType =
    typeof fontMicro
    | typeof fontMini
    | typeof fontSmall
    | typeof fontNormal
    | typeof fontLarge
    | typeof fontHuge
