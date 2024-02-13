import React from 'react';
import { Navigate } from 'react-router-dom';

import WithArticle from '../../../components/blog/WithArticle';
import { withRouter, IHasRouter } from '@admin/components/hoc/WithRouter';

interface IProps extends IHasRouter<'article'> {

}

const CurrentRevision = withRouter(({ router }: IProps) => {
    if (!router.params.article)
        return;

    return (
        <>
            <WithArticle articleId={Number(router.params.article)}>
                {(article, err) => (
                    <>
                        {article && <Navigate to={`revisions/${article.article.current_revision?.uuid}`} />}
                        {err && console.error(err)}
                    </>
                )}
            </WithArticle>
        </>
    )
});

export default CurrentRevision;
