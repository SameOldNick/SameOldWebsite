import { DateTime } from "luxon";

import LocalStorage from './drivers/LocalStorage';
import SessionStorage from './drivers/SessionStorage';
import { IStorageDriver } from "./drivers/IStorageDriver";

class Storage implements IStorageDriver {
    private drivers: Record<string, IStorageDriver>;

    constructor() {
        this.drivers = {
            local: new LocalStorage(),
            session: new SessionStorage()
        };
    }

    public getDefaultDriver(): string {
        return 'local';
    }

    /**
     * Provides access to the underlying storage driver.
     *
     * @returns {(IStorageDriver | undefined)}
     * @memberof Store
     */
    public getDriver(driver?: string): (IStorageDriver | undefined) {
        return this.drivers[driver ?? this.getDefaultDriver()];
    }

    /**
     * Sets a value to a key with an (optional) expiration.
     *
     * @template T
     * @param {string} key Key of item.
     * @param {T} value Value.
     * @param {DateTime} [expires] When item expires (as Moment object).
     * @returns
     * @memberof Store
     */
    public set<T = any>(key: string, value: T, expires?: DateTime) {
        this.getDriver()?.set(key, value, expires);
    }

    /**
     * Checks if key exists in store.
     *
     * @param {string} key
     * @returns True if key exists.
     * @memberof Store
     */
    public has(key: string) {
        return this.getDriver()?.has(key) ?? false;
    }

    /**
     * Gets all of the keys.
     *
     * @returns Array of keys
     * @memberof Store
     */
    public keys() {
        return this.getDriver()?.keys() ?? [];
    }

    /**
     * Gets the value of a key in the store.
     *
     * @template T
     * @param {string} key Key to get value of
     * @returns {(T | undefined)} Value.
     * @memberof Store
     */
    public get<T = any>(key: string): T | undefined {
        return this.getDriver()?.get(key);
    }

    /**
     * Removes an item from the store.
     *
     * @template T
     * @param {string} key Key of item to remove from store.
     * @returns {T} Value (before removed).
     * @memberof Store
     */
    public remove<T = any>(key: string): T | undefined {
        return this.getDriver()?.remove(key);
    }

    /**
     * Clears all of the items that are stored.
     *
     * @returns
     * @memberof Store
     */
    public clearAll() {
        return this.getDriver()?.clearAll();
    }

    /**
     * Gets the expiration of an item.
     *
     * @param {string} key Key of item to get expiration for.
     * @returns {(moment.Moment | undefined)} The expiration of the key (as Moment object) or undefined if key doesn't exist or has no expiration.
     * @memberof Store
     */
    public getExpiration(key: string): DateTime | undefined {
        return this.getDriver()?.getExpiration(key);
    }

    /**
     * Sends each item in store to callback function.
     *
     * @template T
     * @param {(key: any, data: any) => void} cb Callback that receives each value and it's key in store.
     * @memberof Store
     */
    public each(cb: (key: any, data: any) => void) {
        for (const key of this.keys()) {
            const data = this.get(key);

            cb(key, data);
        }
    }

    /**
     * Flashes an item to the store. The item can only be retrieved once.
     *
     * @template T
     * @param {string} key Key of item.
     * @param {T} value Value of item.
     * @memberof Store
     */
    public flash<T = any>(key: string, value: T) {
        this.getDriver()?.flash(key, value);
    }
}

export default new Storage();
