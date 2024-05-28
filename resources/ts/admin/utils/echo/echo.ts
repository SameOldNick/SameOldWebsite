import Echo from 'laravel-echo';
import Pusher, { ChannelAuthorizerGenerator } from 'pusher-js';
import { ChannelAuthorizationData } from 'pusher-js/types/src/core/auth/options';

import { createAuthRequest } from '../api/factories';

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
export function attachEchoToWindow(echo: Echo) {
    window.Echo = echo;

    return echo;
}

/**
 * Creates Echo instance.
 *
 * @export
 * @returns {Echo}
 */
export default function createEcho() {
    const options = getEchoOptions();

    return new Echo(options);
}
