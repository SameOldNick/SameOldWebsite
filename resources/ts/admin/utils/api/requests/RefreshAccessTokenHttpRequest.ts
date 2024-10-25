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
        if (this.shouldMockMethod('put', params)) {
            return this.mockMethod('put', url, params, customConfig);
        }

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
        if (this.shouldMockMethod('delete', params)) {
            return this.mockMethod('delete', url, params, customConfig);
        }

        return this.refreshTokenOnError(() => this.httpRequest.delete<TResponseData, TRequestData>(url, params, customConfig));
    }

    /**
     * Checks if request method should be mocked
     *
     * @template TRequestData
     * @param {string} method Request method (post, put, etc.)
     * @param {TRequestData} params Request data
     * @returns {boolean}
     * @memberof RefreshAccessTokenHttpRequest
     */
    public shouldMockMethod<TRequestData = object>(method: string, params: TRequestData) {
        // Laravel doesn't play nice with FormData submitted with PUT, PATCH, or DELETE request.
        // Source: https://stackoverflow.com/a/50691997/533242
        const methods = ['put', 'patch', 'delete'];

        return methods.includes(method.toLowerCase()) && params instanceof FormData;
    }

    /**
     * Mocks request method by sending it through a POST request
     *
     * @template TResponseData
     * @template TRequestData
     * @param {string} method Request method (post, put, etc)
     * @param {string} url URL
     * @param {TRequestData} params Request parameters
     * @param {AxiosRequestConfig} [customConfig]
     * @returns
     * @memberof RefreshAccessTokenHttpRequest
     */
    public mockMethod<TResponseData = any, TRequestData = object>(method: string, url: string, params: TRequestData, customConfig?: AxiosRequestConfig) {
        // Construct the URL with the _method parameter, accounting for existing query parameters
        const separator = url.includes('?') ? '&' : '?';
        const modifiedUrl = `${url}${separator}_method=${method}`;

        // Call the post method with the modified URL, params, and optional config
        return this.post<TResponseData, TRequestData>(modifiedUrl, params, customConfig);
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
                        logger.error(innerErr);
                    }
                }
            }

            // Send original error back
            throw e;
        }
    }
}
