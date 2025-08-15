import { FABRIC_MACHINES } from './constants'

// __ Стегальные машины
export interface IFabricMachine  {
    ID: number
    TITLE: string
    NAME: string,
    INDEX: string,
}

export type MachineUnionType =
    | typeof FABRIC_MACHINES.AMERICAN
    | typeof FABRIC_MACHINES.GERMAN
    | typeof FABRIC_MACHINES.CHINA
    | typeof FABRIC_MACHINES.KOREAN

// line --------------------------------------------
