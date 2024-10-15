import React from 'react';
import { connect, ConnectedProps } from 'react-redux';

import { IAlertNotificationData } from '@admin/utils/api/models/notifications/AlertNotification';
import notifications, { fetchFromApi } from '@admin/store/slices/notifications';

const connector = connect(
    ({ account, notifications }: RootState) => ({ account, notifications }),
    {
        fetchFromApi,
        setEchoNotifications: notifications.actions.setEchoNotifications
    },
);

interface INotificationProviderProps {
    delay?: number;
}

type NotificationProviderProps = INotificationProviderProps & ConnectedProps<typeof connector> & React.PropsWithChildren;

const NotificationProvider: React.FC<NotificationProviderProps> = ({
    delay,
    account,
    setEchoNotifications,
    fetchFromApi,
    notifications: {
        echoNotifications
    },
    children
}) => {
    const loadFromEcho = React.useCallback((onAlert: (alert: IAlertNotificationData) => void) => {
        if (!account.user) {
            logger.info('No user found. Unable to load alerts from Echo.');

            return;
        }

        const channel = window.EchoWrapper.private(`alerts.${account.user.user.uuid}`);

        channel.listen('.Alert', onAlert);

        return channel;
    }, [account]);

    const handleListenedAlert = React.useCallback((alert: IAlertNotificationData) => {
        setEchoNotifications(echoNotifications.concat(alert));
    }, [setEchoNotifications, echoNotifications]);

    React.useEffect(() => {
        const echoChannel = loadFromEcho(handleListenedAlert);

        fetchFromApi();

        const timer = delay ? window.setInterval(fetchFromApi, delay) : undefined;

        return () => {
            echoChannel?.stopListening('.Alert', handleListenedAlert);

            window.clearInterval(timer);
        };
    }, [delay, loadFromEcho, fetchFromApi, handleListenedAlert])

    return (
        <>
            {children}
        </>
    );
}

export default connector(NotificationProvider);
export { NotificationProviderProps };
