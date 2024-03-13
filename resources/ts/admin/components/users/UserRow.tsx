import React from 'react';
import { Button } from 'reactstrap';
import { FaEdit, FaLock, FaUnlock } from 'react-icons/fa';
import { NavLink } from 'react-router-dom';

import classNames from 'classnames';
import S from 'string';

import LockUserModal from '@admin/components/users/LockUserModal';
import UnlockUserModal from '@admin/components/users/UnlockUserModal';

import UserModel from '@admin/utils/api/models/User';
import awaitModalPrompt from '@admin/utils/modals';


interface IUserProps {
    user: UserModel;
    onLocked: () => void;
    onUnlocked: () => void;
}

const UserRow: React.FC<IUserProps> = ({ user, onLocked, onUnlocked }) => {
    const handleLockClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(LockUserModal, { user });

            onLocked();
        } catch (err) {
            // Modal was cancelled.
        }
    }, [user, onLocked]);

    const handleUnlockClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(UnlockUserModal, { user });

            onUnlocked();
        } catch (err) {
            // Modal was cancelled.
        }
    }, [user, onUnlocked]);

    return (
        <>
            <tr>
                <td>{user.user.id}</td>
                <td className={classNames({ 'text-muted': !user.user.name })}>{user.user.name || '(Empty)'}</td>
                <td>{user.user.email}</td>
                <td title={user.createdAt.toISO() || ''}>{user.createdAt.toLocaleString()}</td>
                <td>{S(user.status).humanize().s}</td>
                <td>
                    {!user.deletedAt && (
                        <>
                            <Button color='primary' tag={NavLink} to={user.generatePath()} className='me-1'>
                                <FaEdit />
                            </Button>
                            <Button color='danger' onClick={handleLockClicked} title='Lock account'>
                                <FaLock />
                            </Button>
                        </>
                    )}

                    {user.deletedAt && (
                        <>
                            <Button color='primary' onClick={handleUnlockClicked} title='Unlock account'>
                                <FaUnlock />
                            </Button>
                        </>
                    )}
                </td>
            </tr>
        </>
    );
}

export default UserRow;
