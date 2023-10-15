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

    export interface IPaginateResponseMeta {
        current_page: number;
        from: number | null;
        to: string | null;
        last_page: number;
        links: IPaginateResponseLink[];
        path: string;
        per_page: number;
        total: number;

        first_page_url: string;
        last_page_url: string;
        prev_page_url: string | null;
        next_page_url: string | null;
    }

    export interface IPaginateResponse<T> extends IPaginateResponseMeta {
        data: T[];
    }

    export interface IPaginateResponseCollection<T> {
        data: T[];

        links: {
            first: string;
            last: string;
            prev: string | null;
            next: string | null;
        };
        meta: Omit<IPaginateResponse<T>, 'first_page_url' | 'last_page_url' | 'prev_page_url' | 'next_page_url'>;
    }
}
