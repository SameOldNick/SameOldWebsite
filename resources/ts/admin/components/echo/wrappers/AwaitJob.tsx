import React from 'react';

import PrivateChannel from '@admin/components/echo/channels/PrivateChannel';
import Events from '@admin/components/echo/events/Events';

interface AwaitJobProps<TData> {
    uuid: string;
    onJobStarted?: (data: TData) => void;
    onJobFailed?: (data: TData) => void;
    onJobFinished?: (data: TData) => void;
}

function AwaitJob<TData extends object>({
    uuid,
    onJobStarted,
    onJobFinished,
    onJobFailed
}: AwaitJobProps<TData>) {
    const handleJobStatusEvent = React.useCallback((data: TData, event: string) => {
        if (event === '.JobStarted' && onJobStarted)
            onJobStarted(data);
        else if (event === '.JobFailed' && onJobFailed)
            onJobFailed(data);
        else if (event === '.JobCompleted' && onJobFinished)
            onJobFinished(data);
    }, [onJobStarted, onJobFailed, onJobFinished]);

    return (
        <>
            <PrivateChannel channel={`jobs.${uuid}`}>
                <Events<TData> callback={handleJobStatusEvent} />
            </PrivateChannel>
        </>
    );
}

export default AwaitJob;
