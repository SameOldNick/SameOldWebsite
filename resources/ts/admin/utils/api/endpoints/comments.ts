import { DateTime } from "luxon";

import { createAuthRequest } from "../factories";
import Comment from "../models/Comment";

export enum CommentStatuses {
    Awaiting = 'awaiting',
    Approved = 'approved',
    Denied = 'denied',
    All = 'all'
}

interface ICommentFilters {
    show?: CommentStatuses;
    article?: number;
    user?: number;
}

/**
 * Loads all comments
 * @param filters
 * @returns Paginated response of IComment objects
 */
export const loadAll = async (filters: ICommentFilters = {}) => {
    const { show, article, user } = filters;

    const response = await createAuthRequest().get<IPaginateResponseCollection<IComment>>('blog/comments', {
        show,
        article,
        user
    });

    return response.data;
}

/**
 * Loads a comment
 * @param id Comment ID
 * @returns Comment instance
 */
export const loadOne = async (id: number) => {
    const response = await createAuthRequest().get<IComment>(`blog/comments/${id}`);

    return new Comment(response.data);
}

/**
 * Updates a comment
 * @param comment Comment instance
 * @param title New title
 * @param content New content
 * @returns Updated Comment instance
 */
export const update = async (comment: Comment, title: string | null, content: string) => {
    const response = await createAuthRequest().put<IComment>(`blog/comments/${comment.comment.id}`, {
        title,
        comment: content
    });

    return new Comment(response.data);
}

/**
 * Approves a comment
 * @param comment Comment instance
 * @param dateTime When comment is updated (current date/time is used if null)
 * @returns Updated Comment instance
 */
export const approve = async (comment: Comment, dateTime?: DateTime) => {
    const response = await createAuthRequest().post<IComment>(`blog/comments/${comment.comment.id}/approve`, {
        approved_at: dateTime !== undefined ? dateTime.toISO() : undefined
    });

    return new Comment(response.data);
}

/**
 * Denies a comment
 * @param comment Comment instance
 * @returns Success message
 */
export const deny = async (comment: Comment) => {
    const response = await createAuthRequest().delete<Record<'success', string>>(`blog/comments/${comment.comment.id}`);

    return response.data;
}
