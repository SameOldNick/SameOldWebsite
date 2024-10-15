import Notification from "./Notification";

export type TAlertNotificationType = 'cffa9651-88f5-4247-abae-63df928e34b7';

export interface IAlertNotificationData {
    dateTime: string;
    id: string;
    color: string;
    message: string;
    link: string | null;
}

export default class AlertNotification extends Notification<TAlertNotificationType, IAlertNotificationData> {

}
