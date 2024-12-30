import React from 'react';
import { Helmet } from 'react-helmet-async';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import ArticleList from '@admin/components/blog/article-list/ArticleList';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';


const All: React.FC = () => {
    return (
        <>
            <Helmet>
                <title>All Posts</title>
            </Helmet>

            <Heading title='All Posts' />

            <Card>
                <CardBody>
                    <ArticleList />
                </CardBody>
            </Card>
        </>
    );
}

export default requiresRolesForPage(All, ['write_posts']);
