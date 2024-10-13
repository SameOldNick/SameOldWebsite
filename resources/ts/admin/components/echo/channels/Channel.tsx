import React from 'react';

import { PusherChannel } from 'laravel-echo/dist/channel';

import ChannelWrapper, { ListenCallback } from '@admin/utils/echo/wrappers/ChannelWrapper';
import { EchoContext, PusherChannelContext } from '@admin/utils/echo/context';

interface IChannelProps extends React.PropsWithChildren {
    channel: string | ChannelWrapper<PusherChannel>;
}

export interface IChannelHandle {
    channel: ChannelWrapper<PusherChannel>;
}

const Channel: React.ForwardRefRenderFunction<IChannelHandle, IChannelProps> = ({ channel, children }, ref) => {
    const context = React.useContext(EchoContext);

    if (context === undefined) {
        logger.error('The Echo Provider is missing.');

        return null;
    }

    const pusherChannel = React.useMemo(() => typeof channel === 'string' ? context.echo.channel(channel) : channel, [channel]);

    React.useImperativeHandle(ref, () => ({
        channel: pusherChannel
    }), [pusherChannel]);

    React.useEffect(() => {
        const callback: ListenCallback = ({ state, event }) => {
            if (state === 'listening' || state === 'stopListening')
                logger.debug(`Channel ${pusherChannel.name} is ${state === 'listening' ? 'listening' : 'no longer listening'} for '${event}' events`);
        }

        pusherChannel.registerChannelListener(callback);

        return () => {
            pusherChannel.unregisterChannelListener(callback);
        }
    }, [pusherChannel]);

    return (
        <PusherChannelContext.Provider value={{ channel: pusherChannel }}>
            {children}
        </PusherChannelContext.Provider>
    );
}

export default React.forwardRef(Channel);
