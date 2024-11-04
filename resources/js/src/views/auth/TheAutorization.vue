<template>
    <div class="w-screen h-screen flex flex-col justify-center items-center bg-slate-200 z-50">
        <form @submit.prevent="formSubmit">

            <div class="w-[500px] border rounded-lg border-slate-500 shadow shadow-slate-700 bg-slate-300">
                <div class="flex justify-around items-center ml-2 mr-2 mt-2 font-semibold text-xl text-slate-600">
                    <div>
                        <router-link :to="{name: 'login'}">
                            <AppInputButton
                                id="login_button"
                                :type="!isRegister ? 'dark' : 'light'"
                                title="Вход"
                                width="w-[200px]"
                            />
                        </router-link>
                    </div>
                    <div>
                        <router-link :to="{name: 'register'}">
                            <AppInputButton
                                id="register_button"
                                :type="isRegister ? 'dark' : 'light'"
                                title="Регистрация"
                                width="w-[200px]"
                            />
                        </router-link>
                    </div>
                </div>

                <div class="m-3">

                    <AppInputText
                        :id="login"
                        v-model.trim="v$.loginValue.$model"
                        :errors="v$.loginValue.$errors"
                        func="email"
                        label="Введите login (email):"
                        placeholder="E-mail..."
                        type="dark"
                        width="100%"
                        @getInputText="loginHandler"
                    />

                    <AppInputText
                        v-if="isRegister"
                        :id="name"
                        v-model.trim="v$.nameValue.$model"
                        :errors="v$.nameValue.$errors"
                        label="Имя:"
                        placeholder="Введите имя..."
                        type="dark"
                        width="100%"
                    />

                    <AppInputText
                        :id="password"
                        v-model.trim="v$.passwordValue.$model"
                        :errors="v$.passwordValue.$errors"
                        func="password"
                        label='Пароль:'
                        placeholder='Введите пароль...'
                        type="dark"
                        width="100%"
                    />

                    <AppInputText
                        v-if="isRegister"
                        :id="password_confirmation"
                        v-model.trim="v$.passwordСonfirmationValue.$model"
                        :errors="v$.passwordСonfirmationValue.$errors"
                        func="password"
                        label='Повторите пароль:'
                        placeholder='Пароль еще раз...'
                        type="dark"
                        width="100%"
                    />

                </div>

                <div class="m-3 mt-5 flex justify-end">
                    <AppInputButton
                        id="newButton"
                        :title="isRegister ? 'Зарегистрироваться' : 'Войти'"
                        func="submit"
                        type="dark"
                        width="w-[200px]"

                    />
                </div>

            </div>
        </form>

        <!--        <div>-->
        <!--           {{v$}}-->
        <!--        </div>-->

        <AppCallout
            v-if="!currUser.email && confirmClick"
            text="Неверный пароль или имя пользователя"
            type="danger"
            @toggleShow="calloutHandler"
        />


    </div>


</template>

<script setup>
import {useVuelidate} from '@vuelidate/core'
import {required, email, minLength, helpers, sameAs} from '@vuelidate/validators'

import {useUserStore} from "@/src/stores/UserStore.js"

import AppInputText from "@/src/components/ui/inputs/AppInputText.vue";
import AppInputButton from "@/src/components/ui/inputs/AppInputButton.vue";
import AppCallout from "@/src/components/ui/callouts/AppCallout.vue";

import ErrorClass from "@/src/app/classes/ErrorClass.js";
import UserClass from "@/src/app/classes/UserClass.js";

import {ref, computed, reactive} from "vue";
import {useRouter} from "vue-router";

const props = defineProps({
    isRegister: Boolean
})

// определяем id для полей
const login = ref("login")
const name = ref("name")
const password = ref("password")
const password_confirmation = ref("password_confirmation")

// определяем модели
const loginValue = defineModel('loginValue', {
    type: String,
    default: ''
})

const nameValue = defineModel('nameValue', {
    type: String,
    default: ''
})

const passwordValue = defineModel('passwordValue', {
    type: String,
    default: ''
})

const passwordСonfirmationValue = defineModel('passwordСonfirmationValue', {
    type: String,
    default: ''
})

const user = useUserStore()
// console.log(user.user)


const router = useRouter()

const rulesObject = {
    loginValue: {
        email: helpers.withMessage('Неверный формат e-mail', email),
        required: helpers.withMessage('Поле обязательно для заполнения', required),
    },
    passwordValue: {
        required: helpers.withMessage('Поле обязательно для заполнения', required),
        minLength: helpers.withMessage('Минимальная длина пароля - 8 символов', minLength(8)),
    },
}


if (props.isRegister) {
    rulesObject.passwordСonfirmationValue = {}
    rulesObject.passwordСonfirmationValue.required = helpers.withMessage('Поле обязательно для заполнения', required)
    rulesObject.passwordСonfirmationValue.sameAsPassword = helpers.withMessage('Пароли не совпадают', sameAs(passwordValue))

    rulesObject.nameValue = {}
    rulesObject.nameValue.required = helpers.withMessage('Поле обязательно для заполнения', required)
    rulesObject.nameValue.minLength = helpers.withMessage('Минимальная длина имени - 3 символа', minLength(3))
}

// console.log(rulesObject)

const rules = computed(() => (rulesObject))

const verify = props.isRegister ?
    {
        loginValue,
        passwordValue,
        passwordСonfirmationValue,
        nameValue
    } :
    {
        loginValue,
        passwordValue
    }

const v$ = useVuelidate(rules, verify)

const loginHandler = (text) => {


}

// для того, чтобы оставить анимацию в callout
const calloutHandler = (show) => {
    setInterval((
        () => confirmClick.value = false
    ), 500)
}


let currUser = reactive(new UserClass())        // определяем реактивный класс для вызова callout
let confirmClick = ref(false)            // определяем для вывода этого callout

console.log(currUser)

const formSubmit = async (e) => {

    v$.value.$touch()                           // валидируем всю форму

    if (!v$.value.$error) {

    let currUserTemp = null                     // для получения данных с сервера

        if (props.isRegister) {
            currUserTemp = user.registerUser({
                login: loginValue,
                name: nameValue,
                password: passwordValue,
                password_confirmation: passwordСonfirmationValue,
            })
        } else {
            currUserTemp = user.fetchUser({
                email: loginValue.value,
                password: passwordValue.value
            })
        }

        const userData = await currUserTemp

        Object.assign(currUser, userData)

        if (currUser.id !== 0) {
            router.push({name: 'ui'})               // потом изменить
        } else {
            confirmClick.value = true
        }

    }

}

</script>

<style scoped>

</style>
