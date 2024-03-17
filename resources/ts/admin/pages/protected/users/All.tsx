import React from 'react';
import { Helmet } from 'react-helmet';
import { Button, Card, CardBody, Col, Form, Input, Row } from 'reactstrap';
import { FaPlus, FaRedo } from 'react-icons/fa';
import { NavLink } from 'react-router-dom';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import Heading from '@admin/layouts/admin/Heading';
import { IWaitToLoadHelpers } from '@admin/components/WaitToLoad';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import UsersList from '@admin/components/users/UsersList';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

interface IProps extends IHasRouter {

}

const All: React.FC<IProps> = ({ router: { navigate } }) => {
    const [show, setShow] = React.useState('both');
    const [renderCount, setRenderCount] = React.useState(0);

    const handleUpdateFormSubmitted = React.useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        setShow(e.currentTarget.show.value);
        setRenderCount((prev) => prev + 1);
    }, []);

    const handleLoadError = React.useCallback((err: unknown, { reload }: IWaitToLoadHelpers) => {
        console.error(err);

        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `Unable to retrieve users: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                reload();
            } else {
                navigate(-1);
            }
        });
    }, [navigate]);

    return (
        <>
            <Helmet>
                <title>All Users</title>
            </Helmet>

            <Heading title='All Users' />

            <Card>
                <CardBody>
                    <Row>
                        <Col xs={12} className='d-flex justify-content-between mb-3'>
                            <div>
                                <Button tag={NavLink} to='create' color='primary'>
                                    <FaPlus /> Create New
                                </Button>
                            </div>
                            <div className="text-end">
                                <Form className="row row-cols-lg-auto g-3" onSubmit={handleUpdateFormSubmitted}>
                                    <Col xs={12}>
                                        <label className="visually-hidden" htmlFor="show">Show</label>

                                        <Input type='select' name='show' id='show'>
                                            <option value="active">Active Only</option>
                                            <option value="inactive">Inactive Only</option>
                                            <option value="both">Both</option>
                                        </Input>
                                    </Col>
                                    <Col xs={12}>
                                        <Button type='submit' color='primary'>
                                            <FaRedo /> Update
                                        </Button>
                                    </Col>
                                </Form>

                            </div>
                        </Col>
                        <Col xs={12}>
                            <UsersList key={renderCount} show={show} onLoadError={handleLoadError} />
                        </Col>
                    </Row>

                </CardBody>
            </Card>
        </>
    );
}

export default requiresRolesForPage(withRouter(All), ['manage_users']);
