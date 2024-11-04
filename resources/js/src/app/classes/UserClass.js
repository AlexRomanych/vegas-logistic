export default class UserClass {

    constructor(args = {}){
        this.id = args.id ?? undefined
        this.name = args.name ?? undefined
        this.email = args.email ?? undefined
        this.role = args.role ?? undefined

    }

}
