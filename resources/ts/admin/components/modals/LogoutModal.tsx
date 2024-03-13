import { IPromptModalProps } from '@admin/utils/modals';
import React from 'react';
import { Modal, Button, ModalHeader, ModalBody, ModalFooter } from 'reactstrap';

interface IProps extends IPromptModalProps {
}

const LogoutModal: React.FC<IProps> = ({ onSuccess, onCancelled}) => (
    <Modal isOpen={true} toggle={() => onCancelled()}>
        <ModalHeader toggle={() => onCancelled()}>
            Ready to Leave?
        </ModalHeader>
        <ModalBody>
            Select &quot;Logout&quot; below if you are ready to end your current session.
        </ModalBody>
        <ModalFooter>
            <Button color="secondary" onClick={() => onCancelled()}>
                Cancel
            </Button>
            {' '}
            <Button color="primary" onClick={() => onSuccess()}>
                Logout
            </Button>
        </ModalFooter>
    </Modal>
)

export default LogoutModal;
