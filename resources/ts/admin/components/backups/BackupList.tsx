import React from 'react';
import { Badge, Button, Table } from 'reactstrap';
import { FaExternalLinkAlt } from 'react-icons/fa';

import S from 'string';
import { DateTime } from 'luxon';

import BackupInfoModal from './BackupInfoModal';
import PaginatedTable from '@admin/components/paginated-table/PaginatedTable';
import Loader from '@admin/components/Loader';
import WaitToLoad from '@admin/components/WaitToLoad';
import LoadError from '@admin/components/LoadError';

import Backup from '@admin/utils/api/models/Backup';
import awaitModalPrompt from '@admin/utils/modals';
import { createAuthRequest } from '@admin/utils/api/factories';

interface IBackupListProps {
    show: string;
}

const BackupList: React.FC<IBackupListProps> = ({ show }) => {
    const retrieveBackups = React.useCallback(async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponse<IBackup>>(link ?? 'backups', { show: show !== 'all' ? show : undefined });

        return response.data;
    }, [show]);

    const mapDataToBackup = React.useCallback((backups: any[]) => backups.map((data) => new Backup(data)), []);

    const handleViewBackupClicked = React.useCallback(async (e: React.MouseEvent, backup: Backup) => {
        e.preventDefault();

        await awaitModalPrompt(BackupInfoModal, { backup });
    }, []);

    return (
        <>
            <WaitToLoad
                callback={retrieveBackups}
                loading={<Loader display={{ type: 'over-element' }} />}
            >
                {(response, err, { reload }) => (
                    <>
                        {/* Display error message */}
                        {err && (
                            <LoadError
                                error={err}
                                onTryAgainClicked={() => reload()}
                                onGoBackClicked={() => window.history.back()}
                            />
                        )}
                        {response && (
                            <PaginatedTable initialResponse={response} pullData={retrieveBackups}>
                                {(data) => (
                                    <Table responsive>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Name</th>
                                                <th scope='col'>Status</th>
                                                <th scope='col'>Created</th>
                                                <th scope='col'>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {data.length === 0 && (
                                                <tr>
                                                    <td colSpan={5} className='text-center text-muted'>(No backups found)</td>
                                                </tr>
                                            )}
                                            {data.length > 0 && mapDataToBackup(data).map((backup, index) => (
                                                <tr key={index}>
                                                    <td>{backup.backup.uuid}</td>
                                                    <td>{backup.file?.name || 'N/A'}</td>
                                                    <td>
                                                        <Badge color={backup.status === 'successful' ? 'success' : 'danger'}>
                                                            {S(backup.status).humanize().s}
                                                        </Badge>
                                                    </td>
                                                    <td>{backup.createdAt.toLocaleString(DateTime.DATETIME_FULL)}</td>
                                                    <td>
                                                        <Button color='primary' onClick={(e) => handleViewBackupClicked(e, backup)} title='More Information' className='me-1'>
                                                            <FaExternalLinkAlt />
                                                        </Button>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </Table>
                                )}
                            </PaginatedTable>
                        )}
                    </>
                )}
            </WaitToLoad>

        </>
    );
}

export default BackupList;
