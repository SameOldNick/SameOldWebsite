import React from 'react';

import { EchoContext } from '@admin/utils/echo/context';
import Channel from './Channel';

interface IPrivateChannelProps extends React.PropsWithChildren {
    channel: string;
}

const PrivateChannel: React.FC<IPrivateChannelProps> = ({ channel, children }) => {
    const context = React.useContext(EchoContext);

    if (context === undefined) {
        logger.error('The Echo Provider is missing.');

        return null;
    }

    const echoChannel = React.useMemo(() => context.echo.private(channel), [channel]);

    return (
        <Channel channel={echoChannel}>
            {children}
        </Channel>
    );
}

export default PrivateChannel;
