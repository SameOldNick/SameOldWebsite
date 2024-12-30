import React from 'react';
import { Helmet } from 'react-helmet-async';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import CommentList from '@admin/components/blog/comments/CommentList';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';


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
