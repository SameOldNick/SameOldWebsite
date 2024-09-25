import React from 'react';
import { FaEnvelope, FaEnvelopeOpen, FaExternalLinkAlt } from 'react-icons/fa';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';

import classNames from 'classnames';
import moment from 'moment';

import Article from '@admin/utils/api/models/Article';
import Comment from '@admin/utils/api/models/Comment';
import User from '@admin/utils/api/models/User';

import ActivityNotification, { ActivityEvent } from '@admin/utils/api/models/notifications/ActivityNotification';

interface IRecentActivityRowProps {
    activity: ActivityNotification;
    onMarkReadClicked: (notification: ActivityNotification) => void;
    onMarkUnreadClicked: (notification: ActivityNotification) => void;
}

const RecentActivityRow: React.FC<IRecentActivityRowProps> = ({ activity, onMarkReadClicked, onMarkUnreadClicked }) => {
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

export default RecentActivityRow;
