import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export interface IMainState {
    loading: boolean;
}

const initialState: IMainState = {
    loading: false
};

export default createSlice({
    name: "main",
    initialState,
    reducers: {
        showLoader: (state, { payload }: PayloadAction<boolean>) => ({ ...state, loading: payload })
    }
});
