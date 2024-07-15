import { createAuthRequest } from "@admin/utils/api/factories";
import Comment from "@admin/utils/api/models/Comment";

export enum CommentStatuses {
    Approved = 'approved',
    Denied = 'denied',
    Flagged = 'flagged',
    AwaitingVerification = 'awaiting_verification',
    AwaitingApproval = 'awaiting_approval',
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
 * @param status New status
 * @returns Updated Comment instance
 */
export const update = async (comment: Comment, params: { title?: string; content?: string; status?: TCommentStatuses; }) => {
    const response = await createAuthRequest().put<IComment>(`blog/comments/${comment.comment.id}`, {
        title: params.title ?? undefined,
        comment: params.content ?? undefined,
        status: params.status ?? undefined,
    });

    return new Comment(response.data);
}
