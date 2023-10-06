import React from 'react';
import { Helmet } from 'react-helmet';
import { FormikHelpers } from 'formik';
import { Card, CardBody } from 'reactstrap';
import { Navigate } from 'react-router-dom';
import withReactContent from 'sweetalert2-react-content';
import { Tag } from 'react-tag-autocomplete';

import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import ProjectForm, { IFormikValues, IOnSubmitValues } from '@admin/components/projects/ProjectForm';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface IProps {

}

interface IState {
    tags: Tag[];
    project?: IProject;
}

export default class Create extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            tags: []
        };

        this.onSubmit = this.onSubmit.bind(this);
    }

    private get initialValues() {
        return {
            name: '',
            description: '',
            url: ''
        };
    }

    private async onSubmit({ name, description, url, tags }: IOnSubmitValues, helpers: FormikHelpers<IFormikValues>) {
        try {
            const response = await createAuthRequest().post<IProject>('projects', {
                title: name,
                description,
                url,
                tags: tags.map(({ label }) => label)
            });

            await this.onCreated(response);
        } catch (e) {
            await this.onError(e);
        }
    }

    private async onCreated(response: AxiosResponse<IProject>) {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Project Created',
            text: 'The project was successfully created.',
        });

        this.setState({ project: response.data });
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
        const { project } = this.state;

        if (project !== undefined) {
            return (
                <Navigate to={`/admin/projects/edit/${project.id}`} />
            );
        }

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
                            initialValues={this.initialValues}
                            onSubmit={this.onSubmit}
                        />
                    </CardBody>
                </Card>
            </>
        );
    }
}
