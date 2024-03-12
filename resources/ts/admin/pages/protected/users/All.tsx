import React from 'react';
import { Helmet } from 'react-helmet';
import { Button, Card, CardBody, Col, Form, Input, Row, Table } from 'reactstrap';
import { FaEdit, FaLock, FaPlus, FaRedo, FaUnlock } from 'react-icons/fa';
import { NavLink } from 'react-router-dom';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';
import classNames from 'classnames';
import S from 'string';

import Heading from '@admin/layouts/admin/Heading';
import LockUserModal from '@admin/components/users/LockUserModal';
import UnlockUserModal from '@admin/components/users/UnlockUserModal';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import PaginatedTable from '@admin/components/PaginatedTable';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { createAuthRequest } from '@admin/utils/api/factories';
import UserModel from '@admin/utils/api/models/User';
import awaitModalPrompt from '@admin/utils/modals';

interface IProps extends IHasRouter {

}

interface IUserProps {
    user: UserModel;
    onLocked: () => void;
    onUnlocked: () => void;
}

const User: React.FC<IUserProps> = ({ user, onLocked, onUnlocked }) => {
    const handleLockClicked = async () => {
        try {
            await awaitModalPrompt(LockUserModal, { user });

            onLocked();
        } catch (err) {
            // Modal was cancelled.
        }
    }

    const handleUnlockClicked = async () => {
        try {
            await awaitModalPrompt(UnlockUserModal, { user });

            onUnlocked();
        } catch (err) {
            // Modal was cancelled.
        }
    }

    return (
        <>
            <tr>
                <td>{user.user.id}</td>
                <td className={classNames({ 'text-muted': !user.user.name })}>{user.user.name || '(Empty)'}</td>
                <td>{user.user.email}</td>
                <td title={user.createdAt.toISO() || ''}>{user.createdAt.toLocaleString()}</td>
                <td>{S(user.status).humanize().s}</td>
                <td>
                    {!user.deletedAt && (
                        <>
                            <Button color='primary' tag={NavLink} to={user.generatePath()} className='me-1'>
                                <FaEdit />
                            </Button>
                            <Button color='danger' onClick={handleLockClicked} title='Lock account'>
                                <FaLock />
                            </Button>
                        </>
                    )}

                    {user.deletedAt && (
                        <>
                            <Button color='primary' onClick={handleUnlockClicked} title='Unlock account'>
                                <FaUnlock />
                            </Button>
                        </>
                    )}
                </td>
            </tr>
        </>
    );
}

const All: React.FC<IProps> = ({ router: { navigate } }) => {
    const waitToLoadRef = React.createRef<IWaitToLoadHandle>();

    const [show, setShow] = React.useState('both');

    const fetchUsers = async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IUser>>(link ?? 'users', { show });

        return response.data;
    }

    const reloadUsers = () => {
        waitToLoadRef.current?.load();
    }

    const handleUpdateFormSubmitted = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        reloadUsers();
    }

    const handleError = (err: unknown) => {
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
                waitToLoadRef.current?.load();
            } else {
                navigate(-1);
            }
        });
    }

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

                                        <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value)}>
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
                            <WaitToLoad
                                ref={waitToLoadRef}
                                loading={<Loader display={{ type: 'over-element' }} />}
                                callback={fetchUsers}
                            >
                                {(response, err, { reload }) => (
                                    <>
                                        {err !== undefined && handleError(err)}
                                        {response !== undefined && (
                                            <PaginatedTable initialResponse={response} pullData={fetchUsers}>
                                                {(data) => (
                                                    <Table>
                                                        <thead>
                                                            <tr>
                                                                {/* TODO: Sort columns */}
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>E-mail</th>
                                                                <th>Created</th>
                                                                <th>Status</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {data.length === 0 && (
                                                                <tr>
                                                                    <tr>
                                                                        <td colSpan={6} className='text-center text-muted'>(No users found)</td>
                                                                    </tr>
                                                                </tr>
                                                            )}
                                                            {data.length > 0 && data.map((user, index) => (
                                                                <User key={index} user={new UserModel(user)} onLocked={reload} onUnlocked={reload} />)
                                                            )}
                                                        </tbody>
                                                    </Table>
                                                )}

                                            </PaginatedTable>
                                        )}
                                    </>
                                )}
                            </WaitToLoad>
                        </Col>
                    </Row>

                </CardBody>
            </Card>
        </>
    );
}

export default withRouter(All);
