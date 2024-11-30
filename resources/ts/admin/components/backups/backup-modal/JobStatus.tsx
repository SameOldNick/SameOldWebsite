import { DateTime } from 'luxon';
import React from 'react';
import { ClipLoader } from 'react-spinners';
import { Alert } from 'reactstrap';

interface IJobStatusProps {
    connected: boolean;
    started?: DateTime;
    finished?: DateTime;
}

const JobStatus: React.FC<IJobStatusProps> = ({ connected, started, finished }) => {
    const [renderCount, setRenderCount] = React.useState(1);

    React.useEffect(() => {
        const timer = setInterval(() => setRenderCount((value) => value + 1), 750);

        return () => {
            clearInterval(timer);
        }
    }, []);

    const displayText = React.useMemo(() => {
        if (connected) {
            if (finished)
                return 'Job finished.';
            else if (started)
                return 'Job started.';
            else
                return 'Waiting for job to start...';
        } else {
            return 'Unable to perform backups because the WebSocket server is currently unreachable.';
        }
    }, [connected, started, finished]);

    return (
        <Alert color={connected ? 'info' : 'danger'} className="d-flex justify-content-between">
            <div className='d-flex align-items-center'>
                {connected && (
                    <span className="me-1">
                        {!started && !finished && <ClipLoader size={25} />}
                    </span>
                )}
                {displayText}
            </div>
            {connected && (
                <div key={renderCount}>
                    {started && !finished && started.toRelative()}
                    {finished && finished?.toLocaleString(DateTime.DATETIME_MED_WITH_SECONDS)}
                </div>
            )}
        </Alert>
    );
}

export default JobStatus;
