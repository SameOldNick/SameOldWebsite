import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import NotificationList from '@admin/components/notifications/NotificationList';

const Notifications: React.FC = () => {
    return (
        <>
            <Helmet>
                <title>Notifications</title>
            </Helmet>

            <Heading title='Notifications' />

            <Row className='justify-content-center mb-3'>
                <Col md={8}>

                    <Card>
                        <CardBody>
                            <NotificationList />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default Notifications;
