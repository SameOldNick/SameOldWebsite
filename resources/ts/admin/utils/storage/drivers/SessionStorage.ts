import store from "store2";

import BaseStorage from './BaseStorage';

export default class SessionStorage extends BaseStorage {
    constructor() {
        super(store.session);
    }
}
