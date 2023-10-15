import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import User from '@admin/utils/api/models/User';

interface IProps {
    user: User;
    onLocked: () => void;
    onCanceled: () => void;
}

const LockUserModal: React.FC<IProps> = ({ user, onLocked, onCanceled }) => {
    const displayPrompt = async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to lock out user with email "${user.user.email}"?`,
            confirmButtonColor: 'danger',
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await lockUser();

            onLocked();
        } else {
            onCanceled();
        }
    }

    // TODO: Move to seperate API class.
    const lockUser = async () => {
        try {
            const response = await createAuthRequest().delete(`/users/${user.user.id}`);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Locked',
                text: `User with email "${user.user.email}" has been locked out.`,
            });
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `Unable to lock out user with email "${user.user.email}": ${message}`,
            });

            onCanceled();
        }
    }

    React.useEffect(() => {
        displayPrompt();
    });

    return (
        <>

        </>
    );
}

export default LockUserModal;
