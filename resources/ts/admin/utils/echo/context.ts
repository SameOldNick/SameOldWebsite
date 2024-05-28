import React from 'react';

import Echo from 'laravel-echo';
import { PusherChannel } from 'laravel-echo/dist/channel';

export interface IEchoContextValue {
    echo: Echo;
}

export interface IPusherChannelValue {
    channel: PusherChannel;
}

const EchoContext = React.createContext<IEchoContextValue | undefined>(undefined);
const PusherChannelContext = React.createContext<IPusherChannelValue | undefined>(undefined);

export { EchoContext, PusherChannelContext };
