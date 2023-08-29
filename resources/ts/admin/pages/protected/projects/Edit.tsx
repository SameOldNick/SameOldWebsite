import React from 'react';
import { Helmet } from 'react-helmet';
import withReactContent from 'sweetalert2-react-content';
import { Tag } from 'react-tag-autocomplete';
import { FormikHelpers } from 'formik';

import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import withRouter, { IHasRouter } from '@admin/components/hoc/WithRouter';
import ProjectForm, { IFormikValues, IOnSubmitValues } from '@admin/components/projects/ProjectForm';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface IProps extends IHasRouter<'project'> {

}

interface IState {
}

export default withRouter(class extends React.Component<IProps, IState> {
    private _waitToLoadRef = React.createRef<WaitToLoad<IProject>>();

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };

        this.getProject = this.getProject.bind(this);
    }

    private async getProject() {
        const { router: { params: { project }} } = this.props;

        const response = await createAuthRequest().get<IProject>(`/projects/${project}`);

        return response.data;
    }

    private async handleError(err: unknown) {
        const { router: { navigate } } = this.props;

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
            this._waitToLoadRef.current?.load();
        } else {
            navigate(-1);
        }
    }

    private getInitialValues(project: IProject) {
        return {
            name: project.project,
            description: project.description,
            url: project.url
        };
    }

    private transformProjectTags(tags: ITag[]): Tag[] {
        return tags.map(({ tag, slug }, index) => ({ label: tag, value: slug ?? index }));
    }

    private async onSubmit(project: IProject, { name, description, url, tags }: IOnSubmitValues, helpers: FormikHelpers<IFormikValues>) {
        try {
            const response = await createAuthRequest().put<IProject>(`projects/${project.id}`, {
                title: name,
                description,
                url,
                tags: tags.map(({ label }) => label)
            });

            await this.onUpdated(response);
        } catch (e) {
            await this.onError(e);
        }
    }

    private async onUpdated(response: AxiosResponse<IProject>) {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Project Updated',
            text: 'The project was successfully updated.',
        });

        this._waitToLoadRef.current?.load();
    }

    private async onError(err: unknown) {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred: ${message}`,
        });
    }

    public render() {
        const { } = this.props;
        const { } = this.state;

        return (
            <>
                <Helmet>
                    <title>Edit Project</title>
                </Helmet>

                <Heading>
                    <Heading.Title>Edit Project</Heading.Title>


                </Heading>

                <>
                    <WaitToLoad<IProject> ref={this._waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={this.getProject}>
                        {(project, err) => (
                            <>
                                {err !== undefined && this.handleError(err)}
                                {
                                    project !== undefined &&
                                    <ProjectForm
                                        initialValues={this.getInitialValues(project)}
                                        initialTags={this.transformProjectTags(project.tags)}
                                        buttonContent='Edit Project'
                                        onSubmit={(values, helpers) => this.onSubmit(project, values, helpers)}
                                    />
                                }
                            </>
                        )}
                    </WaitToLoad>
                </>
            </>
        );
    }
});
