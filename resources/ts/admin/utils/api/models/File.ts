import { DateTime } from "luxon";
import { humanReadableFileSize } from "@admin/utils";

export default class File {
    constructor(
        public readonly file: IFile
    ) {
    }

    /**
     * Gets the name of the file.
     *
     * @readonly
     * @type {string}
     * @memberof File
     */
    public get name(): string {
        return this.file.name;
    }

    /**
     * Gets the file mime type.
     *
     * @readonly
     * @type {string}
     * @memberof File
     */
    public get mimeType(): string {
        return this.file.meta.mime_type;
    }

    /**
     * Gets size of file in human readable format.
     *
     * @readonly
     * @type {string}
     * @memberof File
     */
    public get sizeHumanReadable(): string {
        return humanReadableFileSize(this.file.meta.size);
    }

    /**
     * Gets when file was created.
     *
     * @readonly
     * @memberof File
     */
    public get createdAt() {
        return DateTime.fromISO(this.file.created_at);
    }

    /**
     * Gets when file was updated.
     *
     * @readonly
     * @memberof File
     */
    public get updatedAt() {
        return this.file.updated_at ? DateTime.fromISO(this.file.updated_at) : null;
    }
}
