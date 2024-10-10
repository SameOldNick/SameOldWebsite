import React from 'react';
import { Helmet } from 'react-helmet';

import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';
import { IHasRouter, withRouter } from '@admin/components/hoc/WithRouter';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import EditArticleContainer from '@admin/components/blog/articles/containers/edit/EditArticleContainer';
import LoadArticleError from '@admin/components/blog/articles/containers/edit/LoadArticleError';

import { loadArticle, loadRevision, loadTags } from '@admin/utils/api/endpoints/articles';

interface IProps extends IHasRouter<'article' | 'revision'> {

}

const EditRevision: React.FC<IProps> = ({ router: { navigate, params, location } }) => {
    const waitToLoadArticleRef = React.createRef<IWaitToLoadHandle>();

    const load = React.useCallback(async () => {
        const { article, revision } = params;

        if (!article)
            throw new Error('The "article" parameter is missing.');

        const articleId = Number(article);

        if (isNaN(articleId))
            throw new Error('The "article" parameter must be a number.');

        if (!revision)
            throw new Error('The "revision" parameter is missing.');

        return {
            article: await loadArticle(articleId),
            revision: await loadRevision(articleId, revision),
            tags: await loadTags(articleId),
        };
    }, [params]);

    const handleTryAgainClicked = React.useCallback(() => {
        waitToLoadArticleRef.current?.load();
    }, [waitToLoadArticleRef]);

    const handleGoBackClicked = React.useCallback(() => {
        navigate(-1);
    }, [navigate]);

    React.useEffect(() => {
        waitToLoadArticleRef.current?.load();
    }, [location.pathname, waitToLoadArticleRef.current]);

    return (
        <>
            <Helmet>
                <title>Edit Post</title>
            </Helmet>

            <>
                <WaitToLoad
                    ref={waitToLoadArticleRef}
                    loading={<Loader display={{ type: 'over-element' }} />}
                    callback={load}
                >
                    {(result, err) => (
                        <>
                            {err && (
                                <LoadArticleError
                                    error={err}
                                    onTryAgainClicked={handleTryAgainClicked}
                                    onGoBackClicked={handleGoBackClicked}
                                />
                            )}
                            {result && (
                                <EditArticleContainer
                                    article={result.article}
                                    revision={result.revision}
                                    tags={result.tags}
                                />
                            )}
                        </>

                    )}
                </WaitToLoad>

            </>
        </>
    );
}

export default requiresRolesForPage(withRouter(EditRevision), ['write_posts']);
