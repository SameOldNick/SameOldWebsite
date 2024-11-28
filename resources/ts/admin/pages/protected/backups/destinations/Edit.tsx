import React from 'react';
import { Helmet } from 'react-helmet';
import { Alert as ReactstrapAlert, Button, Col, Container, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import Alert from '@admin/components/alerts/Alert';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';
import { createAuthRequest } from '@admin/utils/api/factories';
import Loader from '@admin/components/Loader';
import BackupDestinationForm, { BackupDestinationFormValues } from '@admin/components/backups/destinations/BackupDestinationForm';
import createErrorHandler from '@admin/utils/errors/factory';
import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';

interface IProps extends IHasRouter<'destination'> {

}

const Edit: React.FC<IProps> = ({ router }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);

    const [error, setError] = React.useState<string>();

    const fetchBackupDestination = React.useCallback(async () => {
        const response = await createAuthRequest().get<IBackupDestination>(`/backup/destinations/${router.params.destination}`);

        return response.data;
    }, []);

    const handeReloadClicked = React.useCallback(async () => {
        waitToLoadRef.current?.load();
    }, [waitToLoadRef]);

    const handleSubmit = React.useCallback(async (id: number, values: BackupDestinationFormValues) => {
        const response = await createAuthRequest().put(`/backup/destinations/${id}`, values);

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Destination Updated',
            text: 'The backup destination was successfully updated.'
        });

        router.navigate('/admin/backups/destinations');
    }, []);

    const handleError = React.useCallback(async (err: unknown) => {
        const message = createErrorHandler().handle(err);

        setError(message);
    }, []);

    return (
        <>
            <Helmet>
                <title>Edit Backup Destination</title>
            </Helmet>

            <Heading title='Edit Backup Destination' />

            <Container className='my-3'>
                <WaitToLoad ref={waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={fetchBackupDestination}>
                    {(destination, err) => (
                        <>
                            {err && (
                                <ReactstrapAlert color='danger' className='d-flex justify-content-between'>
                                    <span>An error occurred getting backup destination. Please try again.</span>
                                    <Button size='sm' color='primary' onClick={handeReloadClicked}>Reload</Button>
                                </ReactstrapAlert>
                            )}
                            {destination && (
                                <>
                                    <Row>
                                        <Col xs={12}>
                                            {error && <Alert alert={{ type: 'danger', message: error }} />}
                                        </Col>
                                    </Row>
                                    <BackupDestinationForm
                                        existing={{
                                            ...destination,
                                            password: null,
                                            confirm_password: null,
                                            private_key: null,
                                            passphrase: null
                                        }}
                                        onSubmit={(values) => handleSubmit(destination.id, values)}
                                        onError={handleError}
                                    />
                                </>
                            )}
                        </>
                    )}
                </WaitToLoad>
            </Container>
        </>
    );
}

export default requiresRolesForPage(withRouter(Edit), ['manage_backups']);

