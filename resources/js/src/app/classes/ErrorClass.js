export default class ErrorClass extends Error {

    constructor(args) {

        // console.log(args)

        const {name, code, message, timeout} = args || {}
        const workMessage = message || 'Произошла ошибка'

        super(workMessage)

        this._message = workMessage
        this._name = name || 'Error'
        this._code = code || 0
        this._timeout = timeout || 0

    }

    // get name() {
    //     return this._name
    //
    // }

    get code() {
        return this._code
    }

    // get message() {
    //     return this._message
    // }

    get timeout() {
        return this._timeout
    }

    // set name(name) {
    //     this._name = name
    // }

    set code(code) {
        this._code = code
    }


    set timeout(timeout) {
        this._timeout = timeout
    }


}
