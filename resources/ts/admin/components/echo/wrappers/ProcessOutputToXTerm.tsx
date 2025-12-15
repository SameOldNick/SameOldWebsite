import React from 'react';
import { useXTerm } from 'react-xtermjs';

import S from 'string';
import { DateTime } from 'luxon';

import ProcessOutput, { IProcessBeginData, IProcessCompleteData, IProcessOutputData, ProcessOutputProps } from './ProcessOutput';
import classNames from 'classnames';

type ProcessOutputToXTermProps = ProcessOutputProps;

const ProcessOutputToXTerm: React.FC<ProcessOutputToXTermProps> = ({
    uuid,
    onProcessStarted,
    onProcessCompleted,
    onProcessOutput
}) => {
    const { ref, instance: xterm } = useXTerm();

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

        if (!xterm) {
            console.warn('XTerm instance not ready yet');
            return;
        }

        data.message.split(/\n/).map((line) => S(line).replaceAll("\r", "").s).forEach((line, i, lines) => {
            if (i !== lines.length - 1) {
                xterm.writeln(line);
            } else {
                if (data.newline)
                    xterm.writeln(line);
                else
                    xterm.write(line);
            }
        });
    }, [onProcessOutput, xterm]);

    return (
        <>
            <ProcessOutput
                uuid={uuid}
                onProcessStarted={handleProcessStartEvent}
                onProcessOutput={handleProcessOutputEvent}
                onProcessCompleted={handleProcessCompleteEvent}
            />

            <>
                <div className={classNames({ 'd-none': !processStarted })} style={{ overflowY: 'auto', border: '1px solid #ccc', borderRadius: '4px' }}>
                    <div ref={ref} style={{ width: '100%', height: '100%' }} />
                </div>
            </>
        </>
    );
}

export default ProcessOutputToXTerm;
export { ProcessOutputToXTermProps };
