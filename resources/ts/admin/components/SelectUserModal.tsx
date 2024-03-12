import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Row, Col, Input, Form, Table, InputGroup } from 'reactstrap';
import { FaSearch } from 'react-icons/fa';

import classNames from 'classnames';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import PaginatedTable from '@admin/components/PaginatedTable';

import { createAuthRequest } from '@admin/utils/api/factories';

import User from '@admin/utils/api/models/User';

interface ISelectUserModalAllowAllProps {
    allowAll: true;
    onSelected: (user?: User) => void;
}

interface ISelectUserModalSpecificProps {
    allowAll?: false;
    onSelected: (user: User) => void;
}

interface ISelectUserModalSharedProps {
    existing?: User;
    onCancelled: () => void;
}

type TSelectUserModalProps = (ISelectUserModalAllowAllProps | ISelectUserModalSpecificProps) & ISelectUserModalSharedProps;

interface IUserRowProps {
    user: User;
    selected: boolean;
    onSelected: (selected: boolean, user: User) => void;
}

const UserRow: React.FC<IUserRowProps> = ({ user, selected, onSelected }) => {
    const tdClassName = React.useMemo(() => classNames({ 'bg-secondary': selected }), [selected]);

    return (
        <tr
            onClick={() => onSelected(!selected, user)}
            style={{ cursor: 'pointer' }}
        >
            <th scope='row' className={tdClassName}>{user.user.id}</th>
            <td className={classNames(tdClassName, { 'text-muted': !user.user.name })}>{user.user.name || '(Empty)'}</td>
            <td className={tdClassName}>{user.user.email}</td>
        </tr>
    );
}

const SelectUserModal: React.FC<TSelectUserModalProps> = ({ existing, allowAll, onSelected, onCancelled }) => {
    const waitToLoadUsersRef = React.createRef<IWaitToLoadHandle>();
    const paginatedTableRef = React.createRef<PaginatedTable<IUser>>();

    const [selected, setSelected] = React.useState<User | undefined>(existing);
    const [show, setShow] = React.useState('both');
    const [search, setSearch] = React.useState('');

    const loadUsers = async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IUser>>(link ?? 'users', { show });

        return response.data;
    }

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (allowAll) {
            onSelected(selected);
        } else {
            if (!selected) {
                console.error('No user selected.');
                return;
            }

            onSelected(selected);
        }
    }

    const passUsersThru = (users: IUser[]) => {
        return users
            .map((user) => new User(user))
            .filter((user) =>
                user.user.id?.toString().includes(search) ||
                user.user.name.includes(search) ||
                user.user.email.includes(search)
            );
    }

    const handleUserSelected = (selected: boolean, user: User) => {
        setSelected(selected ? user : undefined);
    }

    return (
        <>
            <Modal isOpen backdrop='static' size='xl'>
                <Form onSubmit={handleSubmit}>
                    <ModalHeader>
                        Select User
                    </ModalHeader>
                    <ModalBody>
                        <Row>
                            <Col xs={12}>
                                <div className="row row-cols-xl-auto g-3">
                                    <Col xs={12}>
                                        <InputGroup>
                                            <Input
                                                name='search'
                                                id='search'
                                                onChange={(e) => setSearch(e.currentTarget.value)}
                                                onBlur={(e) => setSearch(e.currentTarget.value)}
                                            />
                                            <Button
                                                type='button'
                                                color='primary'
                                                onClick={() => paginatedTableRef.current?.reload()}
                                            >
                                                <FaSearch />
                                            </Button>
                                        </InputGroup>
                                    </Col>
                                </div>
                            </Col>
                            <Col xs={12}>
                                <WaitToLoad
                                    ref={waitToLoadUsersRef}
                                    callback={loadUsers}
                                    loading={<Loader display={{ type: 'over-element' }} />}
                                >
                                    {(response, err) => (
                                        <>
                                            {err && console.error(err)}
                                            {response && (
                                                <PaginatedTable ref={paginatedTableRef} initialResponse={response} pullData={loadUsers}>
                                                    {(data) => (
                                                        <Table hover>
                                                            <thead>
                                                                <tr>
                                                                    <th scope='col'>ID</th>
                                                                    <th scope='col'>Name</th>
                                                                    <th scope='col'>E-mail</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {allowAll && (
                                                                    <tr style={{ cursor: 'pointer' }} onClick={() => setSelected(undefined)}>
                                                                        <td
                                                                            colSpan={4}
                                                                            className={classNames('text-center fw-bold', { 'bg-secondary': selected === undefined })}
                                                                        >
                                                                            All Users
                                                                        </td>
                                                                    </tr>
                                                                )}
                                                                {passUsersThru(data).map((user, index) => (
                                                                    <UserRow
                                                                        key={index}
                                                                        user={user}
                                                                        selected={selected ? selected.user.id === user.user.id : false}
                                                                        onSelected={handleUserSelected}
                                                                    />
                                                                ))}
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
                    </ModalBody>
                    <ModalFooter>
                        <Button type='submit' color="primary" disabled={!allowAll && !selected}>
                            Select
                        </Button>{' '}
                        <Button color="secondary" onClick={onCancelled}>
                            Cancel
                        </Button>
                    </ModalFooter>
                </Form>
            </Modal>
        </>
    );
}

export default SelectUserModal;
