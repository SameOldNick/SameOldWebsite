import React from 'react';
import { Helmet } from 'react-helmet';

import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

import CreateArticleContainer from '@admin/components/blog/articles/containers/create/CreateArticleContainer';

const Create: React.FC = () => {
    return (
        <>
            <Helmet>
                <title>Create Post</title>
            </Helmet>

            <CreateArticleContainer />
        </>
    );
}

export default requiresRolesForPage(Create, ['write_posts']);
