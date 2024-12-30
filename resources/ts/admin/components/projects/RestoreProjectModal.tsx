import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { IPromptModalProps } from '@admin/utils/modals';

interface IProps extends IPromptModalProps {
    project: IProject;
}

const RestoreProjectModal: React.FC<IProps> = ({ project, onSuccess, onCancelled }) => {
    const restoreProject = React.useCallback(async () => {
        try {
            await createAuthRequest().post(`/projects/restore/${project.id}`, {});

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

            onCancelled();
        }
    }, [project, onCancelled]);

    const displayPrompt = React.useCallback(async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to restore the "${project.project}" project?`,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await restoreProject();

            onSuccess();
        } else {
            onCancelled();
        }
    }, [project, restoreProject, onSuccess, onCancelled]);

    React.useEffect(() => {
        displayPrompt();
    }, []);

    return (
        <>

        </>
    );
}

export default RestoreProjectModal;
