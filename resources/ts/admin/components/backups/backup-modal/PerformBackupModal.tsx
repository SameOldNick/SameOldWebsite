import React from 'react';
import { Alert, Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';
import { XTerm } from 'xterm-for-react';
import withReactContent from 'sweetalert2-react-content';

import { DateTime } from 'luxon';
import Swal from "sweetalert2";

import PrivateChannel, { IPrivateChannelHandle } from '@admin/components/echo/channels/PrivateChannel';
import Event from '@admin/components/echo/events/Event';

import { createAuthRequest } from '@admin/utils/api/factories';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '../../Loader';
import createErrorHandler from '@admin/utils/errors/factory';
import JobStatus from './JobStatus';
import AwaitJob from '@admin/components/echo/wrappers/AwaitJob';

export type TBackupTypes = 'full' | 'database' | 'files';
export type TJobStatuses = 'pending' | 'started' | 'finished';

export interface IPerformBackupModalProps {
    type: TBackupTypes;
    onClose: () => void;
}

export interface IPerformBackupResponse {
    uuid: string;
}

export interface IJobData {
    dateTime: string;
    id: string;
    type: string;
}

export interface IProcessBeginData {
    dateTime: string;
    uuid: string;
}

export interface IProcessCompleteData {
    dateTime: string;
    uuid: string;
}

export interface IProcessOutputData {
    dateTime: string;
    uuid: string;
    message: string;
    newline: boolean;
}

const PerformBackupModal: React.FC<IPerformBackupModalProps> = ({ type, onClose }) => {
    const processChannelRef = React.useRef<IPrivateChannelHandle>(null);
    const xtermRef = React.useRef<XTerm>(null);

    const [jobStarted, setJobStarted] = React.useState<DateTime>();
    const [jobFinished, setJobFinished] = React.useState<DateTime>();
    const [processStarted, setProcessStarted] = React.useState<DateTime>();
    const [processCompleted, setProcessCompleted] = React.useState<DateTime>();
    const [canClose, setCanClose] = React.useState(false);

    React.useEffect(() => {
        setCanClose(jobFinished !== undefined);
    }, [jobFinished]);

    const perform = React.useCallback(async () => {
        const response = await createAuthRequest().post<IPerformBackupResponse>('backups/perform', {
            only: type !== 'full' ? type : undefined
        });

        return response.data;
    }, [type]);

    const handleCloseClicked = React.useCallback(async (e: React.MouseEvent) => {
        e.preventDefault();

        if (canClose) {
            onClose();
        } else {
            const result = await withReactContent(Swal).fire({
                icon: 'question',
                title: 'Are You Sure?',
                text: 'When closed, the backup results will be lost.',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                onClose();
        }

    }, [canClose, onClose]);

    const handleJobStarted = React.useCallback((data: IJobData) => setJobStarted(DateTime.fromISO(data.dateTime)), []);
    const handleJobFinished = React.useCallback((data: IJobData) => setJobFinished(DateTime.fromISO(data.dateTime)), []);

    const handleProcessStartEvent = React.useCallback((data: IProcessBeginData, event: string) => {
        setProcessStarted(DateTime.fromISO(data.dateTime));
    }, []);

    const handleProcessCompleteEvent = React.useCallback((data: IProcessCompleteData, event: string) => {
        setProcessCompleted(DateTime.fromISO(data.dateTime));
    }, []);

    const handleProcessOutputEvent = React.useCallback((data: IProcessOutputData, event: string) => {
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
    }, []);

    const isConnected = React.useMemo(() => {
        return window.EchoWrapper?.connectionState === 'connected' ? true : false;
    }, [window.EchoWrapper]);

    return (
        <>
            <Modal isOpen={true} backdrop='static' size='xl'>
                <ModalHeader>
                    Perform Backup
                </ModalHeader>
                <ModalBody>
                    <JobStatus connected={isConnected} started={jobStarted} finished={jobFinished} />

                    <WaitToLoad callback={perform} loading={<Loader display={{ type: 'over-element' }} />}>
                        {(response, err) => (
                            <>
                                {response && (
                                    <>
                                        <AwaitJob<IJobData>
                                            uuid={response.uuid}
                                            onJobStarted={handleJobStarted}
                                            onJobFinished={handleJobFinished}
                                        />

                                        <PrivateChannel ref={processChannelRef} channel={`processes.${response.uuid}`}>
                                            <Event event='.ProcessBegin' callback={handleProcessStartEvent} />
                                            <Event event='.ProcessComplete' callback={handleProcessCompleteEvent} />
                                            <Event event='.ProcessOutput' callback={handleProcessOutputEvent} />
                                        </PrivateChannel>
                                    </>
                                )}
                                {err && (
                                    <Alert color="danger" className="d-flex justify-content-between">
                                        {`Error: ${createErrorHandler().handle(err)}`}
                                    </Alert>
                                )}
                            </>
                        )}
                    </WaitToLoad>

                    {processStarted && <XTerm ref={xtermRef} />}


                </ModalBody>
                <ModalFooter>
                    <Button color={canClose ? 'primary' : 'secondary'} onClick={handleCloseClicked}>
                        Close
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default PerformBackupModal;
