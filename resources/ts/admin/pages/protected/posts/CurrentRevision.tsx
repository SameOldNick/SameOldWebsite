import React from 'react';
import { Navigate } from 'react-router-dom';

import WithArticle from '@admin/components/blog/WithArticle';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

interface IProps extends IHasRouter<'article'> {

}

const CurrentRevision: React.FC<IProps> = ({ router }) => {
    if (!router.params.article)
        return;

    return (
        <>
            <WithArticle articleId={Number(router.params.article)}>
                {(article, err) => (
                    <>
                        {/*article && <Navigate to={`revisions/${article.article.current_revision?.uuid}`} />*/}
                        {article && <Navigate to={article.generatePath(article.article.current_revision?.uuid)} />}
                        {err && logger.error(err)}
                    </>
                )}
            </WithArticle>
        </>
    )
}

export default requiresRolesForPage(withRouter(CurrentRevision), ['write_posts']);
