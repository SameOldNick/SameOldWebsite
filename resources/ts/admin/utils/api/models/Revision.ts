import { DateTime } from "luxon";

export default class Revision {

    constructor(
        public readonly revision: IRevision
    ) {
    }

    public get createdAt() {
        return DateTime.fromISO(this.revision.created_at);
    }

}
