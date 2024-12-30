import React from 'react';
import { Helmet } from 'react-helmet';
import { Button, Card, CardBody, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Form, Input, Row } from 'reactstrap';
import { FaCloudUploadAlt, FaRedo } from 'react-icons/fa';

import Heading from '@admin/layouts/admin/Heading';
import BackupList from '@admin/components/backups/BackupList';
import PerformBackupModal, { TBackupTypes } from '@admin/components/backups/backup-modal/PerformBackupModal';

import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

const All: React.FC<IHasRouter> = ({ router: { navigate } }) => {
    const [performBackup, setPerformBackup] = React.useState<TBackupTypes | false>(false);
    const [dropdownOpen, setDropdownOpen] = React.useState(false);
    const [show, setShow] = React.useState('all');
    const [renderCount, setRenderCount] = React.useState(0);

    const handleUpdateFormSubmitted = React.useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        setShow(e.currentTarget.show.value);

        setRenderCount((count) => count + 1);
    }, []);

    const handleRunFullBackupClicked = React.useCallback(async (e: React.MouseEvent) => {
        e.preventDefault();

        setPerformBackup('full');
    }, []);

    const handleRunDatabaseBackupClicked = React.useCallback(async (e: React.MouseEvent) => {
        e.preventDefault();

        setPerformBackup('database');
    }, []);

    const handleRunFileBackupClicked = React.useCallback(async (e: React.MouseEvent) => {
        e.preventDefault();

        setPerformBackup('files');
    }, []);

    const handlePerformBackupModalClosed = React.useCallback(() => {
        setPerformBackup(false);

        setRenderCount((count) => count + 1);
    }, []);

    return (
        <>
            <Helmet>
                <title>Backups</title>
            </Helmet>

            <Heading title='Backups' />

            {/* Can't use awaitModalPrompt to display modal because the context won't exist. */}
            {performBackup && <PerformBackupModal type={performBackup} onClose={handlePerformBackupModalClosed} />}

            <Card>
                <CardBody>
                    <Row>
                        <Col xs={12} className='d-flex flex-column flex-md-row justify-content-between mb-3'>
                            <div className="mb-3 mb-md-0">
                                <Dropdown
                                    isOpen={dropdownOpen}
                                    toggle={() => setDropdownOpen(!dropdownOpen)}
                                    disabled={performBackup !== false}
                                    className='d-flex flex-column flex-md-row'
                                >
                                    <DropdownToggle color="primary" disabled={performBackup !== false} caret>
                                        <span className='me-1'>
                                            <FaCloudUploadAlt />
                                        </span>
                                        Perform Backup
                                    </DropdownToggle>
                                    <DropdownMenu color="primary">
                                        <DropdownItem onClick={handleRunFullBackupClicked}>Perform Full Backup</DropdownItem>
                                        <DropdownItem onClick={handleRunDatabaseBackupClicked}>Perform Database Backup</DropdownItem>
                                        <DropdownItem onClick={handleRunFileBackupClicked}>Perform File Backup</DropdownItem>
                                    </DropdownMenu>
                                </Dropdown>
                            </div>
                            <div className="text-start text-md-end">
                                <Form className="row row-cols-lg-auto g-3" onSubmit={handleUpdateFormSubmitted}>
                                    <Col xs={12}>
                                        <label className="visually-hidden" htmlFor="show">Show</label>
                                        <Input type='select' name='show' id='show' defaultValue={show}>
                                            <option value="successful">Successful Only</option>
                                            <option value="failed">Failed Only</option>
                                            <option value="not-exists">Non-existent Only</option>
                                            <option value="deleted">Deleted Only</option>
                                            <option value="all">All</option>
                                        </Input>
                                    </Col>
                                    <Col xs={12} className='d-flex flex-column flex-md-row'>
                                        <Button type='submit' color='primary'>
                                            <span className='me-1'>
                                                <FaRedo />
                                            </span>
                                            Update
                                        </Button>
                                    </Col>
                                </Form>
                            </div>
                        </Col>

                        <Col xs={12}>
                            <BackupList key={renderCount} show={show} />
                        </Col>
                    </Row>

                </CardBody>
            </Card>
        </>
    );
}

export default requiresRolesForPage(withRouter(All), ['manage_backups']);
