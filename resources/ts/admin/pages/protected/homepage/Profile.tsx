import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import Avatar from '@admin/components/homepage/avatar';
import HomepageForm from '@admin/components/homepage/HomepageForm';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import SocialMediaLinks from '@admin/components/homepage/socialmedia/SocialMediaLinks';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';
import Authorized from '@admin/middleware/Authorized';

const Profile: React.FC<IHasRouter> = ({ router }) => {
    return (
        <>
            <Helmet>
                <title>Profile</title>
            </Helmet>


            <Heading title='Profile' />

            <Authorized roles={['change_avatar']}>
                <Row className='justify-content-center'>
                    <Col md={4}>
                        <Card className='mb-3'>
                            <CardBody>
                                <Avatar />
                            </CardBody>
                        </Card>

                    </Col>
                </Row>
            </Authorized>

            <Authorized roles={['edit_profile']}>
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
            </Authorized>
        </>
    );
}

export default requiresRolesForPage(withRouter(Profile), ['change_avatar', 'edit_profile'], { any: true });
