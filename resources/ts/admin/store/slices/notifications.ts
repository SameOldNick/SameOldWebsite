import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import axios, { AxiosError } from "axios";

import { all } from "@admin/utils/api/endpoints/notifications";
import MessageNotification from "@admin/utils/api/models/notifications/MessageNotification";
import Notification from "@admin/utils/api/models/notifications/Notification";

export interface INotificationsState {
    messages: MessageNotification[];
    error?: unknown;
}

const initialState: INotificationsState = {
    messages: [],
};

export const fetchMessages = createAsyncThunk<MessageNotification[], void>(
    'notifications/fetch-messages',
    async (_, { rejectWithValue }) => {
        try {
            
            const data = await all({ type: Notification.NOTIFICATION_TYPE_MESSAGE });

            return data.map((record) => new MessageNotification(record as any));
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
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(fetchMessages.fulfilled, (state, action) => ({ ...state, messages: action.payload }))
            .addCase(fetchMessages.rejected, (state, action) => ({ ...state, error: action.payload }));
    }
});