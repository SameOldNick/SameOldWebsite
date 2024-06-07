import { Channel } from 'pusher-js';
import { PusherChannel } from 'laravel-echo/dist/channel';

/**
 * Wraps existing PusherChannel class.
 * There's various issues with Laravel Echo's implementation, including:
 *  - Uses the 'any' type instead of providing specific types.
 *  - The functionality to trigger events from the frontend is hidden.
 *
 * @export
 * @class ChannelWrapper
 * @template TChannel
 */
export default class ChannelWrapper<TChannel extends PusherChannel> {
    /**
     * Creates an instance of ChannelWrapper.
     * @param {TChannel} channel Channel to wrap
     * @memberof ChannelWrapper
     */
    constructor(
        public readonly channel: TChannel
    ) {

    }

    /**
     * Gets the pusher subscription channel.
     *
     * @readonly
     * @memberof ChannelWrapper
     */
    public get subscription() {
        return this.channel.subscription ? this.channel.subscription as Channel : null;
    }

    /**
     * Triggers an event on the websocket.
     * @param event Event name
     * @param data Data to broadcast
     * @returns True if successful
     */
    public send<TData>(event: string, data: TData) {
        if (!this.subscription) {
            throw new Error(`Not subscribed to channel "${this.channel.name}"`);
        }

        return this.subscription.trigger(event, data);
    }
}
