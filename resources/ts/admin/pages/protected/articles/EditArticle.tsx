import React from 'react';
import { Navigate } from 'react-router-dom';

import { withRouter, IHasRouter } from '@admin/components/hoc/withRouter';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import LoadArticleError from '@admin/components/blog/articles/containers/edit/LoadArticleError';

import { loadArticle } from '@admin/utils/api/endpoints/articles';

const EditArticle: React.FC<IHasRouter<'article'>> = ({ router: { params, navigate } }) => {
    const waitToLoadArticleRef = React.useRef<IWaitToLoadHandle>(null);

    const load = React.useCallback(async () => {
        const { article } = params;

        if (!article)
            throw new Error('The "article" parameter is missing.');

        const articleId = Number(article);

        if (isNaN(articleId))
            throw new Error('The "article" parameter must be a number.');

        return await loadArticle(articleId);
    }, [params]);

    const handleTryAgainClicked = React.useCallback(() => {
        waitToLoadArticleRef.current?.load();
    }, [waitToLoadArticleRef]);

    const handleGoBackClicked = React.useCallback(() => {
        navigate(-1);
    }, [navigate]);

    return (
        <>
            <WaitToLoad
                ref={waitToLoadArticleRef}
                loading={<Loader display={{ type: 'over-element' }} />}
                callback={load}
            >
                {(article, err) => (
                    <>
                        {article && <Navigate to={article.generatePath(article.article.current_revision?.uuid)} />}
                        {err && (
                            <LoadArticleError
                                error={err}
                                onTryAgainClicked={handleTryAgainClicked}
                                onGoBackClicked={handleGoBackClicked}
                            />
                        )}
                    </>
                )}
            </WaitToLoad>
        </>
    );
}

export default requiresRolesForPage(withRouter(EditArticle), ['write_posts']);
