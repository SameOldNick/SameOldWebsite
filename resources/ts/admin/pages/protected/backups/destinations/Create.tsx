import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Container, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import BackupFormSettings from '@admin/components/backups/BackupFormSettings';

import { withRouter, IHasRouter } from '@admin/components/hoc/withRouter';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';
import BackupDestinations from '@admin/components/backups/destinations/BackupDestinations';
import BackupDestinationForm, { BackupDestinationFormValues } from '@admin/components/backups/destinations/BackupDestinationForm';
import createErrorHandler from '@admin/utils/errors/factory';
import Alert from '@admin/components/alerts/Alert';
import { createAuthRequest } from '@admin/utils/api/factories';
import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';
import { excludeFromObject } from '@admin/utils';

interface IProps extends IHasRouter {

}

const Create: React.FC<IProps> = ({ router }) => {
    const [error, setError] = React.useState<string>();
    const handleSubmit = React.useCallback(async (values: BackupDestinationFormValues) => {
        const response = await createAuthRequest().post('/backup/destinations', excludeFromObject(values, ['confirm_password']));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Destination Created',
            text: 'The backup destination was successfully created.'
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
                <title>Create Backup Destination</title>
            </Helmet>

            <Heading title='Create Backup Destination' />

            <Container className='my-3'>
                <Row>
                    <Col xs={12}>
                        <Card>

                            <CardBody>
                                {error && <Alert alert={{ type: 'danger', message: error }} />}
                                <BackupDestinationForm onSubmit={handleSubmit} onError={handleError} />
                            </CardBody>
                        </Card>

                    </Col>
                </Row>
            </Container>
        </>
    );
}

export default requiresRolesForPage(withRouter(Create), ['manage_backups']);

