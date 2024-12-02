import React from 'react';

import PrivateChannel, { IPrivateChannelHandle } from '@admin/components/echo/channels/PrivateChannel';
import Event from '@admin/components/echo/events/Event';

interface ProcessOutputProps {
    uuid: string;
    onProcessStarted?: (data: IProcessBeginData) => void;
    onProcessCompleted?: (data: IProcessCompleteData) => void;
    onProcessOutput?: (data: IProcessOutputData) => void;
}

interface IProcessBeginData {
    dateTime: string;
    uuid: string;
}

interface IProcessCompleteData {
    dateTime: string;
    uuid: string;
}

interface IProcessOutputData {
    dateTime: string;
    uuid: string;
    message: string;
    newline: boolean;
}

const ProcessOutput = React.forwardRef<IPrivateChannelHandle, ProcessOutputProps>(({
    uuid,
    onProcessStarted,
    onProcessCompleted,
    onProcessOutput
}, ref) => {
    const handleProcessStartEvent = React.useCallback((data: IProcessBeginData, event: string) => {
        if (onProcessStarted)
            onProcessStarted(data);
    }, [onProcessStarted]);

    const handleProcessCompleteEvent = React.useCallback((data: IProcessCompleteData, event: string) => {
        if (onProcessCompleted)
            onProcessCompleted(data);
    }, [onProcessCompleted]);

    const handleProcessOutputEvent = React.useCallback((data: IProcessOutputData, event: string) => {
        if (onProcessOutput)
            onProcessOutput(data);
    }, [onProcessOutput]);

    return (
        <>
            <PrivateChannel ref={ref} channel={`processes.${uuid}`}>
                <Event event='.ProcessBegin' callback={handleProcessStartEvent} />
                <Event event='.ProcessComplete' callback={handleProcessCompleteEvent} />
                <Event event='.ProcessOutput' callback={handleProcessOutputEvent} />
            </PrivateChannel>
        </>
    );
});

export default ProcessOutput;
export { ProcessOutputProps, IProcessBeginData, IProcessOutputData, IProcessCompleteData };
