import { DateTime } from "luxon";
import { generatePath } from "react-router-dom";

export type TUserStatuses = 'active' | 'inactive';

/**
 * Represents a user.
 *
 * @export
 * @class User
 */
export default class User {
    public static readonly STATUS_ACTIVE: TUserStatuses = 'active';
    public static readonly STATUS_INACTIVE: TUserStatuses = 'inactive';

    /**
     * Creates an instance of User.
     * @param {IUser} user
     * @memberof User
     */
    constructor(
        public readonly user: IUser
    ) {
    }

    /**
     * Gets display name
     *
     * @readonly
     * @memberof User
     */
    public get displayName() {
        return this.user.name || this.user.email;
    }

    /**
     * Gets when user was created.
     *
     * @readonly
     * @memberof User
     */
    public get createdAt() {
        return DateTime.fromISO(this.user.created_at);
    }

    /**
     * Gets when user was updated.
     *
     * @readonly
     * @memberof User
     */
    public get updatedAt() {
        return this.user.updated_at ? DateTime.fromISO(this.user.updated_at) : null;
    }

    /**
     * Gets when user was deleted (or null if not).
     *
     * @readonly
     * @memberof User
     */
    public get deletedAt() {
        return this.user.deleted_at ? DateTime.fromISO(this.user.deleted_at) : null;
    }

    /**
     * Gets the roles user belongs to.
     *
     * @readonly
     * @memberof User
     */
    public get roles() {
        return this.user.roles.map(({ role }) => role);
    }

    /**
     * Gets the users status.
     *
     * @readonly
     * @memberof User
     */
    public get status() {
        return !this.user.deleted_at ? User.STATUS_ACTIVE : User.STATUS_INACTIVE;
    }

    /**
     * Generates path to edit user page.
     *
     * @returns
     * @memberof User
     */
    public generatePath() {
        if (!this.user.id)
            throw new Error('User is missing ID.');

        return generatePath(`/admin/users/edit/:user`, {
            user: this.user.id.toString()
        });
    }
}
