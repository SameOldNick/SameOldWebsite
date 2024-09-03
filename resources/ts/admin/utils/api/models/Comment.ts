import { generatePath } from "react-router-dom";
import { DateTime } from "luxon";

import Article from "./Article";
import Person from "./Person";

export default class Comment {

    /**
     * Gets Article associated with Comment.
     *
     * @type {Article}
     * @memberof Comment
     */
    public readonly article: Article;

    /**
     * Creates an instance of Comment.
     * @param {IComment} comment
     * @memberof Comment
     */
    constructor(
        public readonly comment: IComment
    ) {
        this.article = new Article(comment.article);
    }

    /**
     * Gets status of comment.
     *
     * @readonly
     * @type {TCommentStatuses}
     * @memberof Comment
     */
    public get status(): TCommentStatuses {
        return this.comment.status;
    }

    /**
     * Gets when comment was created.
     *
     * @readonly
     * @memberof Comment
     */
    public get createdAt() {
        return DateTime.fromISO(this.comment.post.created_at);
    }

    /**
     * Gets when comment was last updated.
     *
     * @readonly
     * @memberof Comment
     */
    public get updatedAt() {
        return DateTime.fromISO(this.comment.post.updated_at);
    }

    /**
     * Gets when comment was deleted (or null if not)
     *
     * @readonly
     * @memberof Comment
     */
    public get deletedAt() {
        return this.comment.post.deleted_at ? DateTime.fromISO(this.comment.post.deleted_at) : null;
    }

    public get commenterInfo() {
        return this.comment.commenter;
    }

    /**
     * Gets who posted comment.
     *
     * @readonly
     * @memberof Comment
     */
    public get postedBy() {
        return new Person(this.comment.post.person);
    }

    /**
     * Gets path to edit comment page.
     *
     * @returns
     * @memberof Comment
     */
    public generatePath() {
        if (!this.comment.id)
            throw new Error('Comment is missing ID.');

        return generatePath(`/admin/comments/edit/:comment`, {
            comment: this.comment.id.toString()
        });
    }

}
