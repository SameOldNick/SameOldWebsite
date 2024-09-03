import { DateTime } from "luxon";
import User from "./User";

/**
 * Represents a person (guest or registered user).
 *
 * @export
 * @class Person
 */
export default class Person {

    /**
     * Creates an instance of Person.
     * @param {IPerson} person
     * @memberof Person
     */
    constructor(
        public readonly person: IPerson
    ) {
    }

    /**
     * Gets the User model
     *
     * @readonly
     * @memberof Person
     */
    public get user() {
        return this.person.user ? new User(this.person.user) : null;
    }

    /**
     * Checks if person is guest.
     *
     * @readonly
     * @memberof Person
     */
    public get isGuest() {
        return this.person.user === null;
    }

    /**
     * Checks if person is registered
     *
     * @readonly
     * @memberof Person
     */
    public get isRegistered() {
        return this.person.user !== null;
    }

    /**
     * Gets when person was created.
     *
     * @readonly
     * @memberof Person
     */
    public get createdAt() {
        return DateTime.fromISO(this.person.created_at);
    }

    /**
     * Gets when person was updated.
     *
     * @readonly
     * @memberof Person
     */
    public get updatedAt() {
        return this.person.updated_at ? DateTime.fromISO(this.person.updated_at) : null;
    }

    /**
     * Gets when person was deleted (or null if not).
     *
     * @readonly
     * @memberof Person
     */
    public get deletedAt() {
        return this.person.deleted_at ? DateTime.fromISO(this.person.deleted_at) : null;
    }


}
