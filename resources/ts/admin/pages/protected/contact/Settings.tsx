import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';

import ContactFormSettings from '@admin/components/contact/ContactFormSettings';
import withRouter, { IHasRouter } from '@admin/components/hoc/WithRouter';

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
                    <title>Contact Settings</title>
                </Helmet>

                <Heading>
                    <Heading.Title>Contact Settings</Heading.Title>
                </Heading>

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
});
