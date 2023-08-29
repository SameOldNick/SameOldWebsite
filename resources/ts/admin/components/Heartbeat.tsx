import React from 'react';

import { DateTime } from 'luxon';
import { awaitAtMost } from '@admin/utils';

const { setTimeout } = window;

export interface IHeartbeatCallbackParams {
    instance: Heartbeat;
    timestamp: DateTime;
    nextBeat: DateTime;
    count: number;
}

interface IProps {
    /**
     * The action to perform at each heart beat.
     *
     * @memberof IProps
     */
    callback: (params: IHeartbeatCallbackParams) => Promise<void>;
    /**
     * If set, the callback will be cancelled if time is reached.
     *
     * @type {number}
     * @memberof IProps
     */
    maxExecutionTimeMs?: number;
    /**
     * The minimum number of miliseconds between each heart beat.
     * The interval will be longer if the page isn't visible.
     *
     * @type {number}
     * @memberof IProps
     */
    interval: number;
    /**
     * If true, the heartbeat will be performed at the set interval. Default is true.
     *
     * @type {boolean}
     * @memberof IProps
     */
    active: boolean;
    /**
     * Performs heartbeat when initially mounted. Default is false.
     *
     * @type {boolean}
     * @memberof IProps
     */
    beatOnMount: boolean;
}

interface IState {
    nextBeat: number;
    timeout: number | null;
    hiddenSince: DateTime | null;
}

/**
 * Renders a component that performs a heartbeat at specified intervals.
 *
 * @export
 * @class Heartbeat
 * @extends {React.Component<IProps, IState>}
 */
export default class Heartbeat extends React.Component<IProps, IState> {
    public static defaultProps = {
        requireVisible: true,
        active: true,
        beatOnMount: false
    };

    private callbackCount: number = 0;

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            nextBeat: 0,
            timeout: null,
            hiddenSince: null
        };

        this.beat = this.beat.bind(this);
        this.resume = this.resume.bind(this);
        this.pause = this.pause.bind(this);
    }

    public componentDidMount() {
        const { active, beatOnMount } = this.props;

        if (active)
            this.beat(beatOnMount);
    }

    public componentDidUpdate(prevProps: Readonly<IProps>) {
        const { active } = this.props;
        const { } = this.state;

        if (active !== prevProps.active) {
            if (active)
                this.resume();
            else
                this.pause();
        }
    }

    public componentWillUnmount() {
        this.pause();
    }

    /**
     * Performs a heart beat.
     *
     * @private
     * @param {boolean} doCallback If true, the callback is executed.
     * @memberof Heartbeat
     */
    private async beat(doCallback: boolean) {
        const { active, interval, callback, maxExecutionTimeMs } = this.props;
        const { } = this.state;

        if (!active)
            return;

        const currentDate = DateTime.now();
        const nextBeat = currentDate.valueOf() + Math.abs(interval);
        const nextBeatDate = DateTime.fromMillis(nextBeat - currentDate.valueOf());

        if (doCallback) {
            const params: IHeartbeatCallbackParams = {
                instance: this,
                timestamp: currentDate,
                nextBeat: nextBeatDate,
                count: ++this.callbackCount
            };

            if (maxExecutionTimeMs === undefined) {
                await callback(params);
            } else {
                await awaitAtMost(() => callback(params), maxExecutionTimeMs);
            }
        }

        this.setState({
            nextBeat,
            timeout: setTimeout(() => this.beat(true), nextBeatDate.valueOf())
        });
    }

    /**
     * Resumes heart beats.
     *
     * @private
     * @memberof Heartbeat
     */
    private resume() {
        const { interval } = this.props;
        const { hiddenSince } = this.state;

        this.setState(
            { hiddenSince: null }, () =>
                this.beat(hiddenSince && DateTime.now().diff(hiddenSince, 'milliseconds').toMillis() >= Math.abs(interval) ? true : false)
        );
    }

    /**
     * Pauses heart beats.
     *
     * @private
     * @memberof Heartbeat
     */
    private pause() {
        this.setState(({ timeout }) => {
            if (timeout)
                clearTimeout(timeout);

            return { timeout: null, hiddenSince: DateTime.now() };
        });
    }

    public render() {
        const { } = this.props;

        return (
            <></>
        );
    }
}
