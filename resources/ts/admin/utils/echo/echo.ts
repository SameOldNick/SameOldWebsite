import Echo from 'laravel-echo';
import Pusher, { ChannelAuthorizerGenerator } from 'pusher-js';
import { ChannelAuthorizationData } from 'pusher-js/types/src/core/auth/options';

import { createAuthRequest } from '../api/factories';
import EchoWrapper from './wrappers/EchoWrapper';

if (!window.Pusher) {
    // This needs to be set in order for Echo to work.
    window.Pusher = Pusher;
}

/**
 * Gets options to create Echo instance.
 *
 * @export
 * @returns {object}
 */
export function getEchoOptions() {
    const authorizer: ChannelAuthorizerGenerator = (channel, options) => ({
        authorize: (socketId, callback) => {
            createAuthRequest().post<ChannelAuthorizationData>('broadcasting/auth', {
                socket_id: socketId,
                channel_name: channel.name
            }).then((response) => callback(null, response.data)).catch((error) => callback(error, null));
        }
    });

    return {
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        wsPath: import.meta.env.VITE_REVERB_PATH,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authorizer
    };
}

/**
 * Attaches Echo instance to window.
 *
 * @export
 * @param {Echo} echo
 * @returns {Echo}
 */
export function attachEchoToWindow(echo: Echo): Echo {
    window.Echo = echo;

    return echo;
}

/**
 * Wraps Echo with EchoWrapper
 *
 * @export
 * @param {Echo} echo
 * @returns {EchoWrapper}
 */
export function wrapEcho(echo: Echo): EchoWrapper {
    return new EchoWrapper(echo);
}

/**
 * Attaches EchoWrapper to window
 *
 * @export
 * @param {EchoWrapper} echo
 * @returns {EchoWrapper}
 */
export function attachEchoWrapperToWindow(echo: EchoWrapper): EchoWrapper {
    window.EchoWrapper = echo;

    return echo;
}

/**
 * Creates Echo instance.
 *
 * @export
 * @returns {Echo}
 */
export function createEcho(): Echo {
    const options = getEchoOptions();

    return new Echo(options);
}

/**
 * Creates Echo for EchoProvider
 *
 * @export
 * @return {EchoWrapper} 
 */
export default function factory() {
    const echo = attachEchoToWindow(createEcho());

    return attachEchoWrapperToWindow(wrapEcho(echo));
}