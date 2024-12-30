import React from 'react';
import { Helmet } from 'react-helmet';
import { FormikHelpers } from 'formik';
import withReactContent from 'sweetalert2-react-content';
import { Card, CardBody, Col, Row } from 'reactstrap';

import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';

import Heading from '@admin/layouts/admin/Heading';
import Loader from '@admin/components/Loader';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import UserForm, { IFormikValues, TForwardedRef } from '@admin/components/users/UserForm';
import WaitToLoad, { IWaitToLoadHelpers } from '@admin/components/WaitToLoad';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import User from '@admin/utils/api/models/User';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

const Edit: React.FC<IHasRouter<'user'>> = ({ router }) => {
    const [renderCount, setRenderCount] = React.useState(0);
    const formikRef = React.useRef<TForwardedRef>(null);

    const getUser = React.useCallback(async () => {
        const { params: { user } } = router;

        const response = await createAuthRequest().get<IUser>(`/users/${user}`);

        return new User(response.data);
    }, [router.params]);

    const handleErrorGettingUser = React.useCallback(async (err: unknown, { reload }: IWaitToLoadHelpers) => {
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
            await reload();
        } else {
            router.navigate(-1);
        }
    }, [router.navigate]);

    const getInitialValues = React.useCallback((user: User) => ({
        name: user.user.name || '',
        email: user.user.email || '',
        password: '',
        confirm_password: '',
        state: user.user.state?.code || '',
        country: user.user.country?.code || '',
        roles: user.roles || []
    }), []);

    React.useEffect(() => {
        getUser();
    }, [router.params.user]);

    const handleUpdated = React.useCallback(async (response: AxiosResponse<IUser>) => {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'User Updated',
            text: 'The user was successfully updated.',
        });

        setRenderCount((prev) => prev + 1);
    }, []);

    const handleErrorUpdatingUser = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: message,
        });
    }, []);

    const handleSubmit = React.useCallback(async (user: User, { name, email, password, confirm_password, state, country, roles }: IFormikValues, helpers: FormikHelpers<IFormikValues>) => {
        try {
            const response = await createAuthRequest().put<IUser>(`users/${user.user.id}`, {
                name,
                email,
                password,
                password_confirmation: confirm_password,
                state_code: state,
                country_code: country,
                roles
            });

            await handleUpdated(response);
        } catch (e) {
            await handleErrorUpdatingUser(e);
        }
    }, [handleUpdated, handleErrorUpdatingUser]);

    return (
        <>
            <Helmet>
                <title>Edit User</title>
            </Helmet>

            <Heading title='Edit User' />

            <Row className='justify-content-center'>
                <Col md={8}>
                    <Card>
                        <CardBody>
                            <WaitToLoad key={renderCount} loading={<Loader display={{ type: 'over-element' }} />} callback={getUser}>
                                {(user, err, helpers) => (
                                    <>
                                        {user && (
                                            <UserForm
                                                ref={formikRef}
                                                fields='edit'
                                                initialValues={getInitialValues(user)}
                                                buttonContent='Edit User'
                                                onSubmit={(values, helpers) => handleSubmit(user, values, helpers)}
                                            />
                                        )}
                                        {err && handleErrorGettingUser(err, helpers)}
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

export default requiresRolesForPage(withRouter(Edit), ['manage_users']);
