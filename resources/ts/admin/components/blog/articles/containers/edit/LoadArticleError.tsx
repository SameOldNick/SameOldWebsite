import React from 'react';
import { Alert, Button } from 'reactstrap';

import createErrorHandler from '@admin/utils/errors/factory';

interface LoadArticleErrorProps {
    error: unknown;
    onTryAgainClicked: () => void;
    onGoBackClicked: () => void;
}

const LoadArticleError: React.FC<LoadArticleErrorProps> = ({ error, onTryAgainClicked, onGoBackClicked }) => {
    const errorMessage = React.useMemo(() => {
        const message = createErrorHandler().handle(error);

        return `An error occurred getting the article: ${message}`;
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

export default LoadArticleError;
export { LoadArticleErrorProps };
