import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Container, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import BackupFormSettings from '@admin/components/backups/BackupFormSettings';

import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';
import BackupDestinations from '@admin/components/backups/destinations/BackupDestinations';

interface IProps extends IHasRouter {

}

const Destinations: React.FC<IProps> = ({ router }) => {
    return (
        <>
            <Helmet>
                <title>Backup Destinations</title>
            </Helmet>

            <Heading title='Backup Destinations' />

            <Container className='my-3'>
                <BackupDestinations router={router} />
            </Container>
        </>
    );
}

export default requiresRolesForPage(withRouter(Destinations), ['manage_backups']);

