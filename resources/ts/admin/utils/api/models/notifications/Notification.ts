import { DateTime } from "luxon";

export default class Notification<TType extends string, TData extends object> {
    public static readonly NOTIFICATION_TYPE_ACTIVITY = 'ce659a33-08dd-4c9c-a421-7bb54393b76d';
    public static readonly NOTIFICATION_TYPE_MESSAGE = '6414fd8c-847a-492b-a919-a5fc539456e8';
    public static readonly NOTIFICATION_TYPE_SECURITY_ALERT = '513a8515-ae2a-47d9-9052-212b61f166b0';

    /**
     * Creates an instance of Notification.
     * @param {INotification<TType>} notification
     * @memberof Notification
     */
    constructor(
        public readonly notification: INotification<TType>
    ) {
    }

    /**
     * Checks if notification has type
     *
     * @param {string} type
     * @return {boolean}
     * @memberof Notification
     */
    public isType(type: string): boolean {
        return Notification.isType(this.notification, type);
    }

    /**
     * Checks if notification is type
     *
     * @static
     * @template TNotification
     * @param {INotification} notification
     * @param {string} type
     * @return {*}  {notification is TNotification}
     * @memberof Notification
     */
    public static isType<TNotification extends INotification>(notification: INotification, type: string): notification is TNotification {
        return notification.type === type;
    }

    /**
     * Gets the ID of the notification
     *
     * @readonly
     * @memberof Notification
     */
    public get id() {
        return this.notification.id;
    }

    /**
     * Gets when the notification was created
     *
     * @readonly
     * @memberof Notification
     */
    public get createdAt() {
        return DateTime.fromISO(this.notification.created_at);
    }

    /**
     * Gets when the notification was updated
     *
     * @readonly
     * @memberof Notification
     */
    public get updatedAt() {
        return DateTime.fromISO(this.notification.updated_at);
    }

    /**
     * Gets if/when notification was read.
     *
     * @readonly
     * @memberof Notification
     */
    public get readAt() {
        return this.notification.read_at ? DateTime.fromISO(this.notification.read_at) : null;
    }

    /**
     * Gets notification data
     * @returns Data
     */
    public getData(): TData {
        return typeof this.notification.data === 'string' ? JSON.parse(this.notification.data) : this.notification.data;
    }


}
