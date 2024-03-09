import { Tag } from "react-tag-autocomplete";
import { DateTime } from "luxon";

import { createAuthRequest } from "../factories";

import Article from "../models/Article";
import Revision from "../models/Revision";

export enum ArticleStatuses {
    unpublished = 'unpublished',
    published = 'published',
    scheduled = 'scheduled',
    removed = 'removed',
    all = 'all'
}

export const fetchArticles = async (show?: ArticleStatuses) => {
    const response = await createAuthRequest().get<IPaginateResponseCollection<IArticle>>('blog/articles', { show });

    return response.data;
}

/**
 * Loads tags for article
 * @param articleId Article ID to get tags
 * @returns Array of Tag instances
 */
export const loadTags = async (articleId: number): Promise<Tag[]> => {
    const response = await createAuthRequest().get<ITag[]>(`blog/articles/${articleId}/tags`);

    return response.data.map(({ slug, tag }) => ({
        value: slug,
        label: tag
    }));
}

export const uploadImage = async (file: File, description?: string) => {
    const data = new FormData();

    data.append('image', file);

    if (description)
        data.append('description', description);

    const response = await createAuthRequest().post<IImage>(`images`, data);

    return response.data;
}



export const attachImage = async (articleId: number, imageUuid: string) => {
    const response = await createAuthRequest().post<IImage>(`blog/articles/${articleId}/images/${imageUuid}`, {});

    return response.data;
}

export const detachImage = async (articleId: number, imageUuid: string) => {
    const response = await createAuthRequest().delete(`blog/articles/${articleId}/images/${imageUuid}`, {});

    return true;
}

export const setMainImage = async (articleId: number, imageUuid: string) => {
    const response = await createAuthRequest().post<IArticle>(`blog/articles/${articleId}/images/${imageUuid}/main-image`, {});

    return new Article(response.data);
}

/**
 * Removes main image for article
 * @param article Article
 * @returns IArticle instance
 */
export const unsetMainImage = async (articleId: number) => {
    const response = await createAuthRequest().delete<IArticle>(`blog/articles/${articleId}/main-image`);

    return new Article(response.data);
}

/**
 * Attaches tags to article
 * @param article Article
 * @param tags Tags
 * @returns Article tags
 */
export const attachTags = async (articleId: number, tags: Tag[]): Promise<ITag[]> => {
    const response = await createAuthRequest().post(`blog/articles/${articleId}/tags`, {
        tags: tags.map((tag) => tag.label)
    });

    return response.data;
}

/**
 * Syncs tags to article
 * @param articleId Article ID
 * @param tags Tags
 * @returns Article tags
 */
export const syncTags = async (articleId: number, tags: Tag[]): Promise<ITag[]> => {
    const response = await createAuthRequest().put(`blog/articles/${articleId}/tags`, {
        tags: tags.map((tag) => tag.label)
    });

    return response.data;
}

export const createArticle = async (title: string, slug: string, content: string, summary: string | null, publishedAt: DateTime | null): Promise<Article> => {
    const response = await createAuthRequest().post<IArticle>('blog/articles', {
        title,
        slug,
        published_at: publishedAt ? publishedAt.toISO() : null,
        revision: {
            content,
            summary
        }
    });

    return new Article(response.data);

}

/**
 * Updates article meta data
 * @param articleId Article ID to update
 * @param title New title
 * @param slug New slug
 * @param publishedAt When to publish article or null to unpublish
 * @returns Article instance
 */
export const updateArticle = async (articleId: number, title: string, slug: string, publishedAt: DateTime | null): Promise<Article> => {
    const response = await createAuthRequest().put<IArticle>(`blog/articles/${articleId}`, {
        title,
        slug,
        published_at: publishedAt ? publishedAt.toISO() : null
    });

    return new Article(response.data);

}

/**
 * Restores article
 * @param articleId Article ID to restore
 * @returns Object with success message
 */
export const restoreArticle = async (articleId: number) => {
    const response = await createAuthRequest().post<Record<'success', string>>(`blog/articles/restore/${articleId}`, {});

    return response.data;
}

/**
 * Deletes article
 * @param articleId Article ID to delete
 * @returns Object with success message
 */
export const deleteArticle = async (articleId: number) => {
    const response = await createAuthRequest().delete<Record<'success', string>>(`blog/articles/${articleId}`);

    return response.data;
}

/**
 * Creates revision for article
 * @param articleId Article ID
 * @param content Content
 * @param summary Summary of content
 * @param parentRevisionUuid Parent revision (if any)
 * @returns Revision instance
 */
export const createRevision = async (articleId: number, content: string, summary: string | null, parentRevisionUuid?: string): Promise<Revision> => {
    const response = await createAuthRequest().post<IRevision>(`blog/articles/${articleId}/revisions`, {
        content,
        summary,
        parent: parentRevisionUuid
    });

    return new Revision(response.data);
}

/**
 * Sets current revision for article
 * @param article Article
 * @param revision Revision
 * @returns Current revision
 */
export const setCurrentRevision = async (articleId: number, revisionUuid: string): Promise<Revision> => {
    const response = await createAuthRequest().post<IRevision>(`blog/articles/${articleId}/revision`, {
        revision: revisionUuid
    });

    return new Revision(response.data);
}
