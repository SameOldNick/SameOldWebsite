import React from 'react';
import { ConnectedProps, connect } from 'react-redux';
import { Button, Card, CardBody, Col, Form, Row, Table } from 'reactstrap';
import { FaSync } from 'react-icons/fa';

import { createAuthRequest } from '@admin/utils/api/factories';
import { fetchMessages } from '@admin/store/slices/notifications';
import MessageRow from './MessageRow';
import MessageModal from './MessageModal';

interface IMessageListProps {

}

const connector = connect(
    ({ notifications: { messages } }: RootState) => ({ stored: messages }),
    { dispatchFetchMessages: fetchMessages }
);

type TProps = IMessageListProps & ConnectedProps<typeof connector>;

const MessageList: React.FC<TProps> = ({ stored, dispatchFetchMessages }) => {
    const [showNotification, setShowNotification] = React.useState<TMessageNotification | undefined>();

    const fetchMessages = React.useCallback(async () => {
        await dispatchFetchMessages();
    }, [dispatchFetchMessages]);

    const viewNotification = React.useCallback((notification: TMessageNotification) => {
        setShowNotification(notification);
    }, []);

    const markUnread = React.useCallback(async (notification: TMessageNotification) => {
        try {
            await createAuthRequest().post<TMessageNotification>(`/user/notifications/${notification.id}/unread`, {});

            await fetchMessages();
        } catch (err) {
            console.error(err);
        }
    }, []);

    const markRead = React.useCallback(async (notification: TMessageNotification) => {
        try {
            await createAuthRequest().post<TMessageNotification>(`/user/notifications/${notification.id}/read`, {});

            await fetchMessages();
        } catch (err) {
            console.error(err);
        }
    }, []);

    React.useEffect(() => {
        fetchMessages();
    }, []);

    return (
        <>

            {showNotification && <MessageModal notification={showNotification} onClose={() => setShowNotification(undefined)} />}

            <Card>
                <CardBody>
                    <Row>
                        <Col xs={12} className='d-flex justify-content-between mb-3'>
                            <div></div>
                            <div className="text-end">
                                <Form className="row row-cols-lg-auto g-3" onSubmit={() => fetchMessages()}>
                                    <Col xs={12}>
                                        <Button type='submit' color='primary'>
                                            <FaSync /> Update
                                        </Button>
                                    </Col>
                                </Form>

                            </div>
                        </Col>
                        <Col xs={12}>
                            <Table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>From</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {stored.map((notification, index) => (
                                        <MessageRow
                                            key={index}
                                            notification={notification}
                                            onViewClicked={() => viewNotification(notification)}
                                            onMarkReadClicked={() => markRead(notification)}
                                            onMarkUnreadClicked={() => markUnread(notification)}
                                        />
                                    ))}
                                </tbody>
                            </Table>
                        </Col>
                    </Row>

                </CardBody>
            </Card>
        </>
    )
}

export default connector(MessageList);
