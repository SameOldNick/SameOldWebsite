import React from 'react';
import { Helmet } from 'react-helmet';
import { ConnectedProps, connect } from 'react-redux';
import { Button, Card, CardBody, Col, Form, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row, Table } from 'reactstrap';
import { FaEnvelope, FaEnvelopeOpen, FaExternalLinkAlt, FaSync } from 'react-icons/fa';

import S from 'string';

import Heading from '@admin/layouts/admin/Heading';

import { createAuthRequest } from '@admin/utils/api/factories';
import { fetchMessages } from '@admin/store/slices/notifications';

const connector = connect(
    ({ notifications: { messages } }: RootState) => ({ stored: messages }),
    { fetchMessages }
);

type TProps = ConnectedProps<typeof connector>;

interface IRowProps {
    notification: TMessageNotification;
    onViewClicked: () => void;
    onMarkReadClicked: () => void;
    onMarkUnreadClicked: () => void;
}

interface IModalProps {
    notification: TMessageNotification;
    onClose: () => void;
}

interface IState {
    showNotification?: TMessageNotification;
}

export default connector(class Messages extends React.Component<TProps, IState> {
    static Row: React.FC<IRowProps> = ({ notification, onViewClicked, onMarkReadClicked, onMarkUnreadClicked }) => {
        const address = notification.data.addresses.replyTo.length > 0 ? notification.data.addresses.replyTo[0].address : '(unknown)';
        const message = S(notification.data.view.text).truncate(75).s;

        const isRead = React.useMemo(() => notification.read_at !== null, [notification]);
        const styles: React.CSSProperties = !isRead ? { fontWeight: 'bold' } : {};

        return (
            <tr>
                <td style={styles}>{notification.id}</td>
                <td style={styles}>{address}</td>
                <td style={styles}>{message}</td>
                <td>
                    <Button color='primary' className='me-1' onClick={onViewClicked}>
                        <FaExternalLinkAlt />
                    </Button>
                    {
                        !isRead ? (
                            <Button color='primary' title='Mark as unread' onClick={onMarkReadClicked}>
                                <FaEnvelopeOpen />
                            </Button>
                        ) : (
                            <Button color='primary' title='Mark as read' onClick={onMarkUnreadClicked}>
                                <FaEnvelope />
                            </Button>
                        )
                    }

                </td>

            </tr>
        );
    }

    static Modal: React.FC<IModalProps> = ({ notification, onClose }) => {
        const toggle = () => onClose();

        return (
            <Modal isOpen={true} toggle={toggle} scrollable size='xl'>
                <ModalHeader>Message</ModalHeader>
                <ModalBody>
                    {Object.entries(notification.data.addresses).map(([label, addresses], index) => {
                        return addresses.length > 0 && (
                            <FormGroup key={index} tag="fieldset">
                                <legend>{S(label).humanize().s}</legend>

                                {addresses.map(({ address, name }, index) => (
                                    <Row key={index}>
                                        <Col xs={6}>
                                            <FormGroup>
                                                <Label for='subject'>E-mail:</Label>
                                                <Input id='subject' type='text' value={address} readOnly />
                                            </FormGroup>
                                        </Col>
                                        <Col xs={6}>
                                            <FormGroup>
                                                <Label for='subject'>Name:</Label>
                                                <Input id='subject' type='text' value={name || ''} readOnly />
                                            </FormGroup>
                                        </Col>
                                    </Row>
                                ))}


                            </FormGroup>
                        );
                    })}

                    <Row>
                        <FormGroup>
                            <Label for='subject'>Subject:</Label>
                            <Input id='subject' type='text' value={notification.data.subject} readOnly />
                        </FormGroup>
                    </Row>

                    <Row>
                        <FormGroup>
                            <Label for='textMessage'>Text Message:</Label>
                            <Input id='textMessage' type='textarea' value={notification.data.view.text} readOnly rows={6} />
                        </FormGroup>
                    </Row>

                    <Row>
                        <FormGroup>
                            <Label for='htmlMessage'>HTML Message:</Label>
                            <Input id='htmlMessage' type='textarea' value={notification.data.view.html} readOnly rows={6} />
                        </FormGroup>
                    </Row>

                </ModalBody>

                <ModalFooter>
                    <Button color='primary' onClick={toggle}>Close</Button>
                </ModalFooter>
            </Modal >
        );
    }

    constructor(props: Readonly<TProps>) {
        super(props);

        this.state = {
        };
    }

    componentDidMount(): void {
        this.fetchMessages();
    }

    private async fetchMessages() {
        await this.props.fetchMessages();
    }

    private viewNotification(notification: TMessageNotification) {
        this.setState({ showNotification: notification });
    }

    private async markUnread(notification: TMessageNotification) {
        try {
            const response = await createAuthRequest().post<TMessageNotification>(`/user/notifications/${notification.id}/unread`, {});

            await this.fetchMessages();
        } catch (err) {
            console.error(err);
        }
    }

    private async markRead(notification: TMessageNotification) {
        try {
            const response = await createAuthRequest().post<TMessageNotification>(`/user/notifications/${notification.id}/read`, {});

            await this.fetchMessages();
        } catch (err) {
            console.error(err);
        }
    }

    public render() {
        const { stored } = this.props;
        const { showNotification } = this.state;

        return (
            <>
                <Helmet>
                    <title>Messages</title>
                </Helmet>

                <Heading title='Messages' />

                {showNotification && <Messages.Modal notification={showNotification} onClose={() => this.setState({ showNotification: undefined })} />}

                <Card>
                    <CardBody>
                        <Row>
                            <Col xs={12} className='d-flex justify-content-between mb-3'>
                                <div></div>
                                <div className="text-end">
                                    <Form className="row row-cols-lg-auto g-3" onSubmit={() => this.fetchMessages()}>
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
                                            <Messages.Row
                                                key={index}
                                                notification={notification}
                                                onViewClicked={() => this.viewNotification(notification)}
                                                onMarkReadClicked={() => this.markRead(notification)}
                                                onMarkUnreadClicked={() => this.markUnread(notification)}
                                            />
                                        ))}
                                    </tbody>
                                </Table>
                            </Col>
                        </Row>

                    </CardBody>
                </Card>
            </>
        );
    }
});
