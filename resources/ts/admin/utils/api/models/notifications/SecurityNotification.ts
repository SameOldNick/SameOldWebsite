import Notification from "./Notification";

export type TSecurityNotificationType = '513a8515-ae2a-47d9-9052-212b61f166b0';

export enum Severity {
    SeverityLow = 'low',
    SeverityMedium = 'medium',
    SeverityHigh = 'high',
    SeverityCritical = 'critical',
}

export interface ISecurityAlertNotificationData {
    id: string;
    issue: {
        id: string;
        datetime: string;
        severity: Severity;
        message: string;
        context: object;
    }
}

export default class SecurityNotification extends Notification<TSecurityNotificationType, ISecurityAlertNotificationData> {
    
}
