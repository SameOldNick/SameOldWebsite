import Notification from "./Notification";

export type TMessageNotificationType = '6414fd8c-847a-492b-a919-a5fc539456e8';

export interface IAddress {
    name: string | null;
    address: string;
}

export interface IMessageData {
    addresses: Record<'to' | 'cc' | 'bcc' | 'replyTo', IAddress[]>;
    subject: string;
    view: Record<'html' | 'text', string>;
    type: string;
}

export default class MessageNotification extends Notification<TMessageNotificationType, IMessageData> {
    
}
