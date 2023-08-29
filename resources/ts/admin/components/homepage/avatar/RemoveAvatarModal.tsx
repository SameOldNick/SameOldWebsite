import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import axios from 'axios';
import Swal from 'sweetalert2';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface IProps {
    onRemoved: () => void;
    onCancelled: () => void;
}

const RemoveAvatarModal: React.FC<IProps> = ({ onRemoved, onCancelled }) => {
    const displayPrompt = async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Remove Your Avatar?',
            showConfirmButton: true,
            confirmButtonText: 'Yes',
            confirmButtonColor: 'danger',
            showCancelButton: true,
            cancelButtonText: 'No',
            cancelButtonColor: 'primary',
        });

        if (result.isConfirmed) {
            await deleteAvatar();
        } else {
            onCancelled();
        }
    }

    const deleteAvatar = async () => {
        try {
            const response = await createAuthRequest().delete<IMessageResponse>('user/avatar');

            onRemoved();
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Unable To Remove Avatar',
                text: message,
                showConfirmButton: true,
                confirmButtonText: 'Try Again',
                confirmButtonColor: 'primary',
                cancelButtonColor: 'secondary',
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await deleteAvatar();
            } else {
                onCancelled();
            }

        }
    }

    React.useEffect(() => {
        displayPrompt();
    });

    return (
        <></>
    );
}

export default RemoveAvatarModal;
