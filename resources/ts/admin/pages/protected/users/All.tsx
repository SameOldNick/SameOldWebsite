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
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import withRouter, { IHasRouter } from '@admin/components/hoc/withRouter';
import PaginatedTable from '@admin/components/PaginatedTable';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { createAuthRequest } from '@admin/utils/api/factories';
import User from '@admin/utils/api/models/User';


interface IProps extends IHasRouter {

}

interface IState {
    show: string;
}

interface IUserProps {
    user: User;
    onLocked: () => void;
    onUnlocked: () => void;
}

export default withRouter(class All extends React.Component<IProps, IState> {
    static User: React.FC<IUserProps> = ({ user, onLocked, onUnlocked }) => {
        const [showLockPrompt, setShowLockPrompt] = React.useState(false);
        const [showUnlockPrompt, setShowUnlockPrompt] = React.useState(false);

        const onLockClicked = () => {
            setShowLockPrompt(true);
        }

        const onLockModalLocked = () => {
            setShowLockPrompt(false);
            onLocked();
        }

        const onLockModalCanceled = () => {
            setShowLockPrompt(false);
        }

        const onUnlockClicked = () => {
            setShowUnlockPrompt(true);
        }

        const onUnlockModalUnlocked = () => {
            setShowUnlockPrompt(false);
            onUnlocked();
        }

        const onUnlockModalCanceled = () => {
            setShowUnlockPrompt(false);
        }

        return (
            <>
                {showLockPrompt && <LockUserModal user={user} onLocked={onLockModalLocked} onCanceled={onLockModalCanceled} />}
                {showUnlockPrompt && <UnlockUserModal user={user} onUnlocked={onUnlockModalUnlocked} onCanceled={onUnlockModalCanceled} />}

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
                                <Button color='danger' onClick={onLockClicked} title='Lock account'>
                                    <FaLock />
                                </Button>
                            </>
                        )}

                        {user.deletedAt && (
                            <>
                                <Button color='primary' onClick={onUnlockClicked} title='Unlock account'>
                                    <FaUnlock />
                                </Button>
                            </>
                        )}
                    </td>
                </tr>
            </>
        );
    }

    private _waitToLoadRef = React.createRef<WaitToLoad<IPaginateResponseCollection<IUser>>>();

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            show: 'both'
        };

        this.fetchUsers = this.fetchUsers.bind(this);
        this.handleError = this.handleError.bind(this);
        this.onUpdateFormSubmitted = this.onUpdateFormSubmitted.bind(this);
    }

    private async fetchUsers(link?: string) {
        const { show } = this.state;

        const response = await createAuthRequest().get<IPaginateResponseCollection<IUser>>(link ?? 'users', { show });

        return response.data;
    }

    private reloadUsers() {
        this._waitToLoadRef.current?.load();
    }

    private async onUpdateFormSubmitted(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();

        this.reloadUsers();
    }

    private handleError(err: unknown) {
        const { router: { navigate } } = this.props;

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
                this._waitToLoadRef.current?.load();
            } else {
                navigate(-1);
            }
        });
    }

    public render() {
        const { show } = this.state;

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
                                    <Form className="row row-cols-lg-auto g-3" onSubmit={this.onUpdateFormSubmitted}>
                                        <Col xs={12}>
                                            <label className="visually-hidden" htmlFor="show">Show</label>

                                            <Input type='select' name='show' id='show' value={show} onChange={(e) => this.setState({ show: e.target.value })}>
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
                                    ref={this._waitToLoadRef}
                                    loading={<Loader display={{ type: 'over-element' }} />}
                                    callback={this.fetchUsers}
                                >
                                    {(response, err, { reload }) => (
                                        <>
                                            {err !== undefined && this.handleError(err)}
                                            {response !== undefined && (
                                                <PaginatedTable initialResponse={response} pullData={this.fetchUsers}>
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
                                                                    <All.User key={index} user={new User(user)} onLocked={reload} onUnlocked={reload} />)
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
});
