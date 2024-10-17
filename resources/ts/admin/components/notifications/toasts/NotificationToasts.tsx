import React from 'react';
import { connect, ConnectedProps } from 'react-redux';
import { Toast, ToastBody, ToastHeader } from 'reactstrap';

import notifications, { fetchFromApi } from '@admin/store/slices/notifications';
import NotificationToast from './NotificationToast';
import { IAlertNotificationData } from '@admin/utils/api/models/notifications/AlertNotification';

const connector = connect(
    ({ account, notifications }: RootState) => ({ account, notifications }),
    {
        fetchFromApi,
        setEchoNotifications: notifications.actions.setEchoNotifications
    },
);

type NotificationToastsProps = ConnectedProps<typeof connector>;

const NotificationToasts: React.FC<NotificationToastsProps> = ({
    notifications: {
        echoNotifications,
    }
}) => {
    const [displayed, setDisplayed] = React.useState<IAlertNotificationData[]>([]);

    React.useEffect(() => {
        const difference = displayed.filter((notification) => !echoNotifications.includes(notification));

        setDisplayed((prev) => [
            ...prev,
            ...difference
        ]);

    }, [echoNotifications]);

    return (
        <>
            <div className=''>
                <div className="toast-container position-fixed bottom-0 end-0 p-3">
                    {displayed.map((notification, index) => (
                        <NotificationToast
                            key={index}
                            notification={notification}
                        />
                    ))}
                </div>
            </div>
        </>
    );
}

export default connector(NotificationToasts);
export { NotificationToastsProps };
