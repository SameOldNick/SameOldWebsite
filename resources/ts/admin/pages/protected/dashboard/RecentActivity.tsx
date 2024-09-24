import React from 'react';
import { FaEnvelope, FaEnvelopeOpen, FaExternalLinkAlt } from 'react-icons/fa';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Table } from 'reactstrap';

import classNames from 'classnames';
import moment from 'moment';

import Loader from '@admin/components/Loader';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';

import Article from '@admin/utils/api/models/Article';
import Comment from '@admin/utils/api/models/Comment';
import User from '@admin/utils/api/models/User';
import Notification from '@admin/utils/api/models/notifications/Notification';
import ActivityNotification, { ActivityEvent } from '@admin/utils/api/models/notifications/ActivityNotification';

import { all, markRead, markUnread } from '@admin/utils/api/endpoints/notifications';

interface IRecentActivityProps {
}

interface IActivityRowProps {
    activity: ActivityNotification;
    onMarkReadClicked: (notification: ActivityNotification) => void;
    onMarkUnreadClicked: (notification: ActivityNotification) => void;
}

const ActivityRow: React.FC<IActivityRowProps> = ({ activity, onMarkReadClicked, onMarkUnreadClicked }) => {
    const { event, dateTime, message, context } = activity.getData();

    const [dropdownOpen, setDropdownOpen] = React.useState(false);

    const items = React.useMemo(() => {
        const elements: JSX.Element[] = [];

        if ([ActivityEvent.UserRegistered].includes(event) && 'user' in context) {
            const user = new User(context.user as IUser);

            elements.push(
                <DropdownItem title='View User' href={user.generatePath()}>
                    <FaExternalLinkAlt />{' '}View User
                </DropdownItem>
            );
        } else if ([
            ActivityEvent.ArticleCreated,
            ActivityEvent.ArticlePublished,
            ActivityEvent.ArticleScheduled,
            ActivityEvent.ArticleUnpublished,
            ActivityEvent.ArticleDeleted
        ].includes(event) && 'article' in context) {
            const article = new Article(context.article as IArticle);

            elements.push(
                <DropdownItem title='View Article' href={article.generatePath()}>
                    <FaExternalLinkAlt />{' '}View Article
                </DropdownItem>
            );
        } else if ([ActivityEvent.CommentCreated].includes(event) && 'comment' in context) {
            const comment = new Comment(context.comment as IComment);

            elements.push(
                <DropdownItem title='View Comment' href={comment.generatePath()}>
                    <FaExternalLinkAlt />{' '}View Comment
                </DropdownItem>
            );
        }

        if (activity.readAt === null) {
            elements.push(
                <DropdownItem onClick={() => onMarkReadClicked(activity)}>
                    <FaEnvelopeOpen />{' '}Mark Read
                </DropdownItem>
            );
        } else {
            elements.push(
                <DropdownItem onClick={() => onMarkUnreadClicked(activity)}>
                    <FaEnvelope />{' '}Mark Unread
                </DropdownItem>
            );
        }

        return elements;
    }, [event, activity]);

    return (
        <>
            <tr className={classNames({ 'table-light': activity.readAt === null })}>
                <th scope='row'>
                    <abbr title={moment(dateTime).fromNow()}>
                        {moment(dateTime).format('MM-DD-YYYY HH:mm')}
                    </abbr>
                </th>
                <td>{message}</td>
                <td>
                    <Dropdown isOpen={dropdownOpen} toggle={() => setDropdownOpen(!dropdownOpen)}>
                        <DropdownToggle caret color='primary'>Actions</DropdownToggle>
                        <DropdownMenu>
                            {items.map((item, index) => (
                                <React.Fragment key={index}>
                                    {item}
                                </React.Fragment>
                            ))}
                        </DropdownMenu>
                    </Dropdown>
                </td>
            </tr>
        </>
    );
}

const RecentActivity: React.FC<IRecentActivityProps> = ({ }) => {
    const waitToLoadRef = React.createRef<IWaitToLoadHandle>();

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
                                    <ActivityRow
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
