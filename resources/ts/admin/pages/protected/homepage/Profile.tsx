import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import Avatar from '@admin/components/homepage/avatar';
import HomepageForm from '@admin/components/homepage/HomepageForm';
import withRouter, { IHasRouter } from '@admin/components/hoc/withRouter';
import SocialMediaLinks from '@admin/components/homepage/socialmedia/SocialMediaLinks';

interface IProps extends IHasRouter {

}

interface IState {
}

export default withRouter(class extends React.Component<IProps, IState> {

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };
    }

    public render() {
        const { router } = this.props;
        const { } = this.state;

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
});
