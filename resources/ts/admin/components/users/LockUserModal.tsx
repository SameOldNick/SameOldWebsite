import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import axios from 'axios';

interface IProps {
    user: IUser;
    onLocked: () => void;
    onCanceled: () => void;
}

const LockUserModal: React.FC<IProps> = ({ user, onLocked, onCanceled }) => {
    const displayPrompt = async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to lock out user with email "${user.email}"?`,
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
            const response = await createAuthRequest().delete(`/users/${user.id}`);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Locked',
                text: `User with email "${user.email}" has been locked out.`,
            });
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `Unable to lock out user with email "${user.email}": ${message}`,
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
