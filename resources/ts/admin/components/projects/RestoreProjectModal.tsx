import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface IProps {
    project: IProject;
    onRestored: () => void;
    onCanceled: () => void;
}

const RestoreProjectModal: React.FC<IProps> = ({ project, onRestored, onCanceled }) => {
    const displayPrompt = async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to restore the "${project.project}" project?`,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await restoreProject();

            onRestored();
        } else {
            onCanceled();
        }
    }

    const restoreProject = async () => {
        try {
            const response = await createAuthRequest().post(`/projects/restore/${project.id}`, {});

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Restored',
                text: `The "${project.project}" project has been restored.`,
            });
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `Unable to restore the "${project.project}" project: ${message}`,
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

export default RestoreProjectModal;
