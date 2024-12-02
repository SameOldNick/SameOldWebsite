import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';

import AwaitJob from '@admin/components/echo/wrappers/AwaitJob';

interface IProps {
    uuids: string[];
    onFinished: (uuid: string) => void;
}

const BackupDestinationTestAwaits: React.FC<IProps> = ({ uuids, onFinished }) => {
    const handleStarted = React.useCallback(async (uuid: string) => {
        await withReactContent(Swal).fire({
            icon: 'info',
            title: 'Test Started',
            text: 'The filesystem testing was started. You will be notified when it finishes.'
        });
    }, []);

    const handleFinished = React.useCallback(async (uuid: string) => {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Test Completed',
            text: 'The testing completed successfully. The filesystem appears to be working.'
        });

        onFinished(uuid);
    }, []);

    const handleFailed = React.useCallback(async (uuid: string) => {
        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Test Failed',
            text: 'There was an error trying to communicate with the filesystem.'
        });

        onFinished(uuid);
    }, [onFinished]);

    return (
        <>
            {uuids.map((uuid, i) => (
                <AwaitJob
                    key={i}
                    uuid={uuid}
                    onJobStarted={() => handleStarted(uuid)}
                    onJobFinished={() => handleFinished(uuid)}
                    onJobFailed={() => handleFailed(uuid)}
                />
            ))}
        </>
    );
}

export default BackupDestinationTestAwaits;
