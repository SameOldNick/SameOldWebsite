import axios, { AxiosInstance, AxiosRequestConfig } from 'axios';
import IHttpRequest from './IHttpRequest';

type TAxiosCreateCallback = (config?: AxiosRequestConfig) => AxiosInstance;
type TAxiosCreatedCallback = (instance: AxiosInstance, method: THttpMethod) => void;
export type THttpMethod = 'get' | 'post' | 'put' | 'patch' | 'delete';

export default class HttpRequest implements IHttpRequest {
    private readonly _config: AxiosRequestConfig;

    private axiosCreatedCallbacks: TAxiosCreatedCallback[];
    private static axiosCreatedCallbacksAll: TAxiosCreatedCallback[] = [];
    private static axiosCreateCallback?: TAxiosCreateCallback;

    /**
     * Initializes instance .
     * @memberof HttpRequest
     */
    constructor(config: AxiosRequestConfig) {
        this._config = config;
        this.axiosCreatedCallbacks = [];
    }

    /**
     * Creates Axios instance.
     *
     * @param {object} [customConfig] Any other configuration to be set.
     * @returns {AxiosInstance}
     * @memberof HttpRequest
     */
    public createAxiosInstance(customConfig?: AxiosRequestConfig): AxiosInstance {
        const config: AxiosRequestConfig = {
            ...this._config,
            ...customConfig
        };

        const instance: AxiosInstance = HttpRequest.axiosCreateCallback === undefined ? axios.create(config) : HttpRequest.axiosCreateCallback(config);

        return instance;
    }

    /**
     * Initializes an Axios instance.
     *
     * @param {AxiosInstance} instance Axios instance
     * @param {THttpMethod} method Method to use (GET, POST, etc.)
     * @returns {AxiosInstance} Axios instance
     * @memberof HttpRequest
     */
    public initializeAxiosInstance(instance: AxiosInstance, method: THttpMethod) {
        this.axiosCreatedCallbacks.forEach((cb) => cb(instance, method));
        HttpRequest.axiosCreatedCallbacksAll.forEach((cb) => cb(instance, method));

        return instance;
    }

    public static onAxiosCreateCallback(callback?: TAxiosCreateCallback) {
        HttpRequest.axiosCreateCallback = callback;
    }

    /**
     * Adds a callback that is fired whenever a new AxiosInstance is created for this HttpRequest instance.
     *
     * @param {TAxiosCreatedCallback} callback
     * @returns {() => void} Callback to unsubscribe.
     * @memberof HttpRequest
     */
    public onAxiosInstanceCreated(callback: TAxiosCreatedCallback): () => void {
        this.axiosCreatedCallbacks.push(callback);

        return () => this.axiosCreatedCallbacks = this.axiosCreatedCallbacks.filter((cb) => cb !== callback);
    }

    /**
     * Adds a callback that is fired when any AxiosInstance is created.
     *
     * @static
     * @param {TAxiosCreatedCallback} callback
     * @returns {() => void} Callback to unsubscribe.
     * @memberof HttpRequest
     */
    public static onAxiosInstanceCreated(callback: TAxiosCreatedCallback): () => void {
        HttpRequest.axiosCreatedCallbacksAll.push(callback);

        return () => HttpRequest.axiosCreatedCallbacksAll = HttpRequest.axiosCreatedCallbacksAll.filter((cb) => cb !== callback);
    }

    /**
     * Creates a new AxiosInstance and performs a GET request.
     *
     * @param {string} url URL to get.
     * @param {object} [params] Any parameters to include in GET request.
     * @memberof HttpRequest
     */
    public get<TResponseData = any, TRequestData = object>(url: string, params?: TRequestData, customConfig?: AxiosRequestConfig) {
        const instance = this.initializeAxiosInstance(this.createAxiosInstance(customConfig), 'get');

        return instance.get<TResponseData>(url, { params });
    }

    /**
     * Creates a new AxiosInstance and performs a POST request.
     *
     * @param {string} url URL to send post to.
     * @param {object} params Any parameters to include in POST request.
     * @memberof HttpRequest
     */
    public post<TResponseData = any, TRequestData = object>(url: string, params: TRequestData, customConfig?: AxiosRequestConfig) {
        const instance = this.initializeAxiosInstance(this.createAxiosInstance(customConfig), 'post');

        return instance.post<TResponseData>(url, params);
    }

    /**
     * Creates a new AxiosInstance and performs a PUT request.
     *
     * @param {string} url URL to send put to.
     * @param {object} params Any parameters to include in PUT request.
     * @memberof HttpRequest
     */
    public put<TResponseData = any, TRequestData = object>(url: string, params: TRequestData, customConfig?: AxiosRequestConfig) {
        const instance = this.initializeAxiosInstance(this.createAxiosInstance(customConfig), 'put');

        return instance.put<TResponseData>(url, params);
    }

    /**
     * Creates a new AxiosInstance and performs a DELETE request.
     *
     * @param {string} url URL to send delete to.
     * @param {object} [params] Any parameters to include in DELETE request.
     * @memberof HttpRequest
     */
    public delete<TResponseData = any, TRequestData = object>(url: string, params?: TRequestData, customConfig?: AxiosRequestConfig) {
        const instance = this.initializeAxiosInstance(this.createAxiosInstance(customConfig), 'delete');

        return instance.delete<TResponseData>(url, { params });
    }
}
