import { createAuthRequest } from "@admin/utils/api/factories";

export const loadTechnologies = async () => {
    const response = await createAuthRequest().get<ITechnology[]>('technologies');

    return response.data;
}

export const addTechnology = async (technology: ITechnology) => {
    const response = await createAuthRequest().post('technologies', technology);

    return response.data;
}

export const updateTechnology = async (technology: ITechnology) => {
    const response = await createAuthRequest().put(`technologies/${technology.id}`, technology);

    return response.data;
}

export const deleteTechnology = async (technologyId: number) => {
    const response = await createAuthRequest().delete<Record<'success', string>>(`technologies/${technologyId}`);

    return response.data;
}
