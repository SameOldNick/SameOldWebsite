import { createAsyncThunk, createSlice, PayloadAction } from "@reduxjs/toolkit";
import axios, { AxiosError } from "axios";

import { createAuthRequest } from "@admin/utils/api/factories";
import storage from "@admin/utils/storage";

import * as types from "./types/account";
import User from "@admin/utils/api/models/User";

export interface IAccountState {
    loading: boolean;
    user?: User;
    fetchUser: TApiState<User, AxiosError>;
    stage: types.TAuthStages;
    signout: boolean;
}

const initialState: IAccountState = {
    loading: false,
    fetchUser: { status: 'none' },
    stage: storage.get(types.AUTH_STATE_STORAGE_KEY) ?? { stage: 'none' },
    signout: false
};

export const fetchUser = createAsyncThunk<User, void, { rejectValue: AxiosError }>(
    'account/fetch-user',
    async (_, { rejectWithValue }) => {
        try {
            const response = await createAuthRequest().get<IUser>('user');
            return new User(response.data);
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
    name: "account",
    initialState,
    reducers: {
        setUser: (state, { payload }: PayloadAction<User | undefined>) => ({
            ...state, user: payload
        }),
        authStage: (state, { payload }: PayloadAction<types.TAuthStages>) => {
            if (payload.stage !== 'none')
                storage.set(types.AUTH_STATE_STORAGE_KEY, payload)
            else
                storage.remove(types.AUTH_STATE_STORAGE_KEY);

            return { ...state, stage: payload };
        },
        displaySignout: (state, { payload }: PayloadAction<boolean>) => ({ ...state, signout: payload })
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchUser.pending, (state, action) => ({ ...state, fetchUser: { status: 'pending' } }))
            .addCase(fetchUser.fulfilled, (state, action) => ({ ...state, fetchUser: { status: 'fulfilled', response: action.payload } }))
            .addCase(fetchUser.rejected, (state, action) => ({ ...state, fetchUser: { status: 'rejected', error: action.payload as AxiosError } }));
    }
});
