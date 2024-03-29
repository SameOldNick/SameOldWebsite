import moment from "moment";
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
        return moment(this.message.created_at);
    }

    /**
     * Gets when message was updated.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get updatedAt() {
        return this.message.updated_at ? moment(this.message.updated_at) : null;
    }

    /**
     * Gets when message was approved.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get confirmedAt() {
        return this.message.confirmed_at ? moment(this.message.confirmed_at) : null;
    }

    /**
     * Gets when message expires.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get expiresAt() {
        return this.message.expires_at ? moment(this.message.expires_at) : null;
    }

    /**
     * Gets the message status.
     *
     * @readonly
     * @memberof ContactMessage
     */
    public get status() {
        if (this.message.confirmed_at !== null)
            return ContactMessage.STATUS_CONFIRMED;
        else if (this.expiresAt && moment().isBefore(this.expiresAt))
            return ContactMessage.STATUS_UNCONFIRMED;
        else if (this.expiresAt === null)
            return ContactMessage.STATUS_ACCEPTED;
        else
            return ContactMessage.STATUS_EXPIRED;
    }
}
