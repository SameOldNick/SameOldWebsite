import React from 'react';
import { Helmet } from 'react-helmet';
import { FormikHelpers } from 'formik';
import { Card, CardBody } from 'reactstrap';
import { useNavigate } from 'react-router-dom';
import withReactContent from 'sweetalert2-react-content';

import axios from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import ProjectForm, { IFormikValues, IOnSubmitValues } from '@admin/components/projects/ProjectForm';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

const Create: React.FC = () => {
    const navigate = useNavigate();

    const onCreated = React.useCallback(async (project: IProject) => {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Project Created',
            text: 'The project was successfully created.',
        });

        navigate(`/admin/projects/edit/${project.id}`)
    }, []);

    const onError = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred: ${message}`,
        });
    }, []);

    const handleSubmit = React.useCallback(async ({ name, description, url, tags }: IOnSubmitValues, { }: FormikHelpers<IFormikValues>) => {
        try {
            const response = await createAuthRequest().post<IProject>('projects', {
                title: name,
                description,
                url,
                tags: tags.map(({ label }) => label)
            });

            await onCreated(response.data);
        } catch (e) {
            await onError(e);
        }
    }, [onCreated, onError]);

    const initialValues = React.useMemo(() => ({
        name: '',
        description: '',
        url: ''
    }), []);

    return (
        <>
            <Helmet>
                <title>Create Project</title>
            </Helmet>

            <Heading title='Create Project' />

            <Card>
                <CardBody>
                    <ProjectForm
                        buttonContent='Create Project'
                        initialValues={initialValues}
                        onSubmit={handleSubmit}
                    />
                </CardBody>
            </Card>
        </>
    );
}

export default requiresRolesForPage(Create, ['manage_projects']);
