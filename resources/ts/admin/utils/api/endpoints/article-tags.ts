import { createAuthRequest } from "@admin/utils/api/factories";

import { Tag } from "react-tag-autocomplete";

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
