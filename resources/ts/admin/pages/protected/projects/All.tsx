import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';

import ProjectList from '@admin/components/projects/ProjectList';

interface IProps {

}

interface IState {
}

export default class All extends React.Component<IProps, IState> {
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
                    <title>All Projects</title>
                </Helmet>

                <Heading title='All Projects' />

                <Card>
                    <CardBody>
                        <ProjectList />
                    </CardBody>
                </Card>
            </>
        );
    }
}
