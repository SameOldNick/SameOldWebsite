import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import User from '@admin/utils/api/models/User';
import { IPromptModalProps } from '@admin/utils/modals';

interface IProps extends IPromptModalProps {
    user: User;
}

const UnlockUserModal: React.FC<IProps> = ({ user, onSuccess, onCancelled }) => {
    // TODO: Move to seperate API class.
    const unlockUser = React.useCallback(async () => {
        try {
            await createAuthRequest().post(`/users/restore/${user.user.id}`, {});

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Restored',
                text: `User with email "${user.user.email}" has been unlocked.`,
            });
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `Unable to unlock user with email "${user.user.email}": ${message}`,
            });

            onCancelled();
        }
    }, [user, onCancelled]);

    const displayPrompt = React.useCallback(async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to unlock the user with email "${user.user.email}"?`,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await unlockUser();

            onSuccess();
        } else {
            onCancelled();
        }
    }, [user, unlockUser, onSuccess, onCancelled]);

    React.useEffect(() => {
        displayPrompt();
    }, []);

    return (
        <>

        </>
    );
}

export default UnlockUserModal;
