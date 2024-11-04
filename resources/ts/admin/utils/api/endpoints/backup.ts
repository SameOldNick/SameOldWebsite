
import { createAuthRequest } from "@admin/utils/api/factories";

export interface BackupSettings {
    current_values: IBackupSetting[];
    possible_values: Record<string, string[]>;
}

/**
 * Fetches backup settings
 * @returns Backup settings
 */
export const fetchSettings = async () => {
    const response = await createAuthRequest().get<BackupSettings>('/backup/settings');

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
