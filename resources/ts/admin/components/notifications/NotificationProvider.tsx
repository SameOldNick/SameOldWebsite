import React from 'react';
import { connect, ConnectedProps } from 'react-redux';

import { IAlertNotificationData } from '@admin/utils/api/models/notifications/AlertNotification';
import notifications, { fetchFromApi } from '@admin/store/slices/notifications';
import { EchoContext } from '@admin/utils/echo/context';

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
    const context = React.useContext(EchoContext);

    const loadFromEcho = React.useCallback((onAlert: (alert: IAlertNotificationData) => void) => {
        if (!account.user) {
            logger.info('No user found. Unable to load alerts from Echo.');

            return;
        }

        if (!context) {
            logger.info('Echo context is missing.');

            return;
        }

        const channel = context.echo.private(`users.${account.user.user.uuid}`);

        channel.listen('.Alert', onAlert);

        return channel;
    }, [account, context]);

    const handleListenedAlert = React.useCallback((alert: IAlertNotificationData) => {
        setEchoNotifications(echoNotifications.concat(alert));
    }, [setEchoNotifications, echoNotifications]);

    React.useEffect(() => {
        fetchFromApi();

        const timer = delay ? window.setInterval(fetchFromApi, delay) : undefined;

        return () => {
            window.clearInterval(timer);
        };
    }, [delay, fetchFromApi])

    React.useEffect(() => {
        const echoChannel = loadFromEcho(handleListenedAlert);

        return () => {
            echoChannel?.stopListening('.Alert', handleListenedAlert);
        };
    }, [loadFromEcho, handleListenedAlert])

    return (
        <>
            {children}
        </>
    );
}

export default connector(NotificationProvider);
export { NotificationProviderProps };
