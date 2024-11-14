export default class UserClass {

    constructor(args = {}){
        this.id = args.id ?? 0
        this.name = args.name ?? undefined
        this.email = args.email ?? undefined
        this.role = args.role ?? undefined
        this.status = args.status ?? 0

    }

}
