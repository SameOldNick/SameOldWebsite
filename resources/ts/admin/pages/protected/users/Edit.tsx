import React from 'react';
import { Helmet } from 'react-helmet';
import { FormikHelpers } from 'formik';
import withReactContent from 'sweetalert2-react-content';

import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import withRouter, { IHasRouter } from '@admin/components/hoc/WithRouter';
import UserForm, { IFormikValues, TForwardedRef } from '@admin/components/users/UserForm';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { Card, CardBody, Col, Row } from 'reactstrap';

interface IProps extends IHasRouter<'user'> {

}

interface IState {
}

export default withRouter(class extends React.Component<IProps, IState> {
    private _waitToLoadRef = React.createRef<WaitToLoad<IUser>>();
    private _formikRef = React.createRef<TForwardedRef>();

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };

        this.getUser = this.getUser.bind(this);
    }

    private async getUser() {
        const { router: { params: { user } } } = this.props;

        const response = await createAuthRequest().get<IUser>(`/users/${user}`);

        return response.data;
    }

    private async handleError(err: unknown) {
        const { router: { navigate } } = this.props;

        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `Unable to retrieve user: ${message}`,
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

    private getInitialValues(user: IUser) {
        return {
            name: user.name,
            email: user.email,
            password: '',
            confirm_password: '',
            state: user.state?.code || '',
            country: user.country?.code || '',
            roles: user.roles.map(({ role }) => role)
        }
    }

    private async onSubmit(user: IUser, { name, email, password, confirm_password, state, country, roles }: IFormikValues, helpers: FormikHelpers<IFormikValues>) {
        try {
            const response = await createAuthRequest().put<IUser>(`users/${user.id}`, {
                name,
                email,
                password,
                password_confirmation: confirm_password,
                state_code: state,
                country_code: country,
                roles
            });

            await this.onUpdated(response);
        } catch (e) {
            await this.onError(e);
        }
    }

    private async onUpdated(response: AxiosResponse<IUser>) {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'User Updated',
            text: 'The user was successfully updated.',
        });

        this._formikRef.current?.resetForm();

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
                    <title>Edit User</title>
                </Helmet>

                <Heading>
                    <Heading.Title>Edit User</Heading.Title>
                </Heading>

                <Row className='justify-content-center'>
                    <Col md={8}>
                        <Card>
                            <CardBody>
                                <WaitToLoad<IUser> ref={this._waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={this.getUser}>
                                    {(user, err) => (
                                        <>
                                            {err !== undefined && this.handleError(err)}
                                            {
                                                user !== undefined &&
                                                <UserForm
                                                    innerRef={this._formikRef}
                                                    fields='edit'
                                                    initialValues={this.getInitialValues(user)}
                                                    buttonContent='Edit User'
                                                    onSubmit={(values, helpers) => this.onSubmit(user, values, helpers)}
                                                />
                                            }
                                        </>
                                    )}
                                </WaitToLoad>
                            </CardBody>
                        </Card>
                    </Col>
                </Row>

            </>
        );
    }
});
