import React from 'react';
import { Helmet } from 'react-helmet-async';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';

import BlacklistComponent from '@admin/components/contact/blacklist/Blacklist';
import { withRouter, IHasRouter } from '@admin/components/hoc/withRouter';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

const Blacklist: React.FC<IHasRouter> = ({ router }) => {
    return (
        <>
            <Helmet>
                <title>Manage Blacklist</title>
            </Helmet>

            <Heading title='Manage Blacklist' />

            <Row className='justify-content-center mb-3'>
                <Col md={8}>
                    <Card>
                        <CardBody>
                            <BlacklistComponent />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default requiresRolesForPage(withRouter(Blacklist), ['change_contact_settings']);

