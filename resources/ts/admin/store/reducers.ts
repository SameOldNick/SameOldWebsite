
import { combineReducers } from 'redux';

import accountSlice from './slices/account';
import alertsSlice from './slices/alerts';
import mainSlice from './slices/main';
import notificationsSlice from './slices/notifications';
import messagesSlice from './slices/messages';

const reducers = combineReducers({
    main: mainSlice.reducer,
    account: accountSlice.reducer,
    alerts: alertsSlice.reducer,
    notifications: notificationsSlice.reducer,
    messages: messagesSlice.reducer,
});

export default reducers;
