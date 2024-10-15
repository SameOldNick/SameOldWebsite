import { createAsyncThunk, createSlice, PayloadAction } from "@reduxjs/toolkit";
import axios, { AxiosError } from "axios";

import { all } from "@admin/utils/api/endpoints/notifications";

import Notification from "@admin/utils/api/models/notifications/Notification";
import AlertNotification, { IAlertNotificationData } from "@admin/utils/api/models/notifications/AlertNotification";

export interface INotificationsState {
    apiNotifications: AlertNotification[];
    echoNotifications: IAlertNotificationData[];
}

const initialState: INotificationsState = {
    apiNotifications: [],
    echoNotifications: [],
};

export const fetchFromApi = createAsyncThunk<AlertNotification[], void, { rejectValue: AxiosError }>(
    'notifications/fetch-api',
    async (_, { rejectWithValue }) => {
        try {
            const response = await all({ type: Notification.NOTIFICATION_TYPE_ALERT });

            return response.map((record) => new AlertNotification(record as any));
        } catch (e) {
            if (!axios.isAxiosError(e) && import.meta.env.VITE_APP_DEBUG) {
                logger.error(e);
                throw e;
            }

            return rejectWithValue(e as AxiosError);
        }
    }
);

export default createSlice({
    name: "notifications",
    initialState,
    reducers: {
        setApiNotifications: (state, { payload }: PayloadAction<AlertNotification[]>) => ({
            ...state, apiNotifications: payload
        }),
        setEchoNotifications: (state, { payload }: PayloadAction<IAlertNotificationData[]>) => ({
            ...state, echoNotifications: payload
        }),
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchFromApi.fulfilled, (state, action) => ({ ...state, apiNotifications: state.apiNotifications.concat(action.payload) }));
    }
});
