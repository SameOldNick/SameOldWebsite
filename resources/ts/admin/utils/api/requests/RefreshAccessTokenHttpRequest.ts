import axios, { AxiosRequestConfig } from 'axios';
import IHttpRequest, { TReturnResponse } from './IHttpRequest';

export default class RefreshAccessTokenHttpRequest implements IHttpRequest {

    /**
     * Initializes instance of RefreshAccessTokenHttpRequest.
     * @memberof HttpRequest
     */
    constructor(
        private readonly httpRequest: IHttpRequest,
        private readonly requestNewToken: () => Promise<IJsonWebToken>,
        private readonly storeAccessToken: (accessToken: IJsonWebToken) => void,
    ) {
    }

    /**
     * Creates a new AxiosInstance and performs a GET request.
     *
     * @param {string} url URL to get.
     * @param {object} [params] Any parameters to include in GET request.
     * @memberof HttpRequest
     */
    public async get<TResponseData = any, TRequestData = object>(url: string, params?: TRequestData, customConfig?: AxiosRequestConfig) {
        return this.refreshTokenOnError(() => this.httpRequest.get<TResponseData, TRequestData>(url, params, customConfig));
    }

    /**
     * Creates a new AxiosInstance and performs a POST request.
     *
     * @param {string} url URL to send post to.
     * @param {object} params Any parameters to include in POST request.
     * @memberof HttpRequest
     */
    public post<TResponseData = any, TRequestData = object>(url: string, params: TRequestData, customConfig?: AxiosRequestConfig) {
        return this.refreshTokenOnError(() => this.httpRequest.post<TResponseData, TRequestData>(url, params, customConfig));
    }

    /**
     * Creates a new AxiosInstance and performs a PUT request.
     *
     * @param {string} url URL to send put to.
     * @param {object} params Any parameters to include in PUT request.
     * @memberof HttpRequest
     */
    public put<TResponseData = any, TRequestData = object>(url: string, params: TRequestData, customConfig?: AxiosRequestConfig) {
        return this.refreshTokenOnError(() => this.httpRequest.put<TResponseData, TRequestData>(url, params, customConfig));
    }

    /**
     * Creates a new AxiosInstance and performs a DELETE request.
     *
     * @param {string} url URL to send delete to.
     * @param {object} [params] Any parameters to include in DELETE request.
     * @memberof HttpRequest
     */
    public delete<TResponseData = any, TRequestData = object>(url: string, params?: TRequestData, customConfig?: AxiosRequestConfig) {
        return this.refreshTokenOnError(() => this.httpRequest.delete<TResponseData, TRequestData>(url, params, customConfig));
    }

    /**
     * Tries to refresh token if request failed.
     *
     * @private
     * @template TResponseData
     * @param {() => TReturnResponse<TResponseData>} performRequest
     * @returns {TReturnResponse<TResponseData>}
     * @memberof RefreshAccessTokenHttpRequest
     */
    private async refreshTokenOnError<TResponseData>(performRequest: () => TReturnResponse<TResponseData>) {
        try {
            return await performRequest();
        } catch (e) {
            if (axios.isAxiosError(e)) {
                if (e.response !== undefined && e.response.status === 401) {
                    // Request new access token

                    try {
                        const response = await this.requestNewToken();

                        this.storeAccessToken(response);

                        return await performRequest();

                    } catch (innerErr) {

                    }
                }
            }

            // Send original error back
            throw e;
        }
    }
}
