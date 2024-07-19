import React from 'react';
import { Button, ListGroup, ListGroupItem, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';

import S from 'string';

import { IPromptModalProps } from '@admin/utils/modals';

export interface ISelectRolesModalProps extends IPromptModalProps<TRole[]> {
    roles: TRole[];
}

const SelectRolesModal: React.FC<ISelectRolesModalProps> = ({ roles, onSuccess, onCancelled }) => {
    const [selected, setSelected] = React.useState<TRole[]>(roles);

    const availableRoles = React.useMemo<TRole[]>(() => ([
        "change_avatar",
        "change_contact_settings",
        "edit_profile",
        "manage_backups",
        "manage_comments",
        "manage_images",
        "manage_projects",
        "manage_users",
        "receive_contact_messages",
        "view_contact_messages",
        "write_posts",
    ]), []);

    const handleRoleClicked = React.useCallback((e: React.MouseEvent, role: TRole) => {
        e.preventDefault();

        setSelected((value) => value.includes(role) ? value.filter((el) => el !== role) : value.concat(role));
    }, []);

    return (
        <>
            <Modal isOpen scrollable backdrop='static'>
                <ModalHeader>
                    Select Roles
                </ModalHeader>
                <ModalBody>
                    <ListGroup>
                        {availableRoles.map((role, index) => (
                            <ListGroupItem
                                key={index}
                                action
                                active={selected.includes(role)}
                                tag='a'
                                href='#'
                                onClick={(e) => handleRoleClicked(e, role)}
                            >
                                {S(role).humanize().s}
                            </ListGroupItem>
                        ))}
                    </ListGroup>
                </ModalBody>
                <ModalFooter>
                    <Button color="primary" disabled={selected === undefined} onClick={() => selected && onSuccess(selected)}>
                        Select
                    </Button>{' '}
                    <Button color="secondary" onClick={onCancelled}>
                        Cancel
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default SelectRolesModal;
