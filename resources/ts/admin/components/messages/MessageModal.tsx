import React from 'react';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import S from 'string';

interface IModalProps {
    notification: TMessageNotification;
    onClose: () => void;
}

const MessageModal: React.FC<IModalProps> = ({ notification, onClose }) => {
    const toggle = React.useCallback(() => onClose(), []);

    return (
        <Modal isOpen={true} toggle={toggle} scrollable size='xl'>
            <ModalHeader>Message</ModalHeader>
            <ModalBody>
                {Object.entries(notification.data.addresses).map(([label, addresses], index) => {
                    return addresses.length > 0 && (
                        <FormGroup key={index} tag="fieldset">
                            <legend>{S(label).humanize().s}</legend>

                            {addresses.map(({ address, name }, index) => (
                                <Row key={index}>
                                    <Col xs={6}>
                                        <FormGroup>
                                            <Label for='subject'>E-mail:</Label>
                                            <Input id='subject' type='text' value={address} readOnly />
                                        </FormGroup>
                                    </Col>
                                    <Col xs={6}>
                                        <FormGroup>
                                            <Label for='subject'>Name:</Label>
                                            <Input id='subject' type='text' value={name || ''} readOnly />
                                        </FormGroup>
                                    </Col>
                                </Row>
                            ))}


                        </FormGroup>
                    );
                })}

                <Row>
                    <FormGroup>
                        <Label for='subject'>Subject:</Label>
                        <Input id='subject' type='text' value={notification.data.subject} readOnly />
                    </FormGroup>
                </Row>

                <Row>
                    <FormGroup>
                        <Label for='textMessage'>Text Message:</Label>
                        <Input id='textMessage' type='textarea' value={notification.data.view.text} readOnly rows={6} />
                    </FormGroup>
                </Row>

                <Row>
                    <FormGroup>
                        <Label for='htmlMessage'>HTML Message:</Label>
                        <Input id='htmlMessage' type='textarea' value={notification.data.view.html} readOnly rows={6} />
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
