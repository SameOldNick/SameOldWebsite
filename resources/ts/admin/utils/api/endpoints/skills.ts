import { createAuthRequest } from "@admin/utils/api/factories";

export const loadSkills = async () => {
    const response = await createAuthRequest().get<ISkill[]>('skills');

    return response.data;
}

export const addSkill = async (skill: ISkill) => {
    const response = await createAuthRequest().post('skills', skill);

    return response.data;
}

export const updateSkill = async (skill: ISkill) => {
    const response = await createAuthRequest().put(`skills/${skill.id}`, skill);

    return response.data;
}

export const deleteSkill = async (skillId: number) => {
    const response = await createAuthRequest().delete<Record<'success', string>>(`skills/${skillId}`);

    return response.data;
}
