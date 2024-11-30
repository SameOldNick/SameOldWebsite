import React from 'react';
import { Alert, Button, Card, CardBody, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Row } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import { IHasRouter } from '@admin/components/hoc/WithRouter';
import Loader from '@admin/components/Loader';
import BackupDestinationsTable from '@admin/components/backups/destinations/table/BackupDestinations';

import { createAuthRequest } from '@admin/utils/api/factories';
import createErrorHandler from '@admin/utils/errors/factory';
import BackupDestinationTestAwaits from './tests/BackupDestinationTestAwaits';

interface IProps extends IHasRouter {
}

const BackupDestinations: React.FC<IProps> = ({ router }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);

    const [dropdownOpen, setDropdownOpen] = React.useState(false);
    const [selected, setSelected] = React.useState<IBackupDestination[]>([]);
    const [awaitingTests, setAwaitingTests] = React.useState<string[]>([]);

    const handeReloadClicked = React.useCallback(async () => {
        waitToLoadRef.current?.load();
    }, [waitToLoadRef]);

    const fetchBackupDestinations = React.useCallback(async () => {
        const response = await createAuthRequest().get<IBackupDestination[]>('/backup/destinations');

        return response.data;
    }, []);


    const handleSelect = React.useCallback((destination: IBackupDestination) => {
        setSelected(
            (destinations) =>
                !destinations.map((destination) => destination.id).includes(destination.id) ?
                    destinations.concat(destination) :
                    destinations.filter((item) => item.id !== destination.id)
        );
    }, []);

    const handleSelectAll = React.useCallback((destinations: IBackupDestination[]) => {
        setSelected((value) => value.length === destinations.length ? [] : destinations);
    }, []);

    const handleAddClicked = React.useCallback(async () => {
        router.navigate('/admin/backups/destinations/create');
    }, []);

    const handleTestClicked = React.useCallback(async (destination: IBackupDestination) => {
        try {
            const response = await createAuthRequest().post<Record<'uuid', string>>(`/backup/destinations/${destination.id}/test`, {});

            setAwaitingTests((prev) => prev.concat(response.data.uuid));
        } catch (err) {
            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: 'An error occurred running the test. Please try again.',
            });
        }


    }, []);

    const handleTestFinished = React.useCallback((uuid: string) => {
        setAwaitingTests((prev) => prev.filter((value) => value !== uuid));
    }, []);

    const handleEditClicked = React.useCallback(async (destination: IBackupDestination) => {
        router.navigate(`/admin/backups/destinations/edit/${destination.id}`);
    }, []);

    const handleDeleteClicked = React.useCallback(async (destination: IBackupDestination) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `The backup destination with host "${destination.host}" will be deleted. This operation cannot be undone.`,
            confirmButtonColor: 'danger',
            confirmButtonText: 'Yes, delete it',
            showCancelButton: true,
        });

        if (result.isConfirmed) {
            try {
                const response = await createAuthRequest().delete<Record<'message', string>>(`/backup/destinations/${destination.id}`);

                await withReactContent(Swal).fire({
                    icon: 'success',
                    title: 'Destination Deleted',
                    text: response.data.message
                });

                waitToLoadRef.current?.load();
            } catch (err) {
                const message = createErrorHandler().handle(err);

                await withReactContent(Swal).fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: (
                        <>
                            <p>{message}</p>
                            <p>Please try again.</p>
                        </>
                    )
                });
            }
        }
    }, [waitToLoadRef]);

    const handleEnableAllClicked = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().put<Record<'message', string>>(`/backup/destinations`, {
                destinations: selected.map((destination) => ({
                    id: destination.id,
                    enable: true
                }))
            });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Destinations Enabled',
                text: 'The destinations have been enabled.'
            });

            waitToLoadRef.current?.load();
        } catch (err) {
            const message = createErrorHandler().handle(err);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                html: (
                    <>
                        <p>{message}</p>
                        <p>Please try again.</p>
                    </>
                )
            });
        }
    }, [selected, waitToLoadRef]);

    const handleDisableAllClicked = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().put<Record<'message', string>>(`/backup/destinations`, {
                destinations: selected.map((destination) => ({
                    id: destination.id,
                    enable: false
                }))
            });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Destinations Disabled',
                text: 'The destinations have been disabled.'
            });

            waitToLoadRef.current?.load();
        } catch (err) {
            const message = createErrorHandler().handle(err);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                html: (
                    <>
                        <p>{message}</p>
                        <p>Please try again.</p>
                    </>
                )
            });
        }
    }, [selected, waitToLoadRef]);

    const handleDeleteAllClicked = React.useCallback(async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `This will delete ${selected.length} backup destinations. This operation cannot be undone.`,
            confirmButtonColor: 'danger',
            confirmButtonText: 'Yes, delete them',
            showCancelButton: true,
        });

        if (result.isConfirmed) {
            try {
                const response = await createAuthRequest().delete<Record<'message', string>>(`/backup/destinations`, {
                    destinations: selected.map((destination) => destination.id)
                });

                await withReactContent(Swal).fire({
                    icon: 'success',
                    title: 'Destinations Deleted',
                    text: response.data.message
                });

                waitToLoadRef.current?.load();
            } catch (err) {
                const message = createErrorHandler().handle(err);

                await withReactContent(Swal).fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: (
                        <>
                            <p>{message}</p>
                            <p>Please try again.</p>
                        </>
                    )
                });
            }
        }
    }, [selected, waitToLoadRef]);

    return (
        <>
            <BackupDestinationTestAwaits uuids={awaitingTests} onFinished={handleTestFinished} />
            <Card className="mb-4">
                <CardBody>
                    <Row>
                        <Col xs={12} className='d-flex justify-content-between'>
                            <div>
                                <Button color='primary' onClick={handleAddClicked}>Add Backup Destination</Button>
                            </div>
                            <div>
                                <Dropdown
                                    isOpen={dropdownOpen}
                                    toggle={() => setDropdownOpen((prev) => !prev)}
                                    disabled={selected.length === 0}
                                >
                                    <DropdownToggle caret color={selected.length > 0 ? 'primary' : 'secondary'}>Actions</DropdownToggle>
                                    <DropdownMenu>
                                        <DropdownItem onClick={handleEnableAllClicked}>Enable Selected</DropdownItem>
                                        <DropdownItem onClick={handleDisableAllClicked}>Disable Selected</DropdownItem>
                                        <DropdownItem onClick={handleDeleteAllClicked}>Delete Selected</DropdownItem>
                                    </DropdownMenu>
                                </Dropdown>
                            </div>

                        </Col>
                        <Col xs={12} className='mt-3'>
                            <WaitToLoad ref={waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={fetchBackupDestinations}>
                                {(destinations, err) => (
                                    <>
                                        {err && (
                                            <Alert color='danger' className='d-flex justify-content-between'>
                                                <span>An error occurred getting backup destinations. Please try again.</span>
                                                <Button size='sm' color='primary' onClick={handeReloadClicked}>Reload</Button>
                                            </Alert>
                                        )}
                                        {destinations && (
                                            <BackupDestinationsTable
                                                destinations={destinations}
                                                selected={selected}
                                                onSelectAll={() => handleSelectAll(destinations)}
                                                onSelect={handleSelect}
                                                onTestClicked={handleTestClicked}
                                                onEditClicked={handleEditClicked}
                                                onDeleteClicked={handleDeleteClicked}
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
    );
}

export default BackupDestinations;
