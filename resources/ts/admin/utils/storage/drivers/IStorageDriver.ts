import { DateTime } from 'luxon';

export interface IStorageDriver {
    /**
     * Sets a value to a key with an (optional) expiration.
     *
     * @memberof IStorageDriver
     */
    set: <T>(key: string, value: T, expires?: DateTime) => void;
    /**
     * Flashes an item to the store. The item can only be retrieved once.
     *
     * @memberof IStorageDriver
     */
    flash: <T>(key: string, value: T) => void;
    /**
     * Checks if key exists in store.
     *
     * @memberof IStorageDriver
     */
    has: (key: string) => boolean;
    /**
     * Gets the value of a key in the store.
     *
     * @memberof IStorageDriver
     */
    get: <T>(key: string) => T | undefined;
    /**
     * Gets the expiration of an item.
     *
     * @memberof IStorageDriver
     */
    getExpiration: (key: string) => DateTime | undefined;
    /**
     * Removes an item from the store.
     *
     * @memberof IStorageDriver
     */
    remove: <T>(key: string) => T | undefined;
    /**
     * Clears all of the items that are stored.
     *
     * @memberof IStorageDriver
     */
    clearAll: () => void;
    /**
     * Gets all of the keys in the store.
     *
     * @memberof IStorageDriver
     */
    keys: () => string[];
}
