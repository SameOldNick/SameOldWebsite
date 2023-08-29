import { createAsyncThunk, createSlice, PayloadAction } from "@reduxjs/toolkit";
import axios, { AxiosError } from "axios";

import { createAuthRequest } from "@admin/utils/api/factories";

export interface INotificationsState {
    messages: TMessageNotification[];
    error?: unknown;
}

const initialState: INotificationsState = {
    messages: [],
};

export const fetchMessages = createAsyncThunk<TMessageNotification[], void>(
    'notifications/fetch-messages',
    async (_, { rejectWithValue }) => {
        try {
            const response = await createAuthRequest().get<TMessageNotification[]>('/user/notifications');

            return response.data.filter((message) => message.type === 'App\\Notifications\\MessageNotification');
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
