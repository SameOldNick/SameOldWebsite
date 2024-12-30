import React from 'react';
import { Table } from 'reactstrap';

import Loader from '@admin/components/Loader';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';

import Notification from '@admin/utils/api/models/notifications/Notification';
import ActivityNotification from '@admin/utils/api/models/notifications/ActivityNotification';

import { all, markRead, markUnread } from '@admin/utils/api/endpoints/notifications';
import RecentActivityRow from './RecentActivityRow';

const RecentActivity: React.FC = () => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);

    const fetchRecentActivity = React.useCallback(async () => {
        const notifications = await all({ type: Notification.NOTIFICATION_TYPE_ACTIVITY });

        return notifications
            .map((record) => new ActivityNotification(record as any))
            .sort((a, b) => b.createdAt.diff(a.createdAt, 'seconds').seconds).slice(0, 5);
    }, []);

    const handleMarkReadClicked = React.useCallback(async (activity: ActivityNotification) => {
        await markRead(activity.id);

        await waitToLoadRef.current?.load();
    }, [waitToLoadRef.current]);

    const handleMarkUnreadClicked = React.useCallback(async (activity: ActivityNotification) => {
        await markUnread(activity.id);

        await waitToLoadRef.current?.load();
    }, [waitToLoadRef.current]);

    return (
        <>
            <WaitToLoad
                ref={waitToLoadRef}
                loading={<Loader display={{ type: 'over-element' }} />}
                callback={fetchRecentActivity}
            >
                {(activities, err) => (
                    <>
                        <Table>
                            <thead>
                                <tr>
                                    <th scope='col'>Date/time</th>
                                    <th scope='col'>Message</th>
                                    <th scope='col'>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {activities && activities.length === 0 && (
                                    <tr>
                                        <td colSpan={4}>(No recent activity)</td>
                                    </tr>
                                )}
                                {activities && activities.map((notification, index) => (
                                    <RecentActivityRow
                                        key={index}
                                        activity={notification}
                                        onMarkReadClicked={handleMarkReadClicked}
                                        onMarkUnreadClicked={handleMarkUnreadClicked}
                                    />
                                ))}

                            </tbody>
                        </Table>

                        {err && <p className="text-muted">(An error occurred)</p>}
                    </>
                )}
            </WaitToLoad>
        </>
    );
}

export default RecentActivity;
