import { MouseEvent, useCallback, useMemo } from 'react';
import { Alert, Button, Col, Container, Row } from 'reactstrap';
import { IconContext } from 'react-icons';
import { FaArrowLeft, FaHome, FaRedo } from 'react-icons/fa';

import buildUrl from 'build-url-ts';

import { FallbackErrorProps } from '@admin/components/wrappers/ErrorBoundary';

const UnknownError = ({ error, resetErrorBoundary }: FallbackErrorProps) => {
    const goBack = useCallback((e: MouseEvent) => {
        e.preventDefault();
        window.history.back();
    }, []);

    const tryAgain = useCallback((e: MouseEvent) => {
        e.preventDefault();

        if (resetErrorBoundary) {
            resetErrorBoundary();
        }
    }, [resetErrorBoundary]);

    const goHome = useCallback((e: MouseEvent) => {
        e.preventDefault();
        window.location.href = '/admin';
    }, []);

    const message = useMemo<string | undefined>(() => {
        if (!error) {
            return undefined;
        }

        if (error instanceof Error) {
            return error.message;
        }

        return typeof error === 'object' ? JSON.stringify(error) : String(error);
    }, [error]);

    const stack = useMemo<string | undefined>(() => error instanceof Error ? error.stack : undefined, [error]);

    const reportBugUrl = useMemo(() => buildUrl('https://github.com/SameOldNick/SameOldWebsite/issues/new', {
        queryParams: {
            title: 'Bug Report',
            body: `### Error Details\n\n${message || 'No error message'}\n\n### Stack Trace\n\n${stack || 'No stack trace'}`
        }
    }), [message, stack]);

    return (
        <IconContext.Provider value={{ className: 'react-icons' }}>
            <Container className="text-center py-5">
                <Row>
                    <Col>
                        <h1 className="display-3 text-danger">Oops!</h1>
                        <h3>Something went wrong</h3>
                        <p className="text-muted">
                            We encountered an unexpected error. Please try again or report the issue.
                        </p>

                        {(message || stack) && (
                            <Alert color="danger" className="mt-4" style={{ overflowY: 'auto', maxHeight: '350px' }}>
                                <h5>Error Details</h5>
                                {message && <pre className="mb-0 text-left">{message}</pre>}
                                {stack && (
                                    <pre className="text-left mt-2 small">{stack}</pre>
                                )}
                            </Alert>
                        )}

                        <div className="mt-4">
                            {resetErrorBoundary && (
                                <Button
                                    color="warning"
                                    className="me-2"
                                    onClick={tryAgain}
                                >
                                    <FaRedo className="me-1" />
                                    Reset
                                </Button>
                            )}
                            <Button color="secondary" className="me-2" onClick={goBack}>
                                <FaArrowLeft className="me-1" />
                                Go Back
                            </Button>
                            <Button color="primary" className="me-2" onClick={goHome}>
                                <FaHome className="me-1" />
                                Go Home
                            </Button>

                        </div>

                        <div className="mt-4">
                            <p>
                                If the issue persists, please{' '}
                                <a
                                    href={reportBugUrl}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    report it on GitHub
                                </a>.
                            </p>
                        </div>
                    </Col>
                </Row>
            </Container>
        </IconContext.Provider>
    );
};

export default UnknownError;
