import React from 'react';
import { XTerm } from 'xterm-for-react';

import { DateTime } from 'luxon';

import ProcessOutput, { IProcessBeginData, IProcessCompleteData, IProcessOutputData, ProcessOutputProps } from './ProcessOutput';

interface ProcessOutputToXTermProps extends ProcessOutputProps {
}

const ProcessOutputToXTerm: React.FC<ProcessOutputToXTermProps> = ({
    uuid,
    onProcessStarted,
    onProcessCompleted,
    onProcessOutput
}) => {
    const xtermRef = React.useRef<XTerm>(null);

    const [processStarted, setProcessStarted] = React.useState<DateTime>();
    const [processCompleted, setProcessCompleted] = React.useState<DateTime>();

    const handleProcessStartEvent = React.useCallback((data: IProcessBeginData) => {
        if (onProcessStarted)
            onProcessStarted(data);

        setProcessStarted(DateTime.fromISO(data.dateTime));
    }, [onProcessStarted]);

    const handleProcessCompleteEvent = React.useCallback((data: IProcessCompleteData) => {
        if (onProcessCompleted)
            onProcessCompleted(data);

        setProcessCompleted(DateTime.fromISO(data.dateTime));
    }, [onProcessCompleted]);

    const handleProcessOutputEvent = React.useCallback((data: IProcessOutputData) => {
        if (onProcessOutput)
            onProcessOutput(data);

        /**
         * As per: https://stackoverflow.com/a/71524508/533242
         * xtermjs requires CRLF, not just LF.
         * Using LF will cause formatting/spacing issues.
         */

        data.message.split(/\n/).map((line) => line.replace("\r", "")).forEach((line, i, lines) => {
            if (i !== lines.length - 1) {
                xtermRef.current?.terminal.writeln(line);
            } else {
                if (data.newline)
                    xtermRef.current?.terminal.writeln(line);
                else
                    xtermRef.current?.terminal.write(line);
            }
        });
    }, [onProcessOutput]);

    return (
        <>
            <ProcessOutput
                uuid={uuid}
                onProcessStarted={handleProcessStartEvent}
                onProcessOutput={handleProcessOutputEvent}
                onProcessCompleted={handleProcessCompleteEvent}
            />

            {processStarted && <XTerm ref={xtermRef} />}
        </>
    );
}

export default ProcessOutputToXTerm;
export { ProcessOutputToXTermProps };
