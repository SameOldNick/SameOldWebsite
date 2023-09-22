import React from 'react';
import { Helmet } from 'react-helmet';

import Heading from '@admin/layouts/admin/Heading';
import { Button, Card, CardBody, Col, ListGroup, Row } from 'reactstrap';
import { FaSync, FaTrash } from 'react-icons/fa';
import SkillPrompt from '@admin/components/homepage/skills/SkillPrompt';
import SkillList from '@admin/components/homepage/skills/SkillList';

interface IProps {

}

interface IState {
    addSkill: boolean;
}

export default class extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            addSkill: false
        };
    }

    public render() {
        const { } = this.props;
        const { addSkill } = this.state;

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
}
