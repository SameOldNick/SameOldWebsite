import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import axios from 'axios';

interface IProps {
    user: IUser;
    onUnlocked: () => void;
    onCanceled: () => void;
}

const UnlockUserModal: React.FC<IProps> = ({ user, onUnlocked, onCanceled }) => {
    const displayPrompt = async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to unlock the user with email "${user.email}"?`,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await unlockUser();

            onUnlocked();
        } else {
            onCanceled();
        }
    }

    // TODO: Move to seperate API class.
    const unlockUser = async () => {
        try {
            const response = await createAuthRequest().post(`/users/restore/${user.id}`, {});

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Restored',
                text: `User with email "${user.email}" has been unlocked.`,
            });
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `Unable to unlock user with email "${user.email}": ${message}`,
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

export default UnlockUserModal;
