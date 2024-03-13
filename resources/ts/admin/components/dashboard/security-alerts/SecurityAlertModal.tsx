import React from 'react';
import { Badge, Button, Col, Form, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row, Table } from 'reactstrap';

import moment from 'moment';
import S from 'string';
import { TSecurityAlertNotification } from '@admin/pages/protected/dashboard/SecurityAlerts';

interface ISecurityAlertModalProps {
    alert: TSecurityAlertNotification;
    badgeColor: string;
    onClosed: () => void;
}

const SecurityAlertModal: React.FC<ISecurityAlertModalProps> = ({ badgeColor, alert, onClosed }) => {
    const { data: { issue } } = alert;

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
                            <Input type='text' readOnly value={moment(issue.datetime).toLocaleString()} />
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
