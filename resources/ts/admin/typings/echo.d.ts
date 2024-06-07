import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import EchoWrapper from '@admin/utils/echo/wrappers/EchoWrapper';

declare global {
    interface Window {
        Echo: Echo;
        EchoWrapper: EchoWrapper;
        Pusher: typeof Pusher;
    }

    type TBroadcastEventCreated = 'Illuminate\\Notifications\\Events\\BroadcastNotificationCreated';

    interface IBroadcastEventCreatedData<TType extends string = string> {
        id: string;
        type: TType;
    }

    interface IBroadcastEventCreated<TData extends IBroadcastEventCreatedData> {
        event: TBroadcastEventCreated;
        channel: string;
        data: TData;
    }
}

export default {};
