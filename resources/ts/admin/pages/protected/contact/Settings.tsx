import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';

import ContactFormSettings from '@admin/components/contact/ContactFormSettings';
import withRouter, { IHasRouter } from '@admin/components/hoc/withRouter';

interface IProps extends IHasRouter {

}

const Settings: React.FC<IProps> = ({ router }) => {
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

export default withRouter(Settings);

