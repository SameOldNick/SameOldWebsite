import React from 'react';

import Event from './Event';

interface IBroadcastedEventsProps<TData extends IBroadcastEventCreatedData> extends React.PropsWithChildren {
    callback?: (e: TData, channel: string) => void;
}

export default function BroadcastedEvents<TData extends IBroadcastEventCreatedData>({ callback, children }: IBroadcastedEventsProps<TData>) {
    const handleCallback = React.useCallback((e: IBroadcastEventCreated<TData>) => {
        if (callback)
            callback(e.data, e.channel);
    }, [callback]);

    return (
        <>
            <Event<IBroadcastEventCreated<TData>>
                event='Illuminate\Notifications\Events\BroadcastNotificationCreated'
                callback={handleCallback}
            >
                {children}
            </Event>
        </>
    );
}
