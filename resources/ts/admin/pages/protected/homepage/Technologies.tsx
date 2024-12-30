import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import TechnologyList from '@admin/components/homepage/technologies/TechnologyList';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

const Technologies: React.FC = () => {
    return (
        <>
            <Helmet>
                <title>Technologies</title>
            </Helmet>

            <Heading title='Technologies' />

            <Row className='justify-content-center mb-3'>
                <Col md={8}>
                    <Card>
                        <CardBody>
                            <TechnologyList />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default requiresRolesForPage(Technologies, ['edit_profile']);
