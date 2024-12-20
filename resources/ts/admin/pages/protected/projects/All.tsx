import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';

import ProjectList from '@admin/components/projects/ProjectList';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

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

export default requiresRolesForPage(All, ['manage_projects']);
