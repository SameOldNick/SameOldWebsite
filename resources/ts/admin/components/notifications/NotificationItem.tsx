import React from 'react';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';

import { IStoredNotification } from '../hoc/WithNotifications';

import { DateTime } from 'luxon';
import { FaEnvelope, FaEnvelopeOpen, FaExternalLinkAlt } from 'react-icons/fa';
import classNames from 'classnames';

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
    notification: { message, dateTime, readAt, link },
    selected,
    onSelect,
    onMarkClicked
}) => {
    const [dropdownOpen, setDropdownOpen] = React.useState(false);

    const colClassNames = React.useMemo(() => classNames({ 'fw-bold': !readAt }), [readAt]);

    return (
        <>
            <tr>
                <td>
                    <span className='visually-hidden'>Select notification</span>
                    <input type="checkbox" checked={selected} onChange={() => onSelect(selected)} />
                </td>
                <td className={colClassNames}>{message}</td>
                <td className={colClassNames} title={dateTime.toLocaleString(DateTime.DATETIME_FULL)}>
                    {dateTime.toRelative()}
                </td>
                <td className={colClassNames} title={readAt?.toLocaleString(DateTime.DATETIME_FULL)}>
                    {readAt?.toRelative() ?? 'N/A'}
                </td>
                <td>
                    <Dropdown isOpen={dropdownOpen} toggle={() => setDropdownOpen((prev) => !prev)}>
                        <DropdownToggle caret color='primary'>Actions</DropdownToggle>
                        <DropdownMenu>
                            {link && (
                                <DropdownItem onClick={() => window.open(link, '_blank')}>
                                    <FaExternalLinkAlt />{' '}
                                    Open Link
                                </DropdownItem>
                            )}
                            <DropdownItem onClick={onMarkClicked}>
                                {readAt ? <FaEnvelope /> : <FaEnvelopeOpen />}{' '}
                                {readAt ? "Mark Unread" : "Mark Read"}
                            </DropdownItem>
                        </DropdownMenu>
                    </Dropdown>
                </td>
            </tr>
        </>
    );
}

export default NotificationItem;
export { INotificationItem, NotificationItemProps };
