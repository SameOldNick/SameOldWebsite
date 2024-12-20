import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import ArticleList from '@admin/components/blog/article-list/ArticleList';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

interface IProps {

}

const All: React.FC<IProps> = ({ }) => {
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
