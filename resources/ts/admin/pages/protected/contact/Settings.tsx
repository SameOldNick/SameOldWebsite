import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';

import ContactFormSettings from '@admin/components/contact/ContactFormSettings';
import { withRouter, IHasRouter } from '@admin/components/hoc/withRouter';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

const Settings: React.FC<IHasRouter> = ({ router }) => {
    return (
        <>
            <Helmet>
                <title>Contact Settings</title>
            </Helmet>

            <Heading title='Contact Settings' />

            <Row className='justify-content-center mb-3'>
                <Col md={8}>
                    <Card>
                        <CardBody>
                            <ContactFormSettings router={router} />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default requiresRolesForPage(withRouter(Settings), ['change_contact_settings']);

