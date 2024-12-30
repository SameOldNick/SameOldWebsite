import { DateTime } from "luxon";
import S from "string";

export type TContactMessageStatuses = 'accepted' | 'unconfirmed' | 'confirmed' | 'expired';

/**
 * Represents a contact message.
 *
 * @export
 * @class ContactMessage
 */
export default class ContactMessage {
    public static readonly STATUS_ACCEPTED: TContactMessageStatuses = 'accepted';
    public static readonly STATUS_UNCONFIRMED: TContactMessageStatuses = 'unconfirmed';
    public static readonly STATUS_CONFIRMED: TContactMessageStatuses = 'confirmed';
    public static readonly STATUS_EXPIRED: TContactMessageStatuses = 'expired';

    /**
     * Creates an instance of ContactMessage.
     * @param {IContactMessage} message
     * @memberof ContactMessage
     */
    constructor(
        public readonly message: IContactMessage
    ) {
    }

    /**
     * Gets display name
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get displayName() {
        return `${S(this.message.name).truncate(30).s} (${this.message.email})`;
    }

    /**
     * Gets when message was created.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get createdAt() {
        return DateTime.fromISO(this.message.created_at);
    }

    /**
     * Gets when message was updated.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get updatedAt() {
        return this.message.updated_at ? DateTime.fromISO(this.message.updated_at) : null;
    }

    /**
     * Gets when message was approved.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get confirmedAt(): DateTime<true> | null {
        return this.message.confirmed_at ? DateTime.fromISO(this.message.confirmed_at) : null;
    }

    /**
     * Gets when message expires.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get expiresAt() {
        return this.message.expires_at ? DateTime.fromISO(this.message.expires_at) : null;
    }

    /**
     * Gets the message status.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get status() {
        return this.message.status;
    }
}
