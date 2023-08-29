import React from 'react';
import { Modal, Button, ModalHeader, ModalBody, ModalFooter } from 'reactstrap';

interface IProps {
    show: boolean;
    onLogout: VoidFunction;
    onCancel: VoidFunction;
}

const LogoutModal: React.FC<IProps> = ({ show, onCancel, onLogout }) => (
    <Modal isOpen={show} toggle={onCancel}>
        <ModalHeader toggle={onCancel}>
            Ready to Leave?
        </ModalHeader>
        <ModalBody>
            Select "Logout" below if you are ready to end your current session.
        </ModalBody>
        <ModalFooter>
            <Button color="secondary" onClick={onCancel}>
                Cancel
            </Button>
            {' '}
            <Button color="primary" onClick={onLogout}>
                Logout
            </Button>
        </ModalFooter>
    </Modal>
)

export default LogoutModal;
