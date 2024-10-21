import { createAuthRequest } from "../factories";

/**
 * Checks if notification has type
 * @param notification
 * @param type
 * @returns True or false
 */
export const isNotificationType = <T extends INotification>(notification: INotification, type: string): notification is T => notification.type === type;

/**
 * Notification filters
 *
 * @export
 * @interface INotificationFilters
 */
export interface INotificationFilters {
    show?: 'read' | 'unread' | 'all';
    type?: string;
}

/**
 * Gets all notifications
 * @returns Array of INotification objects
 */
export const all = async (filters: INotificationFilters = {}) => {
    const response = await createAuthRequest().get<INotification[]>('/user/notifications', filters);

    return response.data;
}

/**
 * Gets read notifications
 * @returns Array of INotification objects
 */
export const read = async () => {
    const response = await createAuthRequest().get<INotification[]>('/user/notifications/read');

    return response.data;
}

/**
 * Gets unread notifications
 * @returns Array of INotification objects
 */
export const unread = async () => {
    const response = await createAuthRequest().get<INotification[]>('/user/notifications/unread');

    return response.data;
}

/**
 * Gets single notification
 * @param notification Notification UUID
 * @returns INotification
 */
export const single = async (notification: string) => {
    const response = await createAuthRequest().get<INotification>(`/user/notifications/${notification}`);

    return response.data;
}

/**
 * Marks notification as read
 * @param notification Notification UUID
 * @returns INotification
 */
export const markRead = async (notification: string) => {
    const response = await createAuthRequest().post<INotification>(`/user/notifications/${notification}/read`, {});

    return response.data;
}

/**
 * Marks notification as unread
 * @param notification Notification UUID
 * @returns INotification
 */
export const markUnread = async (notification: string) => {
    const response = await createAuthRequest().post<INotification>(`/user/notifications/${notification}/unread`, {});

    return response.data;
}

/**
 * Bulk update notifications
 * @param data Update data
 * @returns INotification[]
 */
export const bulkUpdate = async (data: { notifications: Array<{ id: string; read_at: string | null; }> }) => {
    const response = await createAuthRequest().post<INotification[]>(`/user/notifications`, data);

    return response.data;
}

/**
 * Deletes notification
 * @param notification Notification UUID
 * @returns INotification
 */
export const destroy = async (notification: string) => {
    const response = await createAuthRequest().delete<INotification>(`/user/notifications/${notification}`);

    return response.data;
}
