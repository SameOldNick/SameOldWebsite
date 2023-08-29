import { AxiosResponse } from "axios";

export const AUTH_STATE_STORAGE_KEY = 'auth_state';

export interface IAuthStageNone {
    stage: 'none';
}

export interface IAuthStageAuthenticated {
    stage: 'authenticated';
    accessToken: IJsonWebToken;
    refreshToken: IJsonWebToken;
}

export type TAuthStages = IAuthStageNone | IAuthStageAuthenticated;

export type TUpdateUserRequest = Record<string, any>;
export type TUpdateUserResponse = AxiosResponse<IMessageResponse>;
