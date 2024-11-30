import React from 'react';

import PrivateChannel from '@admin/components/echo/channels/PrivateChannel';
import Events from '@admin/components/echo/events/Events';

interface IProps {
    uuid: string;
    onJobStarted?: (data: JobData) => void;
    onJobFailed?: (data: JobData) => void;
    onJobFinished?: (data: JobData) => void;
}

interface JobData {
    dateTime: string;
}

const BackupDestinationTestAwait: React.FC<IProps> = ({
    uuid,
    onJobStarted,
    onJobFinished,
    onJobFailed
}) => {
    const handleJobStatusEvent = React.useCallback((data: JobData, event: string) => {
        if (event === '.JobStarted' && onJobStarted)
            onJobStarted(data);
        else if (event === '.JobFailed' && onJobFailed)
            onJobFailed(data);
        else if (event === '.JobCompleted' && onJobFinished)
            onJobFinished(data);
    }, []);

    return (
        <>
            <PrivateChannel channel={`jobs.${uuid}`}>
                <Events callback={handleJobStatusEvent} />
            </PrivateChannel>
        </>
    );
}

export default BackupDestinationTestAwait;
