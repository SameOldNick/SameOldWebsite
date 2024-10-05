import { DateTime } from "luxon";

import File from "./File";

export default class Backup {
    constructor(
        public readonly backup: IBackup
    ) {
    }

    /**
     * Gets the backup status.
     *
     * @readonly
     * @type {TBackupStatuses}
     * @memberof Backup
     */
    public get status() {
        return this.backup.status;
    }

    /**
     * Gets the error message (if any).
     *
     * @readonly
     * @type {(string | null)}
     * @memberof Backup
     */
    public get errorMessage(): string | null {
        return this.backup.error_message || null;
    }

    /**
     * Gets the file associated with the backup.
     *
     * @readonly
     * @type {(File | null)}
     * @memberof Backup
     */
    public get file(): File | null {
        return this.backup.file ? new File(this.backup.file) : null;
    }

    /**
     * Gets when backup was created.
     *
     * @readonly
     * @memberof Backup
     */
    public get createdAt() {
        return DateTime.fromISO(this.backup.created_at);
    }

    /**
     * Gets when backup was updated.
     *
     * @readonly
     * @memberof Backup
     */
    public get updatedAt() {
        return this.backup.updated_at ? DateTime.fromISO(this.backup.updated_at) : null;
    }
}
