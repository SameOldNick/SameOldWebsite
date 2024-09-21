import React from 'react';

import { PusherChannel } from 'laravel-echo/dist/channel';
import EchoWrapper from './wrappers/EchoWrapper';
import ChannelWrapper from './wrappers/ChannelWrapper';

export interface IEchoContextValue {
    echo: EchoWrapper | null;
}

export interface IPusherChannelValue {
    channel: ChannelWrapper<PusherChannel>;
}

const EchoContext = React.createContext<IEchoContextValue | undefined>(undefined);
const PusherChannelContext = React.createContext<IPusherChannelValue | undefined>(undefined);

export { EchoContext, PusherChannelContext };
