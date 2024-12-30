import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import CommentList from '@admin/components/blog/comments/CommentList';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';


const All: React.FC = () => {
    return (
        <>
            <Helmet>
                <title>All Comments</title>
            </Helmet>

            <Heading title='All Comments' />

            <Card>
                <CardBody>
                    <CommentList />
                </CardBody>
            </Card>
        </>
    );
}

export default requiresRolesForPage(All, ['manage_comments']);
