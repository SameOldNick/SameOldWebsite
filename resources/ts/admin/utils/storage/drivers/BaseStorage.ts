import store, { StoreAPI } from 'store2';
import { DateTime } from 'luxon';

import { IStorageDriver } from './IStorageDriver';

export default abstract class BaseStorage implements IStorageDriver {
    private readonly flashNamespace: string = 'flash_mixin';
    private readonly expiresNamespace: string = 'expires_mixin';

    public readonly store: StoreAPI;

    /**
     * Creates an instance of BaseStorage.
     * @param {store.StoreAPI} storeApi Underlying storage to use (local or session)
     * @memberof BaseStorage
     */
    constructor(storeApi: StoreAPI) {
        this.store = storeApi;
    }

    /**
     * Sets a value to a key with an (optional) expiration.
     *
     * @template T
     * @param {string} key Key of item.
     * @param {T} value Value.
     * @param {moment.Moment} [expires] When item expires (as Moment object).
     * @returns
     * @memberof Store
     */
    public set<T>(key: string, value: T, expires?: DateTime) {
        if (expires) {
            this.expiresStore.set(key, expires.toObject(), true);
        }

        return store.set(key, value, true);
    }

    /**
     * Checks if key exists in store.
     *
     * @param {string} key
     * @returns True if key exists.
     * @memberof Store
     */
    public has(key: string) {
        return store.has(key) || this.flashStore.has(key);
    }

    /**
     * Gets all the keys
     *
     * @returns Array of keys
     * @memberof BaseStorage
     */
    public keys() {
        return store.keys();
    }

    /**
     * Gets the value of a key in the store.
     *
     * @template T
     * @param {string} key Key to get value of
     * @returns {(T | undefined)} Value.
     * @memberof Store
     */
    public get<T>(key: string): T | undefined {
        if (this.flashStore.get(key) === true) {
            return this.remove(key);
        } else if (this.expiresStore.has(key)) {
            const expiry = this.getExpiration(key);

            // Check if expired
            if (expiry !== undefined && expiry < DateTime.now()) {
                this.remove(key);

                return undefined;
            }
        }

        return store.get(key);
    }

    /**
     * Removes an item from the store.
     *
     * @template T
     * @param {string} key Key of item to remove from store.
     * @returns {T} Value (before removed).
     * @memberof Store
     */
    public remove<T>(key: string): T {
        if (this.expiresStore.has(key)) {
            this.expiresStore.remove(key);
        } else if (this.flashStore.get(key) === true) {
            this.flashStore.remove(key);
        }

        return store.remove(key);
    }

    /**
     * Clears all of the items that are stored.
     *
     * @returns
     * @memberof Store
     */
    public clearAll() {
        store.clearAll();
    }

    /**
     * Gets the expiration of an item.
     *
     * @param {string} key Key of item to get expiration for.
     * @returns {(moment.Moment | undefined)} The expiration of the key (as Moment object) or undefined if key doesn't exist or has no expiration.
     * @memberof Store
     */
    public getExpiration(key: string): DateTime | undefined {
        const expiry = this.expiresStore.get(key);

        return expiry ? DateTime.fromObject(expiry) : undefined;
    }

    /**
     * Sends each item in store to callback function.
     *
     * @param {(key: any, data: any) => void} cb Callback that receives each value and it's key in store.
     * @returns
     * @memberof Store
     */
    public each(cb: (key: any, data: any) => void) {
        store.getAll()
        store.each((key, data) => {
            const expiry = this.getExpiration(key);

            // Send to callback if not expired
            if (expiry === undefined || expiry > DateTime.now())
                cb(key, data);
        });
    }

    /**
     * Flashes an item to the store. The item can only be retrieved once.
     *
     * @template T
     * @param {string} key Key of item.
     * @param {T} value Value of item.
     * @memberof Store
     */
    public flash<T>(key: string, value: T) {
        this.set(key, value);
        this.flashStore.set(key, true);
    }

    /**
     * Gets the expires store.
     *
     * @readonly
     * @private
     * @memberof Store
     */
    private get expiresStore() {
        return store.namespace(this.expiresNamespace);
    }

    /**
     * Gets the flash store.
     *
     * @readonly
     * @private
     * @memberof Store
     */
    private get flashStore() {
        return store.namespace(this.flashNamespace);
    }
}
