import React from 'react';

import { PusherChannelContext } from '@admin/utils/echo/context';

interface IEventProps<TEventData extends object> extends React.PropsWithChildren {
    matcher?: (event: string) => boolean;
    callback: (e: TEventData, event: string) => void;
}

function Events<TEventData extends object = any>({ matcher, callback, children }: IEventProps<TEventData>) {
    const context = React.useContext(PusherChannelContext);

    if (context === undefined) {
        logger.error('The Echo Channel Provider is missing.');

        return null;
    }

    const { channel } = context;

    const onEvent = React.useCallback((event: string, data: TEventData) => {
        if (matcher === undefined || matcher(event))
            callback(data, event);
    }, [matcher, callback]);

    React.useEffect(() => {
        channel.listenToAll(onEvent);

        return () => {
            channel.stopListeningToAll(onEvent);
        };
    }, [channel]);

    return children;
}

export default Events;
