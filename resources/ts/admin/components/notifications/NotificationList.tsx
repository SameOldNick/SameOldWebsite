import React from 'react';
import { Button, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Form, Input, Label, Row, Table } from 'reactstrap';
import { FaEnvelope, FaEnvelopeOpen, FaSync, FaToolbox, FaTrash } from 'react-icons/fa';
import { connect, ConnectedProps } from 'react-redux';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import { DateTime } from 'luxon';

import NotificationItem, { INotificationItem } from './NotificationItem';
import withNotifications, { IHasNotifications } from '@admin/components/hoc/withNotifications';
import Loader from '@admin/components/Loader';

import { bulkDestroy, bulkUpdate, markRead, markUnread } from '@admin/utils/api/endpoints/notifications';
import { fetchFromApi } from '@admin/store/slices/notifications';
import createErrorHandler from '@admin/utils/errors/factory';

const connector = connect(
    null,
    { fetchFromApi }
);

type NotificationListProps = ConnectedProps<typeof connector> & IHasNotifications;

const NotificationList: React.FC<NotificationListProps> = ({ notifications, fetchFromApi }) => {
    const [renderCount, setRenderCount] = React.useState(1);
    const [sortBy, setSortBy] = React.useState('newest_oldest');
    const [show, setShow] = React.useState('both');
    const [selected, setSelected] = React.useState<string[]>([]);
    const [showDropdown, setShowDropdown] = React.useState(false);
    const [loading, setLoading] = React.useState(false);

    const refresh = React.useCallback(() => {
        return fetchFromApi();
    }, [fetchFromApi]);

    const handleUpdateFormSubmitted = React.useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        try {
            setLoading(true);

            await refresh();
        } catch (err) {
            logger.error(err);
        } finally {
            setLoading(false);
        }

    }, [refresh]);

    const handleMarkAsClicked = React.useCallback(async (notification: INotificationItem) => {
        try {
            setLoading(true);

            if (notification.readAt) {
                await markUnread(notification.uuid);
            } else {
                await markRead(notification.uuid);
            }

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Updated',
                text: `The notification was marked as ${notification.readAt ? 'unread' : 'read'}`,
            });

            refresh();
        } catch (err) {
            const message = createErrorHandler().handle(err);

            await displayError(`An error occurred: ${message}\nPlease try again.`);
        } finally {
            setLoading(false);
        }
    }, [refresh]);

    const handleSelected = React.useCallback((previous: boolean, notification: INotificationItem) => {
        setSelected((prev) => !previous ? prev.concat(notification.uuid) : prev.filter((uuid) => uuid !== notification.uuid));
    }, []);

    const handleSelectChanged = React.useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        setSelected(e.target.checked ? notifications.map((notification) => notification.uuid) : []);
    }, [notifications]);

    const markNotifications = React.useCallback(async (as: 'read' | 'unread', uuids: string[]) => {
        try {
            const data = {
                notifications: uuids.map((uuid) => ({
                    id: uuid,
                    read_at: as === 'read' ? DateTime.now().toISO() : null
                }))
            };

            await bulkUpdate(data);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Updated',
                text: `The notifications were marked as ${as}.`,
            });
        } catch (err) {
            const message = createErrorHandler().handle(err);

            await displayError(`An error occurred: ${message}\nPlease try again.`);
        }
    }, []);

    const handleMarkReadClicked = React.useCallback(async () => {
        try {
            setLoading(true);

            await markNotifications('read', selected);

            refresh();
        } catch (err) {
            logger.error(err);

            await displayError('There was an error marking the selected notifications as read. Please try again.');
        } finally {
            setLoading(false);
        }
    }, [selected, refresh]);

    const handleMarkUnreadClicked = React.useCallback(async () => {
        try {
            setLoading(true);

            await markNotifications('unread', selected);

            refresh();
        } catch (err) {
            logger.error(err);

            await displayError('There was an error marking the selected notifications as unread. Please try again.');
        } finally {
            setLoading(false);
        }

    }, [selected, refresh]);

    const handleDeleteClicked = React.useCallback(async () => {
        const result = await withReactContent(Swal).fire({
            title: 'Are you sure?',
            text: `This will delete the ${selected.length} selected notifications forever.`,
            icon: 'question',
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        try {
            setLoading(true);

            await bulkDestroy(selected);

            refresh();
        } catch (err) {
            logger.error(err);

            await displayError('There was an error deleting the selected notifications. Please try again.');
        } finally {
            setLoading(false);
        }

    }, [selected, refresh]);

    const displayError = React.useCallback((message: string) => withReactContent(Swal).fire({
        title: 'Ooops...',
        text: message,
        icon: 'error',
    }), []);

    React.useEffect(() => {
        // Updates the relative time notifications were sent
        const timer = window.setInterval(() => setRenderCount((count) => count + 1), 10 * 1000);

        return () => {
            window.clearInterval(timer);
        }
    }, []);

    const sortedNotifications = React.useMemo(() =>
        notifications
            .filter((item) => {
                if (show === 'unread')
                    return !item.readAt;
                else if (show === 'read')
                    return item.readAt;

                return true;
            })
            .sort((a, b) => {
                if (sortBy === 'oldest_newest') {
                    return a.dateTime.diff(b.dateTime).milliseconds;
                } else {
                    return b.dateTime.diff(a.dateTime).milliseconds;
                }
            }),
        [notifications, sortBy, show]
    );

    return (
        <>
            {loading && <Loader display={{ type: 'over-element' }} />}
            <Row>
                <Col xs={12} className='d-flex flex-column flex-md-row justify-content-between mb-3'>
                    <div className="d-flex flex-column flex-md-row mb-3 mb-md-0">
                        {selected.length > 0 && (
                            <Dropdown group toggle={() => setShowDropdown((prev) => !prev)} isOpen={showDropdown}>
                                <DropdownToggle caret color='primary'>
                                    <FaToolbox />{' '}
                                    Actions
                                </DropdownToggle>
                                <DropdownMenu>
                                    <DropdownItem onClick={handleMarkReadClicked}>
                                        <FaEnvelopeOpen />{' '}Mark Read
                                    </DropdownItem>
                                    <DropdownItem onClick={handleMarkUnreadClicked}>
                                        <FaEnvelope />{' '}Mark Unread
                                    </DropdownItem>
                                    <DropdownItem onClick={handleDeleteClicked}>
                                        <FaTrash />{' '}Delete
                                    </DropdownItem>
                                </DropdownMenu>
                            </Dropdown>
                        )}
                    </div>
                    <div className="text-start text-md-end">
                        <Form className="row row-cols-lg-auto g-3" onSubmit={handleUpdateFormSubmitted}>
                            <Col xs={12}>
                                <Label htmlFor="sort" className="col-form-label float-md-start me-1">Sort By: </Label>
                                <Col className="float-md-start">
                                    <Input type='select' name='sort' id='sort' value={sortBy} onChange={(e) => setSortBy(e.target.value)}>
                                        <option value="newest_oldest">Newest to Oldest</option>
                                        <option value="oldest_newest">Oldest to Newest</option>
                                    </Input>
                                </Col>
                            </Col>

                            <Col xs={12}>
                                <Label htmlFor="show" className="col-form-label float-md-start me-1">Show: </Label>
                                <Col className="float-md-start">
                                    <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value)}>
                                        <option value="unread">Unread</option>
                                        <option value="read">Read</option>
                                        <option value="all">All</option>
                                    </Input>
                                </Col>
                            </Col>
                            <Col xs={12} className='d-flex flex-column flex-md-row'>
                                <Button type='submit' color='primary'>
                                    <FaSync /> Update
                                </Button>
                            </Col>
                        </Form>
                    </div>
                </Col>

                <Col xs={12}>
                    <Table responsive>
                        <thead>
                            <tr>
                                <th>
                                    <span className='visually-hidden'>Select All</span>
                                    <input type="checkbox" checked={selected.length === notifications.length} onChange={handleSelectChanged} />
                                </th>
                                <th>Message</th>
                                <th>Sent</th>
                                <th>Read</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {sortedNotifications.map((item, index) => (
                                <NotificationItem
                                    key={index}
                                    notification={item}
                                    selected={selected.includes(item.uuid)}
                                    onSelect={(checked) => handleSelected(checked, item)}
                                    onMarkClicked={() => handleMarkAsClicked(item)}
                                />
                            ))}
                        </tbody>
                    </Table>
                </Col>
            </Row>
        </>
    );
}

export default connector(withNotifications(NotificationList));
