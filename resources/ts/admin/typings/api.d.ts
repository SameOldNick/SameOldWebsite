import { AxiosResponse, AxiosError } from 'axios';

declare global {
    export interface ISliceApiStateNone {
        status: 'none';
    }

    export interface ISliceApiStatePending {
        status: 'pending';
    }

    export interface ISliceApiStateFulfilled<TResponse> {
        status: 'fulfilled';
        response: TResponse;
    }

    export interface ISliceApiStateRejected<TError> {
        status: 'rejected';
        error: TError;
    }

    export type TSliceApiState<TResponse = any, TError = Error> = ISliceApiStateNone | ISliceApiStatePending | ISliceApiStateFulfilled<TResponse> | ISliceApiStateRejected<TError>;

    export interface IValidationExceptionResponse<TErrorsKey = string> {
        message: string;
        errors: Record<TErrorsKey, string[]>;
    }

    export interface IMessageResponse<TResponseKey = undefined> {
        response: TResponseKey;
        message: string;
    }

    export interface IJwtResponse {
        access_token: string;
        token_type: 'bearer';
        expires_in: number;
        expires_at: string;
    }

    export interface IFileUrl {
        url: string;
        expires_in: number;
        expires_at: string;
    }

    export interface IPaginateResponseLink {
        url: string | null;
        label: string;
        active: boolean;
    }

    export interface IPaginateResponse<T> {
        current_page: number;
        data: T[];
        first_page_url: string;
        from: number | null;
        last_page: number;
        last_page_url: string;
        links: IPaginateResponseLink[];
        next_page_url: string | null;
        path: string;
        per_page: number;
        prev_page_url: string | null;
        to: string | null;
        total: number;
    }
}
