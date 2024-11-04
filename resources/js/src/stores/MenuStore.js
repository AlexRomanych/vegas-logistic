import {defineStore} from 'pinia'
import {ref, reactive, computed, watch} from 'vue'
import menu from '../assets/menu.js'

export const useMenuStore = defineStore('menu', () => {

    const menuData = menu().map((item, index) => ({...item, groupID: ++index }))    // Добавляем id группы

    console.log(menuData, typeof menuData)
    //
    // const menuList = reactive(menuData)
    //
    // console.log('Это меню', menuList)


    return {
        menuData,
    }

})
