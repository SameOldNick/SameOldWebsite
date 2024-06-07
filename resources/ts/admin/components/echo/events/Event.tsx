import React from 'react';

import { PusherChannelContext } from '@admin/utils/echo/context';

interface IEventProps<TEventData extends object> extends React.PropsWithChildren {
    event: string;
    whisper?: boolean;
    callback?: (e: TEventData, event: string) => void;
}

function Event<TEventData extends object = any>({ event, callback, whisper = false, children }: IEventProps<TEventData>) {
    const context = React.useContext(PusherChannelContext);

    if (context === undefined) {
        logger.error('The Echo Channel Provider is missing.');

        return null;
    }

    const { channel } = context;

    React.useEffect(() => {
        if (whisper)
            channel.channel.listenForWhisper(event, onEvent);
        else
            channel.channel.listen(event, onEvent);

        return () => {
            if (whisper)
                channel.channel.stopListeningForWhisper(event, onEvent);
            else
                channel.channel.stopListening(event, onEvent);
        };
    }, [channel, event, whisper, callback]);

    const onEvent = React.useCallback((e: TEventData) => {
        if (callback)
            callback(e, event);

    }, [callback, event]);

    return children;
}

export default Event;
