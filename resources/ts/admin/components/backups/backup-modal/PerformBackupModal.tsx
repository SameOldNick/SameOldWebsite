import React from 'react';
import { Alert, Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import { DateTime } from 'luxon';
import Swal from "sweetalert2";

import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import AwaitJob from '@admin/components/echo/wrappers/AwaitJob';

import JobStatus from './JobStatus';

import { createAuthRequest } from '@admin/utils/api/factories';
import createErrorHandler from '@admin/utils/errors/factory';
import ProcessOutputToXTerm from '@admin/components/echo/wrappers/ProcessOutputToXTerm';

type TBackupTypes = 'full' | 'database' | 'files';
type TJobStatuses = 'pending' | 'started' | 'finished';

interface IPerformBackupModalProps {
    type: TBackupTypes;
    onClose: () => void;
}

interface IPerformBackupResponse {
    uuid: string;
}

interface IJobData {
    dateTime: string;
    id: string;
    type: string;
}

const PerformBackupModal: React.FC<IPerformBackupModalProps> = ({ type, onClose }) => {
    const [jobStarted, setJobStarted] = React.useState<DateTime>();
    const [jobFinished, setJobFinished] = React.useState<DateTime>();
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

                                        <ProcessOutputToXTerm uuid={response.uuid} />
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
