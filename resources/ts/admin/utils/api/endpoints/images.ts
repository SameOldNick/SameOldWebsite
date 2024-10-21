import { createAuthRequest } from "@admin/utils/api/factories";

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
