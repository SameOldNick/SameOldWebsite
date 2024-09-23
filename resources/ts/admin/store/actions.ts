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

export default actions;
