
import { createAuthRequest } from "@admin/utils/api/factories";

/**
 * Fetches backup settings
 * @returns Backup settings
 */
export const fetchSettings = async () => {
    const response = await createAuthRequest().get<IBackupSetting[]>('/backup/settings');

    return response.data;
}

/**
 * Updates backup settings
 * @param settings
 * @returns Updated settings
 */
export const updateSettings = async (settings: Record<string, string | string[] | null>) => {
    const response = await createAuthRequest().post<IBackupSetting[]>('/backup/settings', settings);

    return response.data;
}
