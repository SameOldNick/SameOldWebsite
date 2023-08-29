import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import merge from "ts-deepmerge";
import { v4 as uuidv4 } from 'uuid';
import { SweetAlertOptions } from "sweetalert2";

import { buildInitialState } from "@admin/store/helpers/api";
import { IAlert } from "@admin/components/Alerts";

export const components = [
    'auth',
] as const;

export type TComponent = typeof components[number];

export interface IStoredAlert extends IAlert {
    id: string;
}

export interface IAlertParams {
    component: TComponent;
    alert: IAlert;
}

export interface ISweetAlertParams extends SweetAlertOptions {
    content?: React.ReactNode;
    beforeConfirmed?: (response?: any) => boolean | void;
    afterConfirmed?: (response?: any) => void;
    beforeCancelled?: () => boolean | void;
    afterCancelled?: () => void;
}

export type TAlertsState = {
    alerts: Record<TComponent, IStoredAlert[]>
    sweetalert?: ISweetAlertParams;
};

const initialState: TAlertsState = {
    alerts: buildInitialState(components, [])
};

export default createSlice({
    name: "alerts",
    initialState,
    reducers: {
        addAlert: (state, { payload: { component, alert } }: PayloadAction<IAlertParams>) => merge(state, { alerts: { [component]: [{ id: uuidv4(), ...alert }] } }),
        clearAlerts: (state, { payload }: PayloadAction<TComponent>) => merge.withOptions({ mergeArrays: false }, state, { alerts: { [payload]: [] } }),
        displaySweetAlert: (state, { payload }: PayloadAction<ISweetAlertParams>) => merge(state, { sweetalert: payload }),
        clearSweetAlert: (state) => merge(state, { sweetalert: undefined })
    }
});
