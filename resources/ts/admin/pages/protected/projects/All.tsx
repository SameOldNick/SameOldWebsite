import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';

import ProjectList from '@admin/components/projects/ProjectList';

interface IProps {

}

const All: React.FC<IProps> = ({ }) => {
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

export default All;
