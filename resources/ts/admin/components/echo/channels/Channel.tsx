import React from 'react';
import { Channel as EchoChannel } from 'laravel-echo';

import { EchoContext, PusherChannelContext } from '@admin/utils/echo/context';
import { PusherChannel } from 'laravel-echo/dist/channel';

interface IChannelProps extends React.PropsWithChildren {
    channel: string | EchoChannel;
}

const isPusherChannel = (channel: EchoChannel): channel is PusherChannel => 'pusher' in channel;

const Channel: React.FC<IChannelProps> = ({ channel, children }) => {
    const context = React.useContext(EchoContext);

    if (context === undefined) {
        logger.error('The Echo Provider is missing.');

        return null;
    }

    const pusherChannel = React.useMemo<PusherChannel | undefined>(() => {
        const echoChannel = typeof channel === 'string' ? context.echo.channel(channel) : channel;

        return isPusherChannel(echoChannel) ? echoChannel : undefined;
    }, [channel]);

    if (pusherChannel === undefined) {
        logger.error('Channel is not a PusherChannel.');

        return null;
    }

    return (
        <PusherChannelContext.Provider value={{ channel: pusherChannel }}>
            {children}
        </PusherChannelContext.Provider>
    );
}

export default Channel;
