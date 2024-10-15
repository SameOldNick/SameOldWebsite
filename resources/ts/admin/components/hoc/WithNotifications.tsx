import React from 'react';
import { DateTime } from 'luxon';

import { useAppSelector } from '@admin/store/hooks';

export interface IStoredNotification {
    dateTime: DateTime;
    uuid: string;
    color: string;
    message: string;
    link?: string;
    readAt?: DateTime;
}

export interface IHasNotifications {
    notifications: IStoredNotification[];
}

export default function withNotifications<TProps extends IHasNotifications = IHasNotifications>(
    Component: React.ComponentType<TProps>
) {
    const WithNotificationsComponent: React.FC<Omit<TProps, keyof IHasNotifications>> = ({
        ...restProps
    }) => {
        const { notifications: {
            apiNotifications,
            echoNotifications
        } } = useAppSelector(({ notifications }) => ({ notifications }));

        // Memoize notifications
        const notifications = React.useMemo(() => {
            const notificationMap: Record<string, IStoredNotification> = {};

            // Process API notifications
            for (const model of apiNotifications) {
                const data = model.getData();
                notificationMap[data.id] = {
                    uuid: data.id,
                    message: data.message,
                    dateTime: model.createdAt,
                    color: data.color,
                    link: data.link || undefined,
                    readAt: model.readAt || undefined,
                };
            }

            // Process Echo notifications
            for (const notification of echoNotifications) {
                notificationMap[notification.id] = {
                    uuid: notification.id,
                    message: notification.message,
                    dateTime: DateTime.fromISO(notification.dateTime),
                    color: notification.color,
                    link: notification.link || undefined,
                    readAt: undefined,
                };
            }

            return Object.values(notificationMap);
        }, [apiNotifications, echoNotifications]);

        return <Component {...(restProps as TProps)} notifications={notifications} />;
    };

    WithNotificationsComponent.displayName = `withNotifications(${Component.displayName || Component.name})`;

    return WithNotificationsComponent;
}
