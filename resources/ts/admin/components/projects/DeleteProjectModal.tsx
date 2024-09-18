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

const DeleteProjectModal: React.FC<IProps> = ({ project, onSuccess, onCancelled }) => {
    const deleteProject = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().delete(`/projects/${project.id}`);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Deleted',
                text: `The "${project.project}" project has been removed.`,
            });
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `Unable to remove the "${project.project}" project: ${message}`,
            });

            onCancelled();
        }
    }, [project, onCancelled]);

    const displayPrompt = React.useCallback(async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to delete the "${project.project}" project?`,
            confirmButtonColor: 'danger',
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await deleteProject();

            onSuccess();
        } else {
            onCancelled();
        }
    }, [project, deleteProject, onSuccess, onCancelled]);

    React.useEffect(() => {
        displayPrompt();
    }, []);

    return (
        <>

        </>
    );
}

export default DeleteProjectModal;
