import React from 'react';
import { Navigate } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import withReactContent from 'sweetalert2-react-content';
import { FormikHelpers } from 'formik';
import { Card, CardBody, Col, Row } from 'reactstrap';

import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import UserForm, { IFormikValues } from '@admin/components/users/UserForm';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface IProps {

}

interface IState {
    user?: IUser;
}

export default class extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };

        this.onFormSubmit = this.onFormSubmit.bind(this);
    }

    private get initialValues() {
        return {
            name: '',
            email: '',
            password: '',
            confirm_password: '',
            state: '',
            country: '',
            roles: []
        }
    }

    private async onFormSubmit({ name, email, password, confirm_password, state, country }: IFormikValues, { }: FormikHelpers<IFormikValues>) {
        try {
            const response = await createAuthRequest().post<IUser>('/users', {
                name,
                email,
                password,
                password_confirmation: confirm_password,
                state_code: state,
                country_code: country
            });

            await this.onCreated(response);
        } catch (e) {
            await this.onError(e);
        }
    }

    private async onCreated(response: AxiosResponse<IUser>) {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'User Created',
            text: 'The user was successfully created.',
        });

        this.setState({ user: response.data });
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
        const { user } = this.state;

        if (user !== undefined) {
            return (
                <Navigate to={`/admin/users/edit/${user.id}`} />
            );
        }

        return (
            <>
                <Helmet>
                    <title>Create User</title>
                </Helmet>

                <Heading title='Create User' />

                <Row className='justify-content-center'>
                    <Col md={8}>
                        <Card>
                            <CardBody>
                                <UserForm fields='create' initialValues={this.initialValues} buttonContent='Create User' onSubmit={this.onFormSubmit} />

                            </CardBody>
                        </Card>
                    </Col>
                </Row>



            </>
        );
    }
}
