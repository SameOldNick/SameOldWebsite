import React from 'react';
import { Badge, Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import ContactMessage from '@admin/utils/api/models/ContactMessage';
import { IPromptModalProps } from '@admin/utils/modals';
import { DateTime } from 'luxon';

interface IModalProps extends IPromptModalProps {
    message: ContactMessage;
}

const MessageModal: React.FC<IModalProps> = ({ message, onSuccess }) => {
    const toggle = React.useCallback(() => onSuccess(), [onSuccess]);

    const badges = React.useMemo(() => {
        const badges = [];

        const {
            confirmedAt,
            expiresAt
        } = message;

        if (confirmedAt) {
            badges.push(
                <Badge title={confirmedAt.toLocaleString()} color='success' className='me-1'>
                    {`Confirmed ${confirmedAt > DateTime.now() ? confirmedAt.toRelative() : confirmedAt.toRelative({ base: DateTime.now() })}`}
                </Badge>
            );
        }

        if (expiresAt) {
            if (expiresAt > DateTime.now()) {
                badges.push(
                    <Badge title={expiresAt.toLocaleString(DateTime.DATETIME_MED)} color='info' className='me-1'>
                        {`Expires ${expiresAt.toRelative()}`}
                    </Badge>
                );
            } else {
                badges.push(
                    <Badge title={expiresAt.toLocaleString(DateTime.DATETIME_MED)} color='warning' className='me-1'>
                        {`Expired ${expiresAt.toRelative({ base: DateTime.now() })}`}
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
