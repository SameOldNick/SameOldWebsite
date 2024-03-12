import React from 'react';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';
import Article from '@admin/utils/api/models/Article';

interface WithArticleChildrenFunc {
    (article: Article, err: undefined): React.ReactNode;
    (article: undefined, err: unknown): React.ReactNode;
}

interface IProps {
    articleId: number;
    children: WithArticleChildrenFunc;
}

const WithArticle: React.FC<IProps> = ({ articleId, children }) => {
    const waitToLoadArticleRef = React.createRef<IWaitToLoadHandle>;

    const loadArticle = async () => {
        const response = await createAuthRequest().get<IArticle>(`blog/articles/${articleId}`);

        return new Article(response.data);
    }

    return (
        <>
            <WaitToLoad
                ref={waitToLoadArticleRef}
                loading={<Loader display={{ type: 'over-element' }} />}
                callback={loadArticle}
            >
                {(article, err) => (
                    <>
                        {article && children(article, undefined)}
                        {err && children(undefined, err)}
                    </>
                )}
            </WaitToLoad>
        </>
    );
};

export default WithArticle;
