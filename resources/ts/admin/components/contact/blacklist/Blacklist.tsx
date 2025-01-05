import React from 'react';
import { Button, Card, CardBody, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Row } from 'reactstrap';
import { FaPlus, FaSync, FaToolbox, FaTrash } from 'react-icons/fa';

import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';
import axios from 'axios';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import PaginatedTable, { PaginatedTableHandle } from '@admin/components/paginated-table/PaginatedTable';
import LoadError from '@admin/components/LoadError';
import CreatePrompt, { BlacklistEntry } from './CreatePrompt';
import Table from './table/Table';

import awaitModalPrompt from '@admin/utils/modals';
import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

const Blacklist: React.FC = () => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const paginatedTableRef = React.useRef<PaginatedTableHandle>(null);

    const [selected, setSelected] = React.useState<IBlacklistEntry[]>([]);
    const [actionDropdown, setActionDropdown] = React.useState(false);

    const load = React.useCallback(async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IBlacklistEntry>>(link ?? '/pages/contact/blacklist');

        return response.data;
    }, []);

    const reload = React.useCallback(() => {
        paginatedTableRef.current?.reset();
        waitToLoadRef.current?.load();

        setSelected([]);
    }, [waitToLoadRef.current, paginatedTableRef.current]);

    const confirmPrompt = React.useCallback(async (text: string) => {
        const result = await withReactContent(Swal).fire({
            title: 'Are you sure?',
            text,
            icon: 'question',
            showCancelButton: true
        });

        return result.isConfirmed;
    }, []);

    const handleDeleteSelectedClicked = React.useCallback(async () => {
        try {
            if (!await confirmPrompt(`This will delete ${selected.length} blacklist entries.`))
                return;

            const data = { entries: selected.map((id) => id) };

            await createAuthRequest().delete<Record<'success', string>>(`/pages/contact/blacklist`, data);

            await withReactContent(Swal).fire({
                title: 'Success!',
                text: 'The selected blacklist entries were deleted.',
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

    }, [selected, reload]);

    const handleRefreshClicked = React.useCallback(async () => {
        reload();
    }, [reload]);

    const handleAddClicked = React.useCallback(async () => {
        const entry = await awaitModalPrompt(CreatePrompt);

        await addEntry(entry);

        reload();
    }, [reload]);

    const addEntry = React.useCallback(async ({ input, type, value }: BlacklistEntry) => {
        try {
            const response = await createAuthRequest().post<Record<'success', string>>(`/pages/contact/blacklist`, {
                input,
                [type === 'regex' ? 'pattern' : 'value']: value
            });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: response.data.success
            });
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to add entry: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await addEntry({ input, type, value });
        }
    }, []);

    const handleRemoveClicked = React.useCallback(async (entry: IBlacklistEntry) => {
        try {
            if (!await confirmPrompt(`This will delete the ${entry.input === 'email' ? 'e-mail address' : 'name'} entry: ${entry.value}`))
                return;

            const response = await createAuthRequest().delete<Record<'success', string>>(`/pages/contact/blacklist/${entry.id}`, {});

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

    const handleSelected = React.useCallback((entry: IBlacklistEntry) => {
        setSelected((prev) => prev.some(({ id }) => id === entry.id) ? prev.filter((value) => value.id !== entry.id) : prev.concat(entry));
    }, []);

    const handleSelectAll = React.useCallback((e: React.ChangeEvent<HTMLInputElement>, entries: IBlacklistEntry[]) => {
        setSelected(e.target.checked ? entries : []);
    }, []);

    return (
        <>
            <Card>
                <CardBody>
                    <Row>
                        <Col xs={12} className='d-flex flex-column flex-md-row justify-content-between mb-3'>
                            <div className="mb-3 mb-md-0">
                                <Button color='primary' onClick={handleAddClicked}>
                                    <FaPlus />{' '}
                                    Add
                                </Button>
                            </div>
                            <div className="text-start text-md-end">
                                <Dropdown group toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                                    <DropdownToggle caret color='primary'>
                                        <FaToolbox />{' '}
                                        Actions
                                    </DropdownToggle>
                                    <DropdownMenu>
                                        <DropdownItem onClick={handleRefreshClicked}>
                                            <FaSync />{' '}
                                            Refresh List
                                        </DropdownItem>
                                        {selected.length > 0 && (
                                            <>
                                                <DropdownItem divider />
                                                <DropdownItem onClick={handleDeleteSelectedClicked}>
                                                    <FaTrash />{' '}
                                                    Remove Selected
                                                </DropdownItem>
                                            </>

                                        )}
                                    </DropdownMenu>

                                </Dropdown>
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
                                                {(entries, key) => (
                                                    <Table
                                                        key={key}
                                                        entries={entries}
                                                        selected={selected}
                                                        onSelectAll={(e) => handleSelectAll(e, entries)}
                                                        onSelect={handleSelected}
                                                        onRemove={handleRemoveClicked}
                                                    />
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

export default Blacklist;
