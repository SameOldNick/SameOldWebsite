import React from 'react';
import { Helmet } from 'react-helmet';
import withReactContent from 'sweetalert2-react-content';
import { Tag } from 'react-tag-autocomplete';
import { FormikHelpers } from 'formik';

import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import { withRouter, IHasRouter } from '@admin/components/hoc/withRouter';
import ProjectForm, { IFormikValues, IOnSubmitValues } from '@admin/components/projects/ProjectForm';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

const Edit: React.FC<IHasRouter<'project'>> = ({ router: { navigate, params: { project } } }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);

    const getProject = React.useCallback(async () => {
        const response = await createAuthRequest().get<IProject>(`/projects/${project}`);

        return response.data;
    }, [project]);

    const handleError = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `Unable to retrieve project: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            waitToLoadRef.current?.load();
        } else {
            navigate(-1);
        }
    }, [waitToLoadRef.current, navigate]);

    const getInitialValues = React.useCallback((project: IProject) => ({
        name: project.project,
        description: project.description,
        url: project.url
    }), []);

    const transformProjectTags = React.useCallback((tags: ITag[]): Tag[] => {
        return tags.map(({ tag, slug }, index) => ({ label: tag, value: slug ?? index }));
    }, []);

    const onUpdated = React.useCallback(async (_response: AxiosResponse<IProject>) => {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Project Updated',
            text: 'The project was successfully updated.',
        });

        waitToLoadRef.current?.load();
    }, [waitToLoadRef.current]);

    const onError = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred: ${message}`,
        });
    }, []);

    const onSubmit = React.useCallback(async (project: IProject, { name, description, url, tags }: IOnSubmitValues, _helpers: FormikHelpers<IFormikValues>) => {
        try {
            const response = await createAuthRequest().put<IProject>(`projects/${project.id}`, {
                title: name,
                description,
                url,
                tags: tags.map(({ label }) => label)
            });

            await onUpdated(response);
        } catch (e) {
            await onError(e);
        }
    }, [onUpdated, onError]);

    return (
        <>
            <Helmet>
                <title>Edit Project</title>
            </Helmet>

            <Heading title='Edit Project' />

            <>
                <WaitToLoad<IProject> ref={waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={getProject}>
                    {(project, err) => (
                        <>
                            {err !== undefined && handleError(err)}
                            {
                                project !== undefined &&
                                <ProjectForm
                                    initialValues={getInitialValues(project)}
                                    initialTags={transformProjectTags(project.tags)}
                                    buttonContent='Edit Project'
                                    onSubmit={(values, helpers) => onSubmit(project, values, helpers)}
                                />
                            }
                        </>
                    )}
                </WaitToLoad>
            </>
        </>
    );
}

export default requiresRolesForPage(withRouter(Edit), ['manage_projects']);
