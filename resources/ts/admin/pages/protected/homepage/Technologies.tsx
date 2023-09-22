import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import TechnologyList from '@admin/components/homepage/technologies/TechnologyList';

interface IProps {

}

const Technologies: React.FC<IProps> = ({ }) => {
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

export default Technologies;
