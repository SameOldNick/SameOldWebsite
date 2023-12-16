import React from 'react';
import { FaExternalLinkAlt } from 'react-icons/fa';
import { Badge, Button, Col, Form, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row, Table } from 'reactstrap';

import moment from 'moment';
import S from 'string';

import Loader from '@admin/components/Loader';
import WaitToLoad from '@admin/components/WaitToLoad';

import { createAuthRequest } from '@admin/utils/api/factories';
import { isNotificationType } from '@admin/utils/api/endpoints/notifications';

const SecurityAlertNotificationType = '513a8515-ae2a-47d9-9052-212b61f166b0';

interface ISecurityAlertNotificationData {
    id: string;
    issue: {
        id: string;
        datetime: string;
        severity: string;
        message: string;
        context: object;
    }
}

type TSecurityAlertNotification = INotification<typeof SecurityAlertNotificationType, ISecurityAlertNotificationData>;

interface ISecurityAlertsProps {
}

interface ISecurityAlertModalProps {
    alert: TSecurityAlertNotification;
    onClosed: () => void;
}

interface ISecurityAlertRowProps {
    alert: TSecurityAlertNotification;
    onViewClicked: () => void;
}

const determineBadgeColorForSeverity = (severity: string) => {
    switch (severity) {
        case 'low': {
            return 'success';
        }

        case 'medium': {
            return 'info';
        }

        case 'high': {
            return 'warning';
        }

        case 'critical': {
            return 'danger';
        }
    }
}

const SecurityAlertModal: React.FC<ISecurityAlertModalProps> = ({ alert, onClosed }) => {
    const { data: { issue } } = alert;

    const badgeColor = React.useMemo(() => determineBadgeColorForSeverity(issue.severity), [alert]);

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

const SecurityAlertRow: React.FC<ISecurityAlertRowProps> = ({ alert, onViewClicked }) => {
    const { data: { issue } } = alert;

    const badgeColor = React.useMemo(() => determineBadgeColorForSeverity(issue.severity), [alert]);

    const handleViewClicked = (e: React.MouseEvent) => {
        e.preventDefault();

        onViewClicked();
    }

    return (
        <>
            <tr>
                <td>
                    <Badge color={badgeColor}>
                        {S(issue.severity).humanize().s}
                    </Badge>
                </td>
                <td>{moment(issue.datetime).fromNow()}</td>
                <td>{S(issue.message).truncate(75).s}</td>
                <td>
                    <Button color='link' title='View more information' onClick={handleViewClicked}>
                        <FaExternalLinkAlt />
                    </Button>
                </td>
            </tr>
        </>
    );
}

const SecurityAlerts: React.FC<ISecurityAlertsProps> = ({ }) => {
    const [selectedAlert, setSelectedAlert] = React.useState<TSecurityAlertNotification>();

    const fetchSecurityAlerts = async () => {
        const alerts: TSecurityAlertNotification[] = [];
        const response = await createAuthRequest().get<INotification[]>('/user/notifications');

        const isSecurityNotification = (notification: INotification): notification is TSecurityAlertNotification =>
            isNotificationType(notification, SecurityAlertNotificationType);

        response.data.forEach((notification) => {
            if (isSecurityNotification(notification)) {
                alerts.push(notification as TSecurityAlertNotification);
            }
        });

        return alerts;
    }

    const handleModalClosed = () => setSelectedAlert(undefined);

    return (
        <>
            {selectedAlert && <SecurityAlertModal alert={selectedAlert} onClosed={handleModalClosed} />}
            <WaitToLoad loading={<Loader display={{ type: 'over-element' }} />} callback={fetchSecurityAlerts}>
                {(alerts, err) => (
                    <>
                        <Table>
                            <thead>
                                <tr>
                                    <th>Severity</th>
                                    <th>Filed</th>
                                    <th>Message</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {alerts && alerts.length === 0 && (
                                    <tr>
                                        <td colSpan={4}>(No alerts found)</td>
                                    </tr>
                                )}
                                {alerts && alerts.map((notification, index) => (
                                    <SecurityAlertRow
                                        key={index}
                                        alert={notification}
                                        onViewClicked={() => setSelectedAlert(notification)}
                                    />
                                ))}

                            </tbody>
                        </Table>

                        {err && <p className="text-muted">(An error occurred)</p>}
                    </>
                )}
            </WaitToLoad>


        </>
    );
}

export default SecurityAlerts;
