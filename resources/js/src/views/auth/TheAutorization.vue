<template>
    <div class="main-container">
        <form @submit.prevent="formSubmit">

            <div class="form-container">
                <div class="container">
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
                        id="submitButton"
                        :title="isRegister ? 'Зарегистрироваться' : 'Войти'"
                        func="submit"
                        type="dark"
                        width="w-[200px]"
                    />
                </div>

            </div>
        </form>

        <AppCallout
            v-if="calloutMessage && confirmClick"
            :text="calloutMessage"
            :type="calloutType"
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

const router = useRouter()
const MIN_PASSWORD_LENGTH = 8
const MIN_NAME_LENGTH = 3

// подготавливаем правила валидации
const rulesObject = {
    loginValue: {
        email: helpers.withMessage('Неверный формат e-mail', email),
        required: helpers.withMessage('Поле обязательно для заполнения', required),
    },
    passwordValue: {
        required: helpers.withMessage('Поле обязательно для заполнения', required),
        minLength: helpers.withMessage(`Минимальная длина пароля - ${MIN_PASSWORD_LENGTH} символов`, minLength(MIN_PASSWORD_LENGTH)),
    },
}

// добавляем правила, если это форма регистрации
if (props.isRegister) {
    rulesObject.passwordСonfirmationValue = {}
    rulesObject.passwordСonfirmationValue.required = helpers.withMessage('Поле обязательно для заполнения', required)
    rulesObject.passwordСonfirmationValue.sameAsPassword = helpers.withMessage('Пароли не совпадают', sameAs(passwordValue))

    rulesObject.nameValue = {}
    rulesObject.nameValue.required = helpers.withMessage('Поле обязательно для заполнения', required)
    rulesObject.nameValue.minLength = helpers.withMessage(`Минимальная длина имени - ${MIN_NAME_LENGTH} символа`, minLength(MIN_NAME_LENGTH))
}

const rules = computed(() => (rulesObject)) // оборачиваем в computed

// подготавливаем объект валидации
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


const currUser = reactive(new UserClass())        // определяем реактивный класс для вызова callout
const confirmClick = ref(false)            // определяем для вывода этого callout
const calloutMessage = ref('')             // определяем показываемое сообщение
const calloutType = ref('danger')          // определяем тип callout

const formSubmit = async (e) => {

    v$.value.$touch()                           // валидируем всю форму

    if (!v$.value.$error) {                     // это показатель ошибки

        let currUserTemp = null                     // для получения данных с сервера

        if (props.isRegister) {
            currUserTemp = user.registerUser({
                email: loginValue.value,
                name: nameValue.value,
                password: passwordValue.value,
                password_confirmation: passwordСonfirmationValue.value,
            })
        } else {
            currUserTemp = user.fetchUser({
                email: loginValue.value,
                password: passwordValue.value
            })
        }

        const userData = await currUserTemp

        Object.assign(currUser, userData)

        if (currUser.status === 200) {
            router.push({name: 'ui'})               // переход в сам dashboard todo потом изменить
        } else {
            confirmClick.value = true
            calloutType.value = 'danger'

            console.log(currUser)

            switch (currUser.status) {
                case 403:
                    calloutMessage.value = 'Пользователь заблокирован'
                    break
                case 404:
                    calloutMessage.value = 'Неверный пароль или имя пользователя'
                    break
                case 409:
                    calloutMessage.value = 'Пользователь с таким email уже существует'
                    break
                case 500:
                    calloutMessage.value = 'Неизвестная ошибка'
                    break
                case 201:
                    calloutMessage.value = 'Пользователь успешно зарегистрирован'
                    calloutType.value = 'success'
                    break

            }
        }

    }

}

</script>

<style scoped>
.main-container {
    @apply w-screen h-screen flex flex-col justify-center items-center bg-slate-200 z-50
}

.form-container {
    @apply w-[500px] border rounded-lg border-slate-500 shadow shadow-slate-700 bg-slate-300
}

.container {
    @apply flex justify-around items-center ml-2 mr-2 mt-2 font-semibold text-xl text-slate-600
}
</style>
