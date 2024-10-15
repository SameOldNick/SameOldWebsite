import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { PusherChannel, PusherEncryptedPrivateChannel, PusherPresenceChannel, PusherPrivateChannel } from 'laravel-echo/dist/channel';
import { PusherConnector } from 'laravel-echo/dist/connector';

import ChannelWrapper from './ChannelWrapper';

type EchoConnectionStates = 'initialized' | 'connecting' | 'connected' | 'unavailable' | 'failed' | 'disconnected';

/**
 * Wraps existing Laravel Echo class
 *
 * @export
 * @class EchoWrapper
 */
class EchoWrapper {
    /**
     * Creates an instance of EchoWrapper.
     * @param {Echo} echo Echo instance to wrap
     * @memberof EchoWrapper
     */
    constructor(
        public readonly echo: Echo
    ) {
    }

    /**
     * Gets the state of the WebSocket
     *
     * @readonly
     * @type {EchoConnectionStates}
     * @memberof EchoWrapper
     */
    public get connectionState(): EchoConnectionStates {
        return this.connector.pusher.connection.state || 'unavailable';
    }

    /**
     * Waits for connection state to be updated to state(s)
     *
     * @param {(EchoConnectionStates | EchoConnectionStates[])} states
     * @param {() => void} callback
     * @returns {EchoWrapper}
     * @memberof EchoWrapper
     */
    public onConnectionStateUpdated(states: EchoConnectionStates | EchoConnectionStates[], callback: Function) {
        for (const state of Array.isArray(states) ? states : [states]) {
            this.connector.pusher.connection.bind(state, () => callback());
        }

        return this;
    }

    /**
     * Calls callback when state is changed
     *
     * @param {(state: EchoConnectionStates) => void} callback
     * @returns {EchoWrapper}
     * @memberof EchoWrapper
     */
    public onConnectionStatesUpdated(callback: (state: EchoConnectionStates) => void) {
        const states: EchoConnectionStates[] = ['initialized', 'connecting', 'connected', 'unavailable', 'failed', 'disconnected'];

        for (const state of states) {
            this.connector.pusher.connection.bind(state, () => callback(state));
        }

        return this;
    }

    /**
     * Gets the Pusher connector
     *
     * @readonly
     * @type {PusherConnector}
     * @memberof EchoWrapper
     */
    public get connector(): PusherConnector {
        return this.echo.connector;
    }

    /**
     * Gets the Pusher instance
     *
     * @readonly
     * @type {Pusher}
     * @memberof EchoWrapper
     */
    public get pusher(): Pusher {
        return this.connector.pusher;
    }

    /**
     * Gets public channel
     * @param channel Name of channel
     * @returns Channel wrapper
     */
    public channel(channel: string): ChannelWrapper<PusherChannel> {
        return this.newChannelWrapper(this.echo.channel(channel) as PusherChannel);
    }

    /**
     * Joins presence channel
     * @param channel Name of channel
     * @returns Channel wrapper
     */
    public join(channel: string): ChannelWrapper<PusherPresenceChannel> {
        return this.newChannelWrapper(this.echo.join(channel) as PusherPresenceChannel);
    }

    /**
     * Listens on channel
     * @param channel Name of channel
     * @returns Channel wrapper
     */
    public listen(channel: string, event: string, callback: Function): ChannelWrapper<PusherChannel> {
        return this.newChannelWrapper(this.echo.listen(channel, event, callback) as PusherChannel);
    }

    /**
     * Gets private channel
     * @param channel Name of channel
     * @returns Channel wrapper
     */
    public private(channel: string): ChannelWrapper<PusherPrivateChannel> {
        return this.newChannelWrapper(this.echo.private(channel) as PusherPrivateChannel);
    }

    /**
     * Gets encrypted private channel
     * @param channel Name of channel
     * @returns Channel wrapper
     */
    public encryptedPrivate(channel: string): ChannelWrapper<PusherEncryptedPrivateChannel> {
        return this.newChannelWrapper(this.echo.encryptedPrivate(channel) as PusherEncryptedPrivateChannel);
    }

    /**
     * Creates new ChannelWrapper instance
     *
     * @protected
     * @template TPusherChannel
     * @param {TPusherChannel} channel
     * @returns
     * @memberof EchoWrapper
     */
    protected newChannelWrapper<TPusherChannel extends PusherChannel>(channel: TPusherChannel) {
        return new ChannelWrapper<TPusherChannel>(this, channel);
    }
}

export default EchoWrapper;
export { EchoConnectionStates };
