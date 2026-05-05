import { defineStore } from 'pinia'
// import {ref, reactive, computed, watch} from 'vue'
import menu from '../assets/menu.js'
import { ref } from 'vue'

export const useMenuStore = defineStore('menu', () => {

    const menuData = menu

    const expandSidebar = ref(false)
    const sidebarWidthExpanded = ref('200px');
    const sidebarWidthCollapsed = ref('50px');

    // const menuData = menu.map((item, index) => ({...item, groupID: ++index }))    // Добавляем id группы

    // console.log(menuData, typeof menuData)
    //
    // const menuList = reactive(menuData)
    //
    // console.log('Это меню', menuList)


    return {
        menu,
        // menuData,

        expandSidebar,
        sidebarWidthExpanded,
        sidebarWidthCollapsed,

    }

})
