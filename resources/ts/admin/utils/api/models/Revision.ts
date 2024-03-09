import { DateTime } from "luxon";

export default class Revision {
    constructor(
        public readonly revision: IRevision
    ) {
    }

    /**
     * Gets when revision was created.
     *
     * @readonly
     * @memberof Revision
     */
    public get createdAt() {
        return DateTime.fromISO(this.revision.created_at);
    }

}
