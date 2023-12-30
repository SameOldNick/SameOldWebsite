import React from 'react';
import { Helmet } from 'react-helmet';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import withRouter, { IHasRouter } from '@admin/components/hoc/WithRouter';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import EditArticleWrapper from '@admin/components/blog/EditArticleWrapper';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import Article from '@admin/utils/api/models/Article';

interface IProps extends IHasRouter<'article' | 'revision'> {

}

const Edit = withRouter(({ router }: IProps) => {
    const waitToLoadArticleRef = React.createRef<WaitToLoad<Article>>();

    const [renderCount, setRenderCount] = React.useState(0);

    const loadArticle = async () => {
        const { params: { article } } = router;

        const response = await createAuthRequest().get<IArticle>(`blog/articles/${article}`);

        return new Article(response.data);
    }

    const handleLoadArticleError = async (err: unknown) => {
        const { navigate } = router;

        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred loading article: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            waitToLoadArticleRef.current?.load();
        } else {
            navigate(-1);
        }
    }

    React.useEffect(() => {
        setRenderCount(renderCount + 1);

        waitToLoadArticleRef.current?.load();
    }, [router.location.pathname]);

    return (
        <>
            <Helmet>
                <title>Edit Post</title>
            </Helmet>

            <WaitToLoad
                ref={waitToLoadArticleRef}
                loading={<Loader display={{ type: 'over-element' }} />}
                callback={loadArticle}
            >
                {(article, err) => (
                    <>
                        {err !== undefined && handleLoadArticleError(err)}
                        {article !== undefined && (
                            <EditArticleWrapper article={article} router={router} />
                        )}
                    </>
                )}
            </WaitToLoad>
        </>
    );
});

export default Edit;
