import React from 'react';
import { Modal, Button, ModalHeader, ModalBody, ModalFooter } from 'reactstrap';

import { IPromptModalProps } from '@admin/utils/modals';

const LogoutModal: React.FC<IPromptModalProps> = ({ onSuccess, onCancelled }) => (
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
