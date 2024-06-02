import React from 'react';

import Event from './Event';

interface IBroadcastedEventTypeProps {
    type: string;
}

interface IBroadcastedEventMatcherProps {
    matcher: (type: string) => boolean;
}

interface IBroadcastedEventProps<TData extends IBroadcastEventCreatedData> extends React.PropsWithChildren {
    callback?: (e: TData, channel: string) => void;
}

type TProps<TData extends IBroadcastEventCreatedData> = (IBroadcastedEventTypeProps | IBroadcastedEventMatcherProps) & IBroadcastedEventProps<TData>;

export default function BroadcastedEvent<TData extends IBroadcastEventCreatedData>({ callback, children, ...props }: TProps<TData>) {
    const matcher = React.useCallback((type: string) => {
        if ('type' in props)
            return type === props.type;
        else
            return props.matcher(type);
    }, [props]);

    const handleCallback = React.useCallback((e: IBroadcastEventCreated<TData>) => {
        if (callback && matcher(e.data.type)) {
            callback(e.data, e.channel);
        }
    }, [callback, matcher]);

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
