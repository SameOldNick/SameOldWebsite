import React from 'react';
import { Table } from 'reactstrap';

import Loader from '@admin/components/Loader';
import WaitToLoad from '@admin/components/WaitToLoad';
import SecurityAlertModal from '@admin/components/dashboard/security-alerts/SecurityAlertModal';
import SecurityAlertRow from '@admin/components/dashboard/security-alerts/SecurityAlertRow';

import Notification from '@admin/utils/api/models/notifications/Notification';
import SecurityNotification from '@admin/utils/api/models/notifications/SecurityNotification';

import { all } from '@admin/utils/api/endpoints/notifications';

const SecurityAlerts: React.FC = () => {
    const [selectedAlert, setSelectedAlert] = React.useState<SecurityNotification>();

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
        const notifications = await all({ type: Notification.NOTIFICATION_TYPE_SECURITY_ALERT });

        return notifications.map((record) => new SecurityNotification(record as any));
    }, []);

    const handleModalClosed = React.useCallback(() => setSelectedAlert(undefined), []);

    const getBadgeColorForAlert = React.useCallback((notification: SecurityNotification) => determineBadgeColorForSeverity(notification.getData().issue.severity), [determineBadgeColorForSeverity]);

    return (
        <>
            {selectedAlert && <SecurityAlertModal badgeColor={getBadgeColorForAlert(selectedAlert)} notification={selectedAlert} onClosed={handleModalClosed} />}
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
                                        notification={notification}
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
