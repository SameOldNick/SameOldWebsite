import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody, Col, Row } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import TechnologyList from '@admin/components/homepage/technologies/TechnologyList';

interface IProps {

}

interface IState {
}

export default class extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };
    }

    public render() {
        const { } = this.props;
        const { } = this.state;

        return (
            <>
                <Helmet>
                    <title>Technologies</title>
                </Helmet>

                <Heading>
                    <Heading.Title>Technologies</Heading.Title>
                </Heading>

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
}
