import React from 'react';
import { Alert, Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';
import { ClipLoader } from 'react-spinners';
import { XTerm } from 'xterm-for-react';
import withReactContent from 'sweetalert2-react-content';

import { DateTime } from 'luxon';
import Swal from "sweetalert2";

import Events from '@admin/components/echo/events/Events';
import PrivateChannel from '@admin/components/echo/channels/PrivateChannel';
import Event from '@admin/components/echo/events/Event';

import { createAuthRequest } from '@admin/utils/api/factories';

export type TBackupTypes = 'full' | 'database' | 'files';
export type TJobStatuses = 'pending' | 'started' | 'finished';

export interface IPerformBackupModalProps {
    type: TBackupTypes;
    onClose: () => void;
}

export interface IPerformBackupResponse {
    uuid: string;
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
    const xtermRef = React.useRef<XTerm>(null);

    const [response, setResponse] = React.useState<IPerformBackupResponse>();
    const [jobStatus, setJobStatus] = React.useState<TJobStatuses>('pending');
    const [processStarted, setProcessStarted] = React.useState<DateTime>();
    const [processCompleted, setProcessCompleted] = React.useState<DateTime>();
    const [canClose, setCanClose] = React.useState(false);

    React.useEffect(() => {
        performBackup();
    }, [type]);

    React.useEffect(() => {
        setCanClose(jobStatus === 'finished');
    }, [jobStatus]);

    const performBackup = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().post<IPerformBackupResponse>('backups/perform', {
                only: type !== 'full' ? type : undefined
            });

            setResponse(response.data);
        } catch (err) {

        }
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

    }, [canClose]);

    const handleJobStatusEvent = React.useCallback((data: any, event: string) => {
        if (event === '.JobStarted')
            setJobStatus('started');
        else if (event === '.JobCompleted')
            setJobStatus('finished');
    }, []);

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

    const jobStatusDisplayText = React.useMemo(() => {
        switch (jobStatus) {
            case 'pending': {
                return 'Waiting for job to start...';
            }

            case 'started': {
                return 'Job started.';
            }

            case 'finished': {
                return 'Job finished.'
            }
        }
    }, [jobStatus]);

    return (
        <>
            <Modal isOpen={true} backdrop='static' size='xl'>
                <ModalHeader>
                    Perform Backup
                </ModalHeader>
                <ModalBody>


                    <Alert color="info" className="d-flex justify-content-center">
                        {jobStatus !== 'finished' && <ClipLoader size={25} className="me-1" />}
                        {jobStatusDisplayText}
                    </Alert>

                    {processStarted && <XTerm ref={xtermRef} />}

                    {response && (
                        <>
                            <PrivateChannel channel={`jobs.${response.uuid}`}>
                                <Events callback={handleJobStatusEvent} />
                            </PrivateChannel>

                            <PrivateChannel channel={`processes.${response.uuid}`}>
                                <Event event='.ProcessBegin' callback={handleProcessStartEvent} />
                                <Event event='.ProcessComplete' callback={handleProcessCompleteEvent} />
                                <Event event='.ProcessOutput' callback={handleProcessOutputEvent} />
                            </PrivateChannel>
                        </>
                    )}
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
