import { DateTime } from "luxon";

import File from "./File";
import { TMarkdownImage } from "@admin/components/blog/articles/editor/form/controls/article-form/Content";

export default class Image {

    /**
     * Gets File associated with Image.
     *
     * @type {File}
     * @memberof Comment
     */
    public readonly file: File;

    /**
     * Creates an instance of Image.
     * @param {IImage} image
     * @memberof Image
     */
    constructor(
        public readonly image: IImage
    ) {
        this.file = new File(image.file);
    }

    /**
     * Gets image UUID
     *
     * @readonly
     * @memberof Image
     */
    public get uuid() {
        return this.image.uuid;
    }

    /**
     * Gets image description
     *
     * @readonly
     * @memberof Image
     */
    public get description() {
        return this.image.description;
    }

    /**
     * Gets the URL to the comment (on the main site)
     *
     * @readonly
     * @type {string}
     * @memberof Image
     */
    public get url(): string {
        return this.image.extra.url;
    }

    /**
     * Gets when image was created.
     *
     * @readonly
     * @memberof Image
     */
    public get createdAt() {
        return DateTime.fromISO(this.image.file.created_at);
    }

    /**
     * Gets when image was last updated.
     *
     * @readonly
     * @memberof Image
     */
    public get updatedAt() {
        return this.image.file.updated_at ? DateTime.fromISO(this.image.file.updated_at) : null;
    }

    /**
     * Transforms to markdown image object.
     *
     * @returns {TMarkdownImage}
     * @memberof Image
     */
    public toMarkdownImage(): TMarkdownImage {
        return {
            url: this.url,
            alt: this.image.description,
            title: this.image.file.name
        };
    }
}
