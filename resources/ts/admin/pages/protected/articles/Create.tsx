import React from 'react';
import { Helmet } from 'react-helmet';

import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

import CreateArticleContainer from '@admin/components/blog/articles/containers/create/CreateArticleContainer';

interface IProps {

}

const Create: React.FC<IProps> = ({ }) => {
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
