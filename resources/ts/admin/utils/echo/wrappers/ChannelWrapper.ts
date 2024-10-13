import { Channel } from 'pusher-js';
import { PusherChannel } from 'laravel-echo/dist/channel';
import EchoWrapper from './EchoWrapper';

type ListenCallbackStates = 'listen' | 'listening' | 'stopListening';
type ListenCallback = <TChannel extends PusherChannel>(params: {
    channel: ChannelWrapper<TChannel>;
    event: string;
    onEvent: Function;
    whisper: boolean;
    state: ListenCallbackStates;
}) => void;

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
class ChannelWrapper<TChannel extends PusherChannel> {
    private channelListeners: ListenCallback[] = [];

    /**
     * Creates an instance of ChannelWrapper.
     * @param {EchoWrapper} echo Echo wrapper
     * @param {TChannel} channel Channel to wrap
     * @memberof ChannelWrapper
     */
    constructor(
        public readonly echo: EchoWrapper,
        public readonly channel: TChannel
    ) {

    }

    /**
     * Gets the channel name
     *
     * @readonly
     * @type {string}
     * @memberof ChannelWrapper
     */
    public get name(): string {
        return this.channel.name;
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
     * Listens for an event on the channel
     *
     * @param {string} event
     * @param {Function} onEvent
     * @returns {ChannelWrapper}
     * @memberof ChannelWrapper
     */
    public listen(event: string, onEvent: Function) {
        this.fireChannelListeners('listen', event, onEvent, false);

        this.channel.listen(event, onEvent);

        this.fireChannelListeners('listening', event, onEvent, false);

        return this;
    }

    /**
     * Listens for whisper event on the channel
     *
     * @param {string} event
     * @param {Function} onEvent
     * @returns {ChannelWrapper}
     * @memberof ChannelWrapper
     */
    public listenForWhisper(event: string, onEvent: Function) {
        this.fireChannelListeners('listen', event, onEvent, true);

        this.channel.listenForWhisper(event, onEvent);

        this.fireChannelListeners('listening', event, onEvent, true);

        return this;
    }

    /**
     * Stops listening for event on the channel
     *
     * @param {string} event
     * @param {Function} onEvent
     * @returns {ChannelWrapper}
     * @memberof ChannelWrapper
     */
    public stopListening(event: string, onEvent: Function) {
        this.channel.stopListening(event, onEvent);

        this.fireChannelListeners('stopListening', event, onEvent, false);

        return this;
    }

    /**
     * Stops listening for whisper events on the channel
     *
     * @param {string} event
     * @param {Function} onEvent
     * @returns {ChannelWrapper}
     * @memberof ChannelWrapper
     */
    public stopListeningForWhisper(event: string, onEvent: Function) {
        this.channel.stopListeningForWhisper(event, onEvent);

        this.fireChannelListeners('stopListening', event, onEvent, true);

        return this;
    }

    /**
     * Registers a channel listener
     * The channel listener is called when an event is listening or no longer listening.
     *
     * @param {ListenCallback} callback
     * @returns {ChannelWrapper}
     * @memberof ChannelWrapper
     */
    public registerChannelListener(callback: ListenCallback) {
        this.channelListeners.push(callback);

        return this;
    }

    /**
     * Unregisters a channel listener
     *
     * @param {ListenCallback} callback
     * @returns {ChannelWrapper}
     * @memberof ChannelWrapper
     */
    public unregisterChannelListener(callback: ListenCallback) {
        this.channelListeners = this.channelListeners.filter((value) => value !== callback);

        return this;
    }

    /**
     * Fires channel listeners
     *
     * @param {ListenCallbackStates} state
     * @param {string} event
     * @param {Function} onEvent
     * @param {boolean} whisper
     * @returns {ChannelWrapper}
     * @memberof ChannelWrapper
     */
    public fireChannelListeners(state: ListenCallbackStates, event: string, onEvent: Function, whisper: boolean) {
        this.channelListeners.forEach((callback) => {
            callback({
                channel: this,
                state,
                event,
                onEvent,
                whisper
            });
        });

        return this;
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

export default ChannelWrapper;
export { ListenCallback, ListenCallbackStates };
