import React from 'react';
import { Button, ListGroupItem } from 'reactstrap';

import { IStoredNotification } from '../hoc/WithNotifications';

import { DateTime } from 'luxon';
import { FaEnvelope, FaEnvelopeOpen } from 'react-icons/fa';

interface INotificationItem {
    uuid: string;
    message: string;
    color: string;
    dateTime: DateTime;
    link?: string;
    readAt?: DateTime;
}

interface NotificationItemProps {
    notification: IStoredNotification;
    selected: boolean;
    onSelect: (previous: boolean) => void;
    onMarkClicked: () => void;
}

const NotificationItem: React.FC<NotificationItemProps> = ({
    notification: { color, message, dateTime, readAt, link },
    selected,
    onSelect,
    onMarkClicked
}) => {
    return (
        <>
            <ListGroupItem color={!readAt ? color : undefined} className='d-flex justify-content-between align-items-center'>
                <div className="d-flex align-items-center">
                    <div>
                        <span className='visually-hidden'>Select notification</span>
                        <input type="checkbox" className="me-2" checked={selected} onClick={() => onSelect(selected)} />
                    </div>
                    <div>
                        <strong>{message}</strong>
                        <br />
                        <small className="text-muted">Sent: {dateTime.toRelative()}</small>
                    </div>
                </div>
                <div className='d-flex gap-2'>
                    {link && (
                        <Button size="sm" color="info" onClick={() => window.open(link, '_blank')}>
                            Open Link
                        </Button>
                    )}
                    <Button size="sm" color={readAt ? "secondary" : "primary"} onClick={onMarkClicked}>
                        {readAt ? <FaEnvelope /> : <FaEnvelopeOpen />}{' '}
                        {readAt ? "Mark Unread" : "Mark Read"}
                    </Button>
                </div>
            </ListGroupItem>
        </>
    );
}

export default NotificationItem;
export { INotificationItem, NotificationItemProps };
