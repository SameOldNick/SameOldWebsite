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
 *
 * @param articleId
 * @returns
 * @exports
 */
export const loadArticle = async (articleId: number) => {
    const response = await createAuthRequest().get<IArticle>(`blog/articles/${articleId}`);

    return new Article(response.data);
}

/**
 *
 * @param articleId
 * @param revisionId
 * @returns
 * @exports
 */
export const loadRevision = async (articleId: number, revisionId: string) => {
    const response = await createAuthRequest().get<IRevision>(`blog/articles/${articleId}/revisions/${revisionId}`);

    return new Revision(response.data);
}

export interface ICreateArticleParams {
    title: string;
    slug: string;
    content: string;
    summary: string | null;
    publishedAt: DateTime | null;
    mainImage?: {
        image: File;
        description?: string;
    };
    images?: string[];
    tags?: string[];
}

/**
 * Creates article
 * @param params
 * @returns Article object
 */
export const createArticle = async ({
    title,
    slug,
    content,
    summary,
    publishedAt,
    mainImage,
    images,
    tags
}: ICreateArticleParams): Promise<Article> => {
    const formData = new FormData();

    // Add basic string fields
    formData.append('title', title);
    formData.append('slug', slug);
    formData.append('content', content);

    // Add summary if available
    if (summary !== null) {
        formData.append('summary', summary);
    }

    // Add published date if available, convert it to ISO string
    if (publishedAt !== null) {
        formData.append('published_at', publishedAt.toISO() ?? '');
    }

    // Add revision content
    formData.append('revision[content]', content);

    // Add revision summary (if exists)
    if (summary) {
        formData.append('revision[summary]', summary);
    }

    // Handle main image as an object
    if (mainImage) {
        formData.append('main_image[image]', mainImage.image);  // Add the image file
        if (mainImage.description) {
            formData.append('main_image[description]', mainImage.description);  // Add image description (optional)
        }
    }

    // Add images array if provided (e.g., additional image URLs or metadata)
    if (images) {
        images.forEach((image, index) => {
            formData.append(`images[${index}]`, image);
        });
    }

    // Add tags array if provided
    if (tags) {
        tags.forEach((tag, index) => {
            formData.append(`tags[${index}]`, tag);
        });
    }

    const response = await createAuthRequest().post<IArticle>('blog/articles', formData);

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
