import { Tag } from "react-tag-autocomplete";
import { DateTime } from "luxon";

import { IMainImageNew } from "@admin/components/blog/article-form/main-image";
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
 * @param article Article to get tags
 * @returns Array of Tag instances
 */
export const loadTags = async ({ article }: Article): Promise<Tag[]> => {
    const response = await createAuthRequest().get<ITag[]>(`blog/articles/${article.id}/tags`);

    return response.data.map(({ slug, tag }) => ({
        value: slug,
        label: tag
    }));
}

/**
 * Upload main image for article
 * @param article Article
 * @param image Image
 * @returns IArticleImage instance
 */
export const uploadMainImage = async (article: Article, image: IMainImageNew) => {
    const data = new FormData();

    data.append('image', image.file);

    if (image.description)
        data.append('description', image.description);

    const response = await createAuthRequest().post<IArticleImage>(`blog/articles/${article.article.id}/images`, data);

    return response.data;
}

export const setMainImage = async (article: Article, articleImage: IArticleImage) => {
    const response = await createAuthRequest().post<IArticle>(`blog/articles/${article.article.id}/images/${articleImage.uuid}/main-image`, {});

    return new Article(response.data);
}

/**
 * Removes main image for article
 * @param article Article
 * @returns IArticle instance
 */
export const deleteMainImage = async (article: Article) => {
    const response = await createAuthRequest().delete<IArticle>(`blog/articles/${article.article.id}/main-image`);

    return new Article(response.data);
}

/**
 * Attaches tags to article
 * @param article Article
 * @param tags Tags
 * @returns Article tags
 */
export const attachTags = async (article: Article, tags: Tag[]): Promise<ITag[]> => {
    const response = await createAuthRequest().post(`blog/articles/${article.article.id}/tags`, {
        tags: tags.map((tag) => tag.label)
    });

    return response.data;
}

/**
 * Syncs tags to article
 * @param article Article
 * @param tags Tags
 * @returns Article tags
 */
export const syncTags = async (article: Article, tags: Tag[]): Promise<ITag[]> => {
    const response = await createAuthRequest().put(`blog/articles/${article.article.id}/tags`, {
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
 * @param article Article to update
 * @param title New title
 * @param slug New slug
 * @param publishedAt When to publish article or null to unpublish
 * @returns Article instance
 */
export const updateArticle = async (article: Article, title: string, slug: string, publishedAt: DateTime | null): Promise<Article> => {
    const response = await createAuthRequest().put<IArticle>(`blog/articles/${article.article.id}`, {
        title,
        slug,
        published_at: publishedAt ? publishedAt.toISO() : null
    });

    return new Article(response.data);

}

/**
 * Restores article
 * @param article Article to restore
 * @returns Object with success message
 */
export const restoreArticle = async (article: Article) => {
    const response = await createAuthRequest().post<Record<'success', string>>(`blog/articles/restore/${article.article.id}`, {});

    return response.data;
}

/**
 * Deletes article
 * @param article Article to delete
 * @returns Object with success message
 */
export const deleteArticle = async (article: Article) => {
    const response = await createAuthRequest().delete<Record<'success', string>>(`blog/articles/${article.article.id}`);

    return response.data;
}

/**
 * Creates revision for article
 * @param article Article
 * @param content Content
 * @param summary Summary of content
 * @param parentRevision Parent revision (if any)
 * @returns Revision instance
 */
export const createRevision = async (article: Article, content: string, summary: string | null, parentRevision?: Revision): Promise<Revision> => {
    const response = await createAuthRequest().post<IRevision>(`blog/articles/${article.article.id}/revisions`, {
        content,
        summary,
        parent: parentRevision && parentRevision.revision.uuid !== undefined ? parentRevision.revision.uuid : null
    });

    return new Revision(response.data);
}

/**
 * Sets current revision for article
 * @param article Article
 * @param revision Revision
 * @returns Current revision
 */
export const setCurrentRevision = async (article: Article, revision: Revision): Promise<Revision> => {
    const response = await createAuthRequest().post<IRevision>(`blog/articles/${article.article.id}/revision`, {
        revision: revision.revision.uuid
    });

    return new Revision(response.data);
}
