import { AxiosRequestConfig, AxiosResponse } from 'axios';

export type TReturnResponse<T> = Promise<AxiosResponse<T>>;

export default interface IHttpRequest {
    /**
     * Creates a new AxiosInstance and performs a GET request.
     *
     * @param {string} url URL to get.
     * @param {object} [params] Any parameters to include in GET request.
     * @memberof HttpRequest
     */
    get<TResponseData = any, TRequestData = object>(url: string, params?: TRequestData, customConfig?: AxiosRequestConfig): TReturnResponse<TResponseData>;

    /**
     * Creates a new AxiosInstance and performs a POST request.
     *
     * @param {string} url URL to send post to.
     * @param {object} params Any parameters to include in POST request.
     * @memberof HttpRequest
     */
    post<TResponseData = any, TRequestData = object>(url: string, params: TRequestData, customConfig?: AxiosRequestConfig): TReturnResponse<TResponseData>;

    /**
     * Creates a new AxiosInstance and performs a PUT request.
     *
     * @param {string} url URL to send put to.
     * @param {object} params Any parameters to include in PUT request.
     * @memberof HttpRequest
     */
    put<TResponseData = any, TRequestData = object>(url: string, params: TRequestData, customConfig?: AxiosRequestConfig): TReturnResponse<TResponseData>;
    /**
     * Creates a new AxiosInstance and performs a DELETE request.
     *
     * @param {string} url URL to send delete to.
     * @param {object} [params] Any parameters to include in DELETE request.
     * @memberof HttpRequest
     */
    delete<TResponseData = any, TRequestData = object>(url: string, params?: TRequestData, customConfig?: AxiosRequestConfig): TReturnResponse<TResponseData>;
}
