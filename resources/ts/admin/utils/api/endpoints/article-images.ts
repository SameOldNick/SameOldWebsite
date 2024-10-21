import { createAuthRequest } from "@admin/utils/api/factories";

import Article from "@admin/utils/api/models/Article";

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
