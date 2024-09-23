import React from 'react';

import { PusherPrivateChannel } from 'laravel-echo/dist/channel';

import Channel from './Channel';

import { EchoContext } from '@admin/utils/echo/context';
import ChannelWrapper from '@admin/utils/echo/wrappers/ChannelWrapper';

interface IPrivateChannelProps extends React.PropsWithChildren {
    channel: string;
}

export interface IPrivateChannelHandle {
    channel: ChannelWrapper<PusherPrivateChannel>;
}

const PrivateChannel: React.ForwardRefRenderFunction<IPrivateChannelHandle, IPrivateChannelProps> = ({ channel, children }, ref) => {
    const context = React.useContext(EchoContext);

    if (context === undefined) {
        logger.error('The Echo Provider is missing.');

        return null;
    }

    const echoChannel = React.useMemo(() => context.echo.private(channel), [channel]);

    React.useImperativeHandle(ref, () => ({
        channel: echoChannel
    }));

    return (
        <Channel channel={echoChannel}>
            {children}
        </Channel>
    );
}

export default React.forwardRef(PrivateChannel);
