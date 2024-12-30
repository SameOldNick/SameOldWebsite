import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import SkillList from '@admin/components/homepage/skills/SkillList';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

const Skills: React.FC = () => {
    return (
        <>
            <Helmet>
                <title>Skills</title>
            </Helmet>

            <Heading title='Skills' />

            <Row className='justify-content-center mb-3'>
                <Col md={8}>

                    <Card>
                        <CardBody>
                            <SkillList />
                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default requiresRolesForPage(Skills, ['edit_profile']);
