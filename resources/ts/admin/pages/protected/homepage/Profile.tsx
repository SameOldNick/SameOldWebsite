import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import Avatar from '@admin/components/homepage/avatar';
import HomepageForm from '@admin/components/homepage/HomepageForm';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import SocialMediaLinks from '@admin/components/homepage/socialmedia/SocialMediaLinks';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

interface IProps extends IHasRouter {

}

const Profile: React.FC<IProps> = ({ router }) => {
    return (
        <>
            <Helmet>
                <title>Profile</title>
            </Helmet>


            <Heading title='Profile' />

            <Row className='justify-content-center'>
                <Col md={4}>
                    <Card className='mb-3'>
                        <CardBody>
                            <Avatar />
                        </CardBody>
                    </Card>

                </Col>
            </Row>

            <Row className='justify-content-center mb-3'>
                <Col md={8}>
                    <Card>
                        <CardBody>
                            <HomepageForm router={router} />
                        </CardBody>
                    </Card>
                </Col>
            </Row>

            <Row className='justify-content-center'>
                <Col md={8}>
                    <Card>
                        <CardBody>
                            <SocialMediaLinks />

                        </CardBody>
                    </Card>
                </Col>
            </Row>
        </>
    );
}

export default requiresRolesForPage(withRouter(Profile), ['edit_profile']);
