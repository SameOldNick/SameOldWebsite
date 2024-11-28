import React from 'react';
import Helmet from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import BackupFormSettings from '@admin/components/backups/BackupFormSettings';

import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

interface IProps extends IHasRouter {

}

const Settings: React.FC<IProps> = ({ router }) => {
    return (
        <>
            <Helmet>
                <title>Backup Settings</title>
            </Helmet>

            <Heading title='Backup Settings' />

            <Row className='justify-content-center mb-3'>
                <Col md={8}>
                    <Card>
                        <CardBody>
                            <BackupFormSettings router={router} />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default requiresRolesForPage(withRouter(Settings), ['manage_backups']);

