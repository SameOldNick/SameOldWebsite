import { buildUrl } from 'build-url-ts';
import { AxiosRequestConfig } from 'axios';
import { Store } from 'redux';

import account from '@admin/store/slices/account';

import HttpRequest from './requests/HttpRequest';
import RefreshAccessTokenHttpRequest from './requests/RefreshAccessTokenHttpRequest';

const API_URL = import.meta.env.VITE_API_URL || '';

type TUrlOptions = Parameters<typeof buildUrl>[1];

/**
 * Creates a URL for the API
 * @param path Path to append to API URL
 * @param options Any additional options
 * @returns URL to API
 */
export const createUrl = (path?: string, options?: Omit<TUrlOptions, 'path'>) => buildUrl(API_URL, { path, ...options });

/**
 * Creates a HttpRequest instance for performing requests on the API
 * @returns HttpRequest instance
 */
export const createRequest = (config: AxiosRequestConfig = {}) => {
    const baseURL = createUrl();

    return new HttpRequest({
		baseURL,
		...config
	});
}

export const createAuthRequestWithStore = (store: Store, config: AxiosRequestConfig = {}) => {
    const request = createRequest(config);

    request.onAxiosInstanceCreated((instance) => {
        const { account: { stage } } = store.getState();  // Access store here

        if (stage.stage === 'authenticated') {
            instance.interceptors.request.use((config) => {
                if (config.headers) {
                    config.headers.Authorization = `Bearer ${stage.accessToken.access_token}`;
                }
                return config;
            });
        }
    });

    const requestNewToken = async () => {
        const { account: { stage } } = store.getState();

        if (stage.stage !== 'authenticated')
            throw new Error('Not authenticated.');

        const refreshToken = stage.refreshToken.access_token;

        const request = createRequest({
            headers: {
                'Authorization': `Bearer ${refreshToken}`
            }
        });

        const response = await request.post<IJsonWebToken>('/auth/refresh', {});

        return response.data;
    }

    const storeAccessToken = (accessToken: IJsonWebToken) => {
        const { account: { stage } } = store.getState();

        if (stage.stage !== 'authenticated') {
            throw new Error('Unable to get refresh token');
        }

        store.dispatch(account.actions.authStage({ stage: 'authenticated', accessToken, refreshToken: stage.refreshToken }));
    }

    return new RefreshAccessTokenHttpRequest(request, requestNewToken, storeAccessToken);
};

let initializedStore: Store | null = null; // Lazy store reference

/**
 * Attaches token to each API request for authenticated API calls
 * @returns Request instance
 */
export const createAuthRequest = (config: AxiosRequestConfig = {}) => {
    if (!initializedStore) {
        throw new Error("Store has not been initialized.");
    }

    return createAuthRequestWithStore(initializedStore, config);
}

// Function to set the store after it's initialized
export const setStore = (store: Store) => {
    initializedStore = store;
};