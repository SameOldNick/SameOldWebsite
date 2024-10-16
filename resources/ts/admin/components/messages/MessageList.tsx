import React from 'react';
import { Button, Card, CardBody, Col, Form, Input, Label, Row, Table } from 'reactstrap';
import { FaSync } from 'react-icons/fa';

import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';
import axios from 'axios';

import MessageRow from './MessageRow';
import MessageModal from './MessageModal';
import WaitToLoad, { IWaitToLoadHandle, IWaitToLoadHelpers } from '../WaitToLoad';
import Loader from '../Loader';

import ContactMessage from '@admin/utils/api/models/ContactMessage';
import awaitModalPrompt from '@admin/utils/modals';
import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { DateTime } from 'luxon';

interface IMessageListProps {

}

const MessageList: React.FC<IMessageListProps> = ({ }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);

    const [sortBy, setSortBy] = React.useState('sent');
    const [show, setShow] = React.useState('all');

    const fetchMessages = React.useCallback(async () => {
        const response = await createAuthRequest().get<IContactMessage[]>('/contact-messages');

        return response.data
            .map((message) => new ContactMessage(message))
            .filter((message) => show !== 'all' ? message.status === show : true)
            .sort((a, b) => {
                // TODO: Allow ascending and descending ordering.

                if (sortBy === 'from')
                    return a.displayName.localeCompare(b.displayName);
                else if (sortBy === 'status')
                    return a.status.localeCompare(b.status);
                else
                    return a.createdAt.diff(b.createdAt).seconds;
            });
    }, [sortBy, show]);

    const handleViewClicked = React.useCallback((message: ContactMessage) => {
        awaitModalPrompt(MessageModal, { message });
    }, []);

    const handleMarkUnconfirmedClicked = React.useCallback(async (message: ContactMessage, { reload }: IWaitToLoadHelpers) => {
        try {
            await createAuthRequest().put<IContactMessage>(`/contact-messages/${message.message.uuid}`, {
                confirmed_at: null
            });

            await withReactContent(Swal).fire({
                title: 'Success!',
                text: 'The contact message was marked as unconfirmed.',
                icon: 'success'
            });

        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined)

            await withReactContent(Swal).fire({
                title: 'Oops...',
                text: `An error occurred: ${message}`,
                icon: 'error'
            });
        } finally {
            await reload();
        }

    }, []);

    const handleMarkConfirmedClicked = React.useCallback(async (message: ContactMessage, { reload }: IWaitToLoadHelpers) => {
        try {
            await createAuthRequest().put<IContactMessage>(`/contact-messages/${message.message.uuid}`, {
                confirmed_at: DateTime.now().toISO()
            });

            await withReactContent(Swal).fire({
                title: 'Success!',
                text: 'The contact message was marked as confirmed.',
                icon: 'success'
            });

        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined)

            await withReactContent(Swal).fire({
                title: 'Oops...',
                text: `An error occurred: ${message}`,
                icon: 'error'
            });
        } finally {
            await reload();
        }

    }, []);

    const handleDeleteClicked = React.useCallback(async (message: ContactMessage, { reload }: IWaitToLoadHelpers) => {
        try {
            const result = await withReactContent(Swal).fire({
                title: 'Are You Sure?',
                text: `This will remove contact message with ID "${message.message.uuid}".`,
                icon: 'question',
                showCancelButton: true
            });

            if (!result.isConfirmed) {
                return;
            }

            const response = await createAuthRequest().delete<Record<'success', string>>(`/contact-messages/${message.message.uuid}`);

            await withReactContent(Swal).fire({
                title: 'Success!',
                text: response.data.success,
                icon: 'success'
            });

        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined)

            await withReactContent(Swal).fire({
                title: 'Oops...',
                text: `An error occurred: ${message}`,
                icon: 'error'
            });
        } finally {
            await reload();
        }

    }, []);

    const handleDisplayOptionsFormSubmit = React.useCallback((e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        waitToLoadRef.current?.load();
    }, [waitToLoadRef.current]);

    return (
        <>
            <Card>
                <CardBody>
                    <Row>
                        <Col xs={12} className='d-flex flex-column flex-md-row justify-content-between mb-3'>
                            <div className="mb-3 mb-md-0"></div>
                            <div className="text-start text-md-end">
                                <Form className="row row-cols-lg-auto g-3" onSubmit={handleDisplayOptionsFormSubmit}>
                                    <Col xs={12}>
                                        <Label htmlFor="sort" className="col-form-label float-md-start me-1">Sort By: </Label>
                                        <Col className="float-md-start">
                                            <Input type='select' name='sort' id='sort' value={sortBy} onChange={(e) => setSortBy(e.target.value)}>
                                                <option value="from">From</option>
                                                <option value="sent">Sent</option>
                                                <option value="status">Status</option>
                                            </Input>
                                        </Col>
                                    </Col>

                                    <Col xs={12}>
                                        <Label htmlFor="show" className="col-form-label float-md-start me-1">Show: </Label>
                                        <Col className="float-md-start">
                                            <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value)}>
                                                <option value="accepted">Accepted</option>
                                                <option value="confirmed">Confirmed</option>
                                                <option value="unconfirmed">Unconfirmed</option>
                                                <option value="expired">Expired</option>
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

                            <WaitToLoad
                                ref={waitToLoadRef}
                                loading={<Loader display={{ type: 'over-element' }} />}
                                callback={fetchMessages}
                            >
                                {(messages, err, helpers) => (
                                    <>
                                        <Table responsive>
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>From</th>
                                                    <th>Message</th>
                                                    <th>Sent</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {messages && messages.map((message, index) => (
                                                    <MessageRow
                                                        key={index}
                                                        message={message}
                                                        onViewClicked={() => handleViewClicked(message)}
                                                        onMarkUnconfirmedClicked={() => handleMarkUnconfirmedClicked(message, helpers)}
                                                        onMarkConfirmedClicked={() => handleMarkConfirmedClicked(message, helpers)}
                                                        onRemoveClicked={() => handleDeleteClicked(message, helpers)}
                                                    />
                                                ))}
                                                {err !== undefined && (
                                                    <tr>
                                                        <td colSpan={5}>(An error occurred)</td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </Table>
                                    </>
                                )}
                            </WaitToLoad>

                        </Col>
                    </Row>

                </CardBody>
            </Card>
        </>
    )
}

export default MessageList;
