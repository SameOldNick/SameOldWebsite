import React from 'react';
import { Table } from 'reactstrap';

import WaitToLoad, { IWaitToLoadHelpers } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import PaginatedTable from '@admin/components/PaginatedTable';

import { createAuthRequest } from '@admin/utils/api/factories';
import UserModel from '@admin/utils/api/models/User';
import UserRow from './UserRow';

interface IProps {
    show: string;
    onLoadError: (err: unknown, helpers: IWaitToLoadHelpers) => void;
}

const UsersList: React.FC<IProps> = ({ show, onLoadError }) => {
    const fetchUsers = React.useCallback(async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IUser>>(link ?? 'users', { show });

        return response.data;
    }, [show]);

    const handleLocked = React.useCallback(({ reload }: IWaitToLoadHelpers) => reload(), []);
    const handleUnlocked = React.useCallback(({ reload }: IWaitToLoadHelpers) => reload(), []);

    return (
        <>

            <WaitToLoad
                loading={<Loader display={{ type: 'over-element' }} />}
                callback={fetchUsers}
            >
                {(response, err, helpers) => (
                    <>
                        {err !== undefined && onLoadError(err, helpers)}
                        {response !== undefined && (
                            <PaginatedTable initialResponse={response} pullData={fetchUsers}>
                                {(data) => (
                                    <Table responsive>
                                        <thead>
                                            <tr>
                                                {/* TODO: Sort columns */}
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>E-mail</th>
                                                <th>Created</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {data.length === 0 && (
                                                <tr>
                                                    <td colSpan={6} className='text-center text-muted'>(No users found)</td>
                                                </tr>
                                            )}
                                            {data.length > 0 && data.map((user, index) => (
                                                <UserRow
                                                    key={index}
                                                    user={new UserModel(user)}
                                                    onLocked={() => handleLocked(helpers)}
                                                    onUnlocked={() => handleUnlocked(helpers)}
                                                />)
                                            )}
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

export default UsersList;
