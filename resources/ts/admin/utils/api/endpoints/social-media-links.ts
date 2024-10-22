import { createAuthRequest } from "@admin/utils/api/factories";

export const loadSocialMediaLinks = async () => {
    const response = await createAuthRequest().get<ISocialMediaLink[]>('social-media');

    return response.data;
}

export const addSocialMediaLink = async (link: string) => {
    const response = await createAuthRequest().post<ISocialMediaLink[]>('social-media', { link });

    return response.data;
}

export const updateSocialMediaLink = async (linkId: number, link: string) => {
    const response = await createAuthRequest().put<ISocialMediaLink[]>(`social-media/${linkId}`, { link });

    return response.data;
}

export const deleteSocialMediaLink = async (linkId: number) => {
    const response = await createAuthRequest().delete<ISocialMediaLink[]>(`social-media/${linkId}`);

    return response.data;
}
