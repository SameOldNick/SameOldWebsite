import React from 'react';
import { Table } from 'reactstrap';

import Loader from '@admin/components/Loader';
import WaitToLoad from '@admin/components/WaitToLoad';

import { createAuthRequest } from '@admin/utils/api/factories';
import { isNotificationType } from '@admin/utils/api/endpoints/notifications';
import SecurityAlertModal from '@admin/components/dashboard/security-alerts/SecurityAlertModal';
import SecurityAlertRow from '@admin/components/dashboard/security-alerts/SecurityAlertRow';

export const SecurityAlertNotificationType = '513a8515-ae2a-47d9-9052-212b61f166b0';

export interface ISecurityAlertNotificationData {
    id: string;
    issue: {
        id: string;
        datetime: string;
        severity: string;
        message: string;
        context: object;
    }
}

export type TSecurityAlertNotification = INotification<typeof SecurityAlertNotificationType, ISecurityAlertNotificationData>;

interface ISecurityAlertsProps {
}



const SecurityAlerts: React.FC<ISecurityAlertsProps> = ({ }) => {
    const [selectedAlert, setSelectedAlert] = React.useState<TSecurityAlertNotification>();

    const determineBadgeColorForSeverity = React.useCallback((severity: string): string => {
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

            default: {
                return '';
            }
        }
    }, []);

    const fetchSecurityAlerts = React.useCallback(async () => {
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
    }, []);

    const handleModalClosed = React.useCallback(() => setSelectedAlert(undefined), []);

    const getBadgeColorForAlert = React.useCallback((alert: TSecurityAlertNotification) => determineBadgeColorForSeverity(alert.data.issue.severity), [determineBadgeColorForSeverity]);

    return (
        <>
            {selectedAlert && <SecurityAlertModal badgeColor={getBadgeColorForAlert(selectedAlert)} alert={selectedAlert} onClosed={handleModalClosed} />}
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
                                        badgeColor={getBadgeColorForAlert(notification)}
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
