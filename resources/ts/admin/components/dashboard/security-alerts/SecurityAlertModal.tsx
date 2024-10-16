import React from 'react';
import { Badge, Button, Col, Form, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row, Table } from 'reactstrap';

import S from 'string';
import SecurityNotification from '@admin/utils/api/models/notifications/SecurityNotification';
import { DateTime } from 'luxon';

interface ISecurityAlertModalProps {
    notification: SecurityNotification;
    badgeColor: string;
    onClosed: () => void;
}

const SecurityAlertModal: React.FC<ISecurityAlertModalProps> = ({ badgeColor, notification, onClosed }) => {
    const { issue } = notification.getData();

    return (
        <Modal isOpen onClosed={onClosed} size='lg'>
            <ModalHeader>
                Security Alert
            </ModalHeader>
            <ModalBody>
                <Form>
                    <Row className='mb-3'>
                        <Col sm={2} className='text-end'>
                            Severity
                        </Col>
                        <Col sm={10}>
                            <Badge color={badgeColor}>
                                {S(issue.severity).humanize().s}
                            </Badge>
                        </Col>
                    </Row>
                    <FormGroup row>
                        <Label sm={2} className='text-end'>
                            Filed
                        </Label>
                        <Col sm={10}>
                            <Input type='text' readOnly value={DateTime.fromISO(issue.datetime).toLocaleString()} />
                        </Col>
                    </FormGroup>
                    <FormGroup row>
                        <Label sm={2} className='text-end'>
                            Message
                        </Label>
                        <Col sm={10}>
                            <Input type='text' readOnly value={issue.message} />
                        </Col>
                    </FormGroup>

                    <Row>
                        <Col sm={2} className='text-end'>
                            Context
                        </Col>
                        <Col sm={10}>
                            <Table>
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {Object.entries(issue.context).map(([key, value], index) => (
                                        <tr key={index}>
                                            <td>{key}</td>
                                            <td title={S(value).s}>
                                                {value ? S(value).truncate(75).s : '(empty)'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </Table>
                        </Col>
                    </Row>
                </Form>
            </ModalBody>
            <ModalFooter>
                <Button color="primary" onClick={() => onClosed()}>
                    Close
                </Button>
            </ModalFooter>
        </Modal>
    );
}

export default SecurityAlertModal;
