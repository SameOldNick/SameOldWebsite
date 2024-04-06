import React from 'react';
import { DateTime } from 'luxon';
import { awaitAtMost } from '@admin/utils';

const { setTimeout, clearTimeout } = window;

export interface IHeartbeatCallbackParams {
    timestamp: DateTime;
    nextBeat: DateTime;
    count: number;
}

interface IProps {
    callback: (params: IHeartbeatCallbackParams) => Promise<void>;
    maxExecutionTimeMs?: number;
    interval: number;
    active: boolean;
    beatOnMount?: boolean;
}

/**
 * Renders a component that performs a heartbeat at specified intervals.
 *
 * @param {IProps} {
 *     callback,
 *     maxExecutionTimeMs,
 *     interval,
 *     active,
 *     beatOnMount = false
 * }
 */
const Heartbeat: React.FC<IProps> = ({
    callback,
    maxExecutionTimeMs,
    interval,
    active,
    beatOnMount = false
}: IProps) => {

    const [nextBeat, setNextBeat] = React.useState(0);
    const [hiddenSince, setHiddenSince] = React.useState<DateTime>();

    let callbackCount = 0;
    let timeout: number | undefined;

    React.useEffect(() => {
        const beat = async (doCallback: boolean) => {
            if (!active) return;

            const currentDate = DateTime.now();
            const nextBeat = currentDate.valueOf() + Math.abs(interval);
            const nextBeatDate = DateTime.fromMillis(nextBeat - currentDate.valueOf());

            if (doCallback) {
                const params: IHeartbeatCallbackParams = {
                    timestamp: currentDate,
                    nextBeat: nextBeatDate,
                    count: ++callbackCount
                };

                if (maxExecutionTimeMs === undefined) {
                    await callback(params);
                } else {
                    await awaitAtMost(() => callback(params), maxExecutionTimeMs);
                }
            }

            setNextBeat(nextBeat);

            timeout = setTimeout(() => beat(true), nextBeatDate.valueOf());
        };

        const resume = () => {
            setHiddenSince(undefined);
            timeout = setTimeout(() => beat(hiddenSince && DateTime.now().diff(hiddenSince, 'milliseconds').toMillis() >= Math.abs(interval) ? true : false), interval);

        };

        const pause = () => {
            if (timeout) {
                clearTimeout(timeout);
                timeout = undefined;
            }

            setHiddenSince(DateTime.now());
        };

        const terminate = () => {
            if (timeout) {
                clearTimeout(timeout);
                timeout = undefined;
            }
        }

        if (active) {
            resume();
        } else {
            pause();
        }

        return () => {
            terminate();
        };
    }, [callback, maxExecutionTimeMs, interval, active, beatOnMount]);

    return <></>;
};

export default Heartbeat;

