import React from 'react';
import { Helmet } from 'react-helmet-async';
import { Tag } from 'react-tag-autocomplete';

import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';
import { IHasRouter, withRouter } from '@admin/components/hoc/withRouter';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import EditArticleContainer from '@admin/components/blog/articles/containers/edit/EditArticleContainer';
import LoadArticleError from '@admin/components/blog/articles/containers/edit/LoadArticleError';

import { loadArticle, loadRevision } from '@admin/utils/api/endpoints/articles';
import { loadTags } from '@admin/utils/api/endpoints/article-tags';
import Revision from '@admin/utils/api/models/Revision';
import Article from '@admin/utils/api/models/Article';

interface CurrentArticle {
    article: Article;
    revision: Revision;
    tags: Tag[];
}

const EditRevision: React.FC<IHasRouter<'article' | 'revision'>> = ({ router: { navigate, params, location } }) => {
    const waitToLoadArticleRef = React.useRef<IWaitToLoadHandle>(null);

    const [currentArticle, setCurrentArticle] = React.useState<CurrentArticle>();

    const load = React.useCallback(async () => {
        const { article, revision } = params;

        if (!article)
            throw new Error('The "article" parameter is missing.');

        const articleId = Number(article);

        if (isNaN(articleId))
            throw new Error('The "article" parameter must be a number.');

        if (!revision)
            throw new Error('The "revision" parameter is missing.');

        const current = {
            article: await loadArticle(articleId),
            revision: await loadRevision(articleId, revision),
            tags: await loadTags(articleId),
        };

        setCurrentArticle(current);

        return current;
    }, [params]);

    const handleTryAgainClicked = React.useCallback(() => {
        waitToLoadArticleRef.current?.load();
    }, [waitToLoadArticleRef]);

    const handleGoBackClicked = React.useCallback(() => {
        navigate(-1);
    }, [navigate]);

    React.useEffect(() => {
        // Prevents load being called twice when first rendered
        if (currentArticle !== undefined && (
            currentArticle.article.article.id !== Number(params.article) ||
            currentArticle.revision.revision.uuid !== params.revision)
        ) {
            waitToLoadArticleRef.current?.load();
        }
    }, [location.pathname]);

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
