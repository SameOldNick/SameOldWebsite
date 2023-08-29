import store from "store2";

import BaseStorage from './BaseStorage';

export default class LocalStorage extends BaseStorage {
    constructor() {
        super(store.local);
    }
}
