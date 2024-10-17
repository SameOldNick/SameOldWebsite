import React from 'react';
import { Button, Toast, ToastBody, ToastHeader } from 'reactstrap';
import { NavLink as ReactRouterNavLink } from 'react-router-dom';

import { DateTime } from 'luxon';

import { IAlertNotificationData } from '@admin/utils/api/models/notifications/AlertNotification';

interface NotificationToastProps {
    notification: IAlertNotificationData;
}

const NotificationToast: React.FC<NotificationToastProps> = ({
    notification: {
        message,
        dateTime: dateTimeIso
    }
}) => {
    const [open, setOpen] = React.useState(true);

    const dateTime = React.useMemo(() => DateTime.fromISO(dateTimeIso), [dateTimeIso]);

    const handleToggle = React.useCallback(() => setOpen((prev) => !prev), [setOpen]);

    return (
        <>
            <Toast isOpen={open}>
                <ToastHeader tag='div' tagClassName='d-flex justify-content-between w-100' toggle={handleToggle}>
                    <strong className="">New Notification</strong>
                    <small className="text-muted" title={dateTime.toLocaleString(DateTime.DATETIME_MED)}>{dateTime.toRelative()}</small>
                </ToastHeader>
                <ToastBody>
                    <p>{message}</p>
                    <Button tag='a' href='/admin/notifications' size='sm' color='primary'>View Notifications</Button>
                </ToastBody>
            </Toast>
        </>
    );
}

export default NotificationToast;
export { NotificationToastProps };
