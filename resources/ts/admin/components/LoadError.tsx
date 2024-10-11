import React from 'react';
import { Alert, Button } from 'reactstrap';

import createErrorHandler from '@admin/utils/errors/factory';

interface LoadErrorProps {
    error: unknown;
    onTryAgainClicked: () => void;
    onGoBackClicked: () => void;
}

const LoadError: React.FC<LoadErrorProps> = ({ error, onTryAgainClicked, onGoBackClicked }) => {
    const errorMessage = React.useMemo(() => {
        const message = createErrorHandler().handle(error);

        return `An error occurred: ${message}`;
    }, [error]);

    return (
        <Alert color='danger' className='text-center'>
            {errorMessage}

            <div className="mt-3">
                <Button color='primary' className='me-2' onClick={onTryAgainClicked}>
                    Try Again
                </Button>

                <Button color='secondary' onClick={onGoBackClicked}>
                    Go Back
                </Button>
            </div>
        </Alert>
    );
}

export default LoadError;
export { LoadErrorProps };
