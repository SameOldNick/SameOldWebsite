import { Tag } from "react-tag-autocomplete";
import { DateTime } from "luxon";

import { createAuthRequest } from "@admin/utils/api/factories";

import Article from "@admin/utils/api/models/Article";
import Revision from "@admin/utils/api/models/Revision";

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

/**
 * Uploads an image
 * @param file File to upload
 * @param description Description (if any)
 * @returns IImage object
 */
export const uploadImage = async (file: File, description?: string) => {
    const data = new FormData();

    data.append('image', file);

    if (description)
        data.append('description', description);

    const response = await createAuthRequest().post<IImage>(`images`, data);

    return response.data;
}

/**
 * Attaches article to image
 * @param articleId Article ID
 * @param imageUuid Image UUID
 * @returns IImage object
 */
export const attachImage = async (articleId: number, imageUuid: string) => {
    const response = await createAuthRequest().post<IImage>(`blog/articles/${articleId}/images/${imageUuid}`, {});

    return response.data;
}

/**
 * Detaches image from article
 * @param articleId Article ID
 * @param imageUuid Image UUID
 * @returns True
 */
export const detachImage = async (articleId: number, imageUuid: string) => {
    const response = await createAuthRequest().delete(`blog/articles/${articleId}/images/${imageUuid}`, {});

    return true;
}

/**
 * Sets main image for article
 * @param articleId
 * @param imageUuid
 * @returns Article object
 */
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

/**
 * Creates article
 * @param title Title
 * @param slug Slug
 * @param content Initial content
 * @param summary Summary
 * @param publishedAt When article is published
 * @returns Article object
 */
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
