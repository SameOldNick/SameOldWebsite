import React from 'react';
import { useNavigate } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import withReactContent from 'sweetalert2-react-content';
import { FormikHelpers } from 'formik';
import { Card, CardBody, Col, Row } from 'reactstrap';

import axios from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import UserForm, { IFormikValues } from '@admin/components/users/UserForm';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import User from '@admin/utils/api/models/User';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

interface IProps {

}

const Create: React.FC<IProps> = ({ }) => {
    const navigate = useNavigate();

    const initialValues = React.useMemo(() => ({
        name: '',
        email: '',
        password: '',
        confirm_password: '',
        state: '',
        country: '',
        roles: []
    }), []);

    const onCreated = React.useCallback(async (user: User) => {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'User Created',
            text: 'The user was successfully created.',
        });

        navigate(user.generatePath());
    }, []);

    const onError = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred: ${message}`,
        });
    }, []);

    const handleFormSubmit = React.useCallback(async ({ name, email, password, confirm_password, state, country }: IFormikValues, { }: FormikHelpers<IFormikValues>) => {
        try {
            const response = await createAuthRequest().post<IUser>('/users', {
                name,
                email,
                password,
                password_confirmation: confirm_password,
                state_code: state,
                country_code: country
            });

            await onCreated(new User(response.data));
        } catch (e) {
            await onError(e);
        }
    }, [onCreated, onError]);

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
                            <UserForm fields='create' initialValues={initialValues} buttonContent='Create User' onSubmit={handleFormSubmit} />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default requiresRolesForPage(Create, ['manage_users']);
