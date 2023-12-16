import { createAuthRequest } from "../factories";

export const isNotificationType = <T extends INotification>(notification: INotification, type: string): notification is T => notification.type === type;

export const all = async () => {
    const response = await createAuthRequest().get<INotification[]>('/user/notifications');

    return response.data;
}

export const read = async () => {
    const response = await createAuthRequest().get<INotification[]>('/user/notifications/read');

    return response.data;
}

export const unread = async () => {
    const response = await createAuthRequest().get<INotification[]>('/user/notifications/unread');

    return response.data;
}

export const single = async (notification: string) => {
    const response = await createAuthRequest().get<INotification>(`/user/notifications/${notification}`);

    return response.data;
}

export const markRead = async (notification: string) => {
    const response = await createAuthRequest().post<INotification>(`/user/notifications/${notification}/read`, {});

    return response.data;
}

export const markUnread = async (notification: string) => {
    const response = await createAuthRequest().post<INotification>(`/user/notifications/${notification}/read`, {});

    return response.data;
}

export const destroy = async (notification: string) => {
    const response = await createAuthRequest().delete<INotification>(`/user/notifications/${notification}`);

    return response.data;
}
