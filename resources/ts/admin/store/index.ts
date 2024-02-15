
import { combineReducers, Middleware } from 'redux';
import { configureStore, Tuple } from '@reduxjs/toolkit'
import { thunk } from 'redux-thunk';
import logger from 'redux-logger';

import accountSlice from './slices/account';
import alertsSlice from './slices/alerts';
import mainSlice from './slices/main';
import notificationsSlice from './slices/notifications';

const actions = {
    main: mainSlice.actions,
    account: accountSlice.actions,
    alerts: alertsSlice.actions,
    notifications: notificationsSlice.actions,
};

const createRootReducer = () => combineReducers({
    main: mainSlice.reducer,
    account: accountSlice.reducer,
    alerts: alertsSlice.reducer,
    notifications: notificationsSlice.reducer,
});

const createRootMiddlewares = () => {
    const middleware: Middleware[] = [thunk];

    if (import.meta.env.VITE_APP_DEBUG)
        middleware.push(logger);

    return new Tuple(...middleware);
}

const createStore = () => configureStore({
    reducer: createRootReducer(),
    middleware: createRootMiddlewares
});

const store = createStore();

export { actions };
export default store;
