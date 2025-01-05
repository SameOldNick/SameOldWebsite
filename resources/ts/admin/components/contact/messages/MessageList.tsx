import React from 'react';
import { Button, Card, CardBody, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Form, Input, Label, Row, Table } from 'reactstrap';
import { FaRegCheckCircle, FaRegTimesCircle, FaSync, FaToolbox, FaTrash } from 'react-icons/fa';

import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';
import axios from 'axios';
import { DateTime } from 'luxon';

import MessageRow from './MessageRow';
import MessageModal from './MessageModal';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import PaginatedTable, { PaginatedTableHandle } from '@admin/components/paginated-table/PaginatedTable';
import LoadError from '@admin/components/LoadError';

import ContactMessage from '@admin/utils/api/models/ContactMessage';
import awaitModalPrompt from '@admin/utils/modals';
import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import classNames from 'classnames';

const MessageList: React.FC = () => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const paginatedTableRef = React.useRef<PaginatedTableHandle>(null);

    const [sortBy, setSortBy] = React.useState('sent_descending');
    const [show, setShow] = React.useState('all');
    const [perPage, setPerPage] = React.useState('15');
    const [selected, setSelected] = React.useState<string[]>([]);
    const [actionDropdown, setActionDropdown] = React.useState(false);

    const load = React.useCallback(async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IContactMessage>>(
            link ?? '/contact-messages',
            {
                sort: sortBy,
                show: show !== 'all' ? show : undefined,
                per_page: !isNaN(parseInt(perPage)) ? Number(perPage) : 0
            });

        return response.data;
    }, [sortBy, show, perPage]);

    const reload = React.useCallback(() => {
        paginatedTableRef.current?.reset();
        waitToLoadRef.current?.load();

        setSelected([]);
    }, [waitToLoadRef.current, paginatedTableRef.current]);

    const handleViewClicked = React.useCallback((message: ContactMessage) => {
        awaitModalPrompt(MessageModal, { message });
    }, []);

    const confirmPrompt = React.useCallback(async (text: string) => {
        const result = await withReactContent(Swal).fire({
            title: 'Are you sure?',
            text,
            icon: 'question',
            showCancelButton: true
        });

        return result.isConfirmed;
    }, []);

    const handleMarkUnconfirmedClicked = React.useCallback(async (message: ContactMessage) => {
        try {
            if (!await confirmPrompt(`This will mark contact message with ID "${message.message.uuid}" as unconfirmed.`))
                return;

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
            reload();
        }

    }, [reload]);

    const handleMarkConfirmedClicked = React.useCallback(async (message: ContactMessage) => {
        try {
            if (!await confirmPrompt(`This will mark contact message with ID "${message.message.uuid}" as confirmed.`))
                return;

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
            reload();
        }

    }, [reload]);

    const handleDenyClicked = React.useCallback(async (input: 'name' | 'email', message: ContactMessage) => {
        try {
            const value = input === 'name' ? message.message.name : message.message.email;

            if (!await confirmPrompt(`Any messages from "${value}" will be denied.`))
                return;

            const response = await createAuthRequest().post<Record<'success', string>>(`/pages/contact/blacklist`, {
                input,
                value
            });

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
            reload();
        }

    }, [reload]);

    const handleDeleteClicked = React.useCallback(async (message: ContactMessage) => {
        try {
            if (!await confirmPrompt(`This will remove contact message with ID "${message.message.uuid}".`))
                return;

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
            reload();
        }

    }, [reload]);

    const handleMarkSelectedUnconfirmedClicked = React.useCallback(async () => {
        try {
            if (!await confirmPrompt(`This will mark ${selected.length} contact messages as unconfirmed.`))
                return;

            const data = {
                messages: selected.map((uuid) => ({
                    uuid,
                    confirmed_at: null
                }))
            };

            await createAuthRequest().put<IContactMessage>(`/contact-messages`, data);

            await withReactContent(Swal).fire({
                title: 'Success!',
                text: 'The selected contact messages were marked as unconfirmed.',
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
            reload();
        }
    }, [reload, selected]);

    const handleMarkSelectedConfirmedClicked = React.useCallback(async () => {
        try {
            if (!await confirmPrompt(`This will mark ${selected.length} contact messages as confirmed.`))
                return;

            const data = {
                messages: selected.map((uuid) => ({
                    uuid,
                    confirmed_at: DateTime.now().toISO()
                }))
            };

            await createAuthRequest().put<IContactMessage>(`/contact-messages`, data);

            await withReactContent(Swal).fire({
                title: 'Success!',
                text: 'The selected contact messages were marked as confirmed.',
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
            reload();
        }
    }, [reload, selected]);

    const handleDeleteSelectedClicked = React.useCallback(async () => {
        try {
            if (!await confirmPrompt(`This will delete ${selected.length} contact messages.`))
                return;

            const data = { messages: selected.map((uuid) => uuid) };

            await createAuthRequest().delete<IContactMessage>(`/contact-messages`, data);

            await withReactContent(Swal).fire({
                title: 'Success!',
                text: 'The selected contact messages were deleted.',
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
            reload();
        }

    }, [reload, selected]);

    const handleDisplayOptionsFormSubmit = React.useCallback((e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        reload();
    }, [reload]);

    const handleSelected = React.useCallback((uuid: string) => {
        setSelected((prev) => prev.includes(uuid) ? prev.filter((value) => value !== uuid) : prev.concat(uuid));
    }, []);

    const isAllSelected = React.useCallback((messages: IContactMessage[]) => {
        const uuids = messages.map((message) => message.uuid);

        return selected.length > 0 && selected.filter((value) => uuids.includes(value)).length === uuids.length;
    }, [selected]);

    const handleSelectAll = React.useCallback((e: React.ChangeEvent<HTMLInputElement>, messages: IContactMessage[]) => {
        setSelected(e.target.checked ? messages.map((message) => message.uuid) : []);
    }, []);

    return (
        <>
            <Card>
                <CardBody>
                    <Row>
                        <Col xs={12} className='d-flex flex-column flex-md-row justify-content-between mb-3'>
                            <div className="mb-3 mb-md-0">
                                {selected.length > 0 && (
                                    <Dropdown group toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                                        <DropdownToggle caret color='primary'>
                                            <FaToolbox />{' '}
                                            Actions
                                        </DropdownToggle>
                                        <DropdownMenu>
                                            <DropdownItem onClick={handleMarkSelectedUnconfirmedClicked}>
                                                <FaRegTimesCircle />{' '}
                                                Mark Unconfirmed
                                            </DropdownItem>
                                            <DropdownItem onClick={handleMarkSelectedConfirmedClicked}>
                                                <FaRegCheckCircle />{' '}
                                                Mark Confirmed
                                            </DropdownItem>
                                            <DropdownItem onClick={handleDeleteSelectedClicked}>
                                                <FaTrash />{' '}
                                                Remove
                                            </DropdownItem>
                                        </DropdownMenu>
                                    </Dropdown>
                                )}
                            </div>
                            <div className="text-start text-md-end">
                                <Form className="row row-cols-lg-auto g-3" onSubmit={handleDisplayOptionsFormSubmit}>
                                    <Col xs={12}>
                                        <Label htmlFor="sort" className="col-form-label float-md-start me-1">Items Per Page: </Label>
                                        <Col className="float-md-start">
                                            <Input type='select' name='per_page' id='perPage' value={perPage} onChange={(e) => setPerPage(e.target.value)}>
                                                <option value="15">15</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                                <option value="all">All</option>
                                            </Input>
                                        </Col>
                                    </Col>

                                    <Col xs={12}>
                                        <Label htmlFor="sort" className="col-form-label float-md-start me-1">Sort By: </Label>
                                        <Col className="float-md-start">
                                            <Input type='select' name='sort' id='sort' value={sortBy} onChange={(e) => setSortBy(e.target.value)}>
                                                <option value="from">From</option>
                                                <option value="sent_descending">Sent (Newest to Oldest)</option>
                                                <option value="sent_ascending">Sent (Oldest to Newest)</option>
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
                                callback={load}
                            >
                                {(response, err) => (
                                    <>
                                        {response && (
                                            <PaginatedTable
                                                ref={paginatedTableRef}
                                                loader={<Loader display={{ type: 'over-element' }} />}
                                                initialResponse={response}
                                                pullData={load}
                                            >
                                                {(messages, key) => (
                                                    <Table key={key} responsive>
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <input
                                                                        type="checkbox"
                                                                        checked={isAllSelected(messages)}
                                                                        onChange={(e) => handleSelectAll(e, messages)}
                                                                    />
                                                                </th>
                                                                <th>ID</th>
                                                                <th>From</th>
                                                                <th>Message</th>
                                                                <th>Sent</th>
                                                                <th>Status</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {messages.map((message) => new ContactMessage(message)).map((message, index) => (
                                                                <MessageRow
                                                                    key={index}
                                                                    message={message}
                                                                    selected={selected.includes(message.message.uuid)}
                                                                    className={classNames({ 'table-active': selected.includes(message.message.uuid) })}
                                                                    onSelected={() => handleSelected(message.message.uuid)}
                                                                    onViewClicked={() => handleViewClicked(message)}
                                                                    onMarkUnconfirmedClicked={() => handleMarkUnconfirmedClicked(message)}
                                                                    onMarkConfirmedClicked={() => handleMarkConfirmedClicked(message)}
                                                                    onDenyNameClicked={() => handleDenyClicked('name', message)}
                                                                    onDenyEmailClicked={() => handleDenyClicked('email', message)}
                                                                    onRemoveClicked={() => handleDeleteClicked(message)}
                                                                />
                                                            ))}
                                                        </tbody>
                                                    </Table>
                                                )}
                                            </PaginatedTable>
                                        )}
                                        {err && (
                                            <LoadError
                                                error={err}
                                                onTryAgainClicked={() => reload()}
                                                onGoBackClicked={() => window.history.back()}
                                            />
                                        )}

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
