import { createAsyncThunk, createSlice, PayloadAction } from "@reduxjs/toolkit";
import axios, { AxiosError } from "axios";

import { createAuthRequest } from "@admin/utils/api/factories";
import { all, isNotificationType } from "@admin/utils/api/endpoints/notifications";

export interface INotificationsState {
    messages: TMessageNotification[];
    error?: unknown;
}

const initialState: INotificationsState = {
    messages: [],
};

const messageNotificationType = '6414fd8c-847a-492b-a919-a5fc539456e8';

export const isMessageNotification = (notification: INotification) => isNotificationType(notification, messageNotificationType);

export const fetchMessages = createAsyncThunk<TMessageNotification[], void>(
    'notifications/fetch-messages',
    async (_, { rejectWithValue }) => {
        try {
            const data = await all();

            return data.filter((notification) => isMessageNotification(notification as INotification)) as TMessageNotification[];
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
    reducers: { },
    extraReducers: (builder) => {
        builder
            .addCase(fetchMessages.fulfilled, (state, action) => ({ ...state, messages: action.payload }))
            .addCase(fetchMessages.rejected, (state, action) => ({ ...state, error: action.payload }));
    }
});
