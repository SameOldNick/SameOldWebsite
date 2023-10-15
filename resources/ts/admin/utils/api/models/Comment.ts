import { DateTime } from "luxon";
import Article from "./Article";
import { generatePath } from "react-router-dom";
import User from "./User";

export type TCommentStatuses = 'awaiting' | 'approved' | 'denied';

export default class Comment {
    public static readonly STATUS_AWAITING: TCommentStatuses = 'awaiting';
    public static readonly STATUS_APPROVED: TCommentStatuses = 'approved';
    public static readonly STATUS_DENIED: TCommentStatuses = 'denied';

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
        if (this.deletedAt)
            return 'denied';
        else if (this.approvedAt)
            return 'approved';
        else
            return 'awaiting';
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

    /**
     * Gets when comment was approved (or null if not)
     *
     * @readonly
     * @memberof Comment
     */
    public get approvedAt() {
        return this.comment.approved_at ? DateTime.fromISO(this.comment.approved_at) : null;
    }

    /**
     * Gets who approved comment (or null if not)
     *
     * @readonly
     * @memberof Comment
     */
    public get approvedBy() {
        return this.comment.approved_by ? new User(this.comment.approved_by) : null;
    }

    /**
     * Gets who posted comment.
     *
     * @readonly
     * @memberof Comment
     */
    public get postedBy() {
        return this.comment.post.user ? new User(this.comment.post.user) : null;
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
