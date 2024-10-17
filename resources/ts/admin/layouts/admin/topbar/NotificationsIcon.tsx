import React from 'react';
import { FaBell } from 'react-icons/fa';
import { Badge, NavItem, NavLink } from 'reactstrap';
import { NavLink as ReactRouterNavLink } from 'react-router-dom';

import withNotifications, { IHasNotifications } from '@admin/components/hoc/WithNotifications';

const NotificationsIcon: React.FC<IHasNotifications> = ({ notifications }) => {
    const displayCount = React.useMemo(() => {
        const unreadCount = notifications.filter((notification) => !notification.readAt).length;

        if (unreadCount === 0)
            return undefined;

        return unreadCount > 9 ? '9+' : `${unreadCount}`;
    }, [notifications]);

    return (
        <>
            <NavItem className='mx-1'>
                <NavLink tag={ReactRouterNavLink} to='notifications'>
                    <span className='position-relative'>
                        <FaBell className='fa-fw' />
                        {/* Counter - Alerts */}
                        {displayCount && (
                            <Badge pill color='danger' className='position-absolute top-0 start-100 translate-middle'>
                                {displayCount}
                                <span className="visually-hidden">unread alerts</span>
                            </Badge>
                        )}
                    </span>
                </NavLink>
            </NavItem>
        </>
    );
}

export default withNotifications(NotificationsIcon);
