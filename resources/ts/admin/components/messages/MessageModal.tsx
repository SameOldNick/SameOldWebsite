import React from 'react';
import { Badge, Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import ContactMessage from '@admin/utils/api/models/ContactMessage';
import { IPromptModalProps } from '@admin/utils/modals';

interface IModalProps extends IPromptModalProps {
    message: ContactMessage;
}

const MessageModal: React.FC<IModalProps> = ({ message, onSuccess }) => {
    const toggle = React.useCallback(() => onSuccess(), [onSuccess]);

    const badges = React.useMemo(() => {
        const badges = [];

        if (message.confirmedAt) {
            badges.push(
                <Badge title={message.confirmedAt.toLocaleString()} color='success' className='me-1'>
                    {`Confirmed ${message.confirmedAt.isAfter() ? message.confirmedAt.fromNow() : message.confirmedAt.toNow()}`}
                </Badge>
            );
        }

        if (message.expiresAt) {
            if (message.expiresAt.isAfter()) {
                badges.push(
                    <Badge title={message.expiresAt.toLocaleString()} color='info' className='me-1'>
                        {`Expires ${message.expiresAt.fromNow()}`}
                    </Badge>
                );
            } else {
                badges.push(
                    <Badge title={message.expiresAt.toLocaleString()} color='warning' className='me-1'>
                        {`Expired ${message.expiresAt.toNow()}`}
                    </Badge>
                );
            }

        }

        return badges;
    }, [message]);

    return (
        <Modal isOpen={true} toggle={toggle} scrollable size='xl'>
            <ModalHeader>Message</ModalHeader>
            <ModalBody>
                <Row>
                    <Col xs={12}>
                        {badges}
                    </Col>
                </Row>

                <Row>
                    <Col md={6}>
                        <FormGroup>
                            <Label for='subject'>Name:</Label>
                            <Input id='subject' type='text' value={message.message.name} readOnly />
                        </FormGroup>
                    </Col>
                    <Col md={6}>
                        <FormGroup>
                            <Label for='subject'>E-mail:</Label>
                            <Input id='subject' type='text' value={message.message.email} readOnly />
                        </FormGroup>
                    </Col>
                </Row>

                <Row>
                    <FormGroup>
                        <Label for='textMessage'>Message:</Label>
                        <Input id='textMessage' type='textarea' value={message.message.message} readOnly rows={6} />
                    </FormGroup>
                </Row>

            </ModalBody>

            <ModalFooter>
                <Button color='primary' onClick={toggle}>Close</Button>
            </ModalFooter>
        </Modal >
    );
}

export default MessageModal;
