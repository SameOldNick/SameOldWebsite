import React from 'react';
import { Tag } from 'react-tag-autocomplete';
import withReactContent from 'sweetalert2-react-content';

import axios from 'axios';
import Swal, { SweetAlertOptions } from 'sweetalert2';
import { DateTime } from 'luxon';

import { IMainImageExisting, IMainImageNew, TMainImage, isMainImageNew } from './article-form/main-image';
import EditForm, { IArticleValues } from './EditForm';
import Revision from '@admin/utils/api/models/Revision';
import Article from '@admin/utils/api/models/Article';

import { IHasRouter } from '@admin/components/hoc/withRouter';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { createRevision, deleteMainImage, loadTags, setCurrentRevision, updateArticle, attachTags, uploadMainImage, setMainImage as setMainImageApi, syncTags } from '@admin/utils/api/calls/articles';

interface IEditArticleWrapperProps extends IHasRouter<'article' | 'revision'> {
    article: Article;
}

const EditArticleWrapper: React.FC<IEditArticleWrapperProps> = ({ article, router }) => {
    const waitToLoadRevisionRef = React.createRef<WaitToLoad<Revision>>();

    const [tags, setTags] = React.useState<Tag[]>([]);
    const [mainImage, setMainImage] = React.useState<TMainImage | undefined>();
    const [dirty, setDirty] = React.useState({
        mainImage: false,
        tags: false
    });

    const loadMainImage = async ({ article }: Article): Promise<IMainImageExisting | undefined> => {
        if (article.main_image) {
            return {
                src: article.main_image.file.url || '',
                description: article.main_image.description,
            };
        } else {
            return undefined;
        }
    }

    const loadImagesAndTags = async () => {
        return tryApiCall(async () => {
            const mainImage = await loadMainImage(article);
            setMainImage(mainImage);

            const tags = await loadTags(article);
            setTags(tags);
        });
    }

    async function tryApiCall<TTryAgainReturn = void>(apiCall: () => Promise<TTryAgainReturn>): Promise<TTryAgainReturn> {
        try {
            return apiCall();
        } catch (err) {
            return handleApiError(err, () => tryApiCall(apiCall));
        }
    }

    async function handleApiError<TTryAgainReturn = void>(err: unknown, onTryAgain: () => Promise<TTryAgainReturn>) {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error ocurred: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            return onTryAgain();
        } else {
            throw err;
        }
    }

    const updateMainImage = async (article: Article, mainImage: TMainImage | null) => {
        return tryApiCall(async () => {
            if (mainImage && isMainImageNew(mainImage)) {
                const image = await uploadMainImage(article, mainImage);
                await setMainImageApi(article, image);
            }
            else // if (mainImage === null)
                return deleteMainImage(article);
        });
    }

    const displaySuccess = async (message: string, extra: SweetAlertOptions = {}) => {
        return withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            ...extra
        });
    }

    const handleSaveAsRevision = async ({ title, slug, content, summary, parentRevision }: IArticleValues) => {
        try {
            let newArticle = article;

            if (article.article.title !== title || article.article.slug !== slug) {
                //newArticle = await updateArticle(title, slug, article.article.published_at ? DateTime.fromISO(article.article.published_at) : null);
                newArticle = await tryApiCall(() => updateArticle(article, title, slug, article.article.published_at ? DateTime.fromISO(article.article.published_at) : null));
            }

            if (!newArticle.article.id)
                throw new Error(`ID is missing from article: ${JSON.stringify(article)}`);

            if (dirty.mainImage) {
                await updateMainImage(newArticle, mainImage || null);
            }

            if (dirty.tags) {
                await tryApiCall(() => syncTags(newArticle, tags));
            }

            const revision = await tryApiCall(() => createRevision(newArticle, content, summary, parentRevision));

            await displaySuccess('Revision was saved.');

            router.navigate(newArticle.generatePath(revision.revision.uuid));
        } catch (err) {
            // What happens if an error occurred and user didn't try again.
            console.error(err);
        }

    }

    const handleUpdate = async ({ title, slug, content, summary, parentRevision }: IArticleValues) => {
        try {
            let newArticle = article;

            if (article.article.title !== title || article.article.slug !== slug) {
                newArticle = await updateArticle(article, title, slug, article.article.published_at ? DateTime.fromISO(article.article.published_at) : null);
            }

            if (!newArticle.article.id)
                throw new Error(`ID is missing from article: ${JSON.stringify(article)}`);

            if (dirty.mainImage) {
                await updateMainImage(newArticle, mainImage || null);
            }

            if (dirty.tags) {
                await syncTags(newArticle, tags);
            }

            const revision = await createRevision(newArticle, content, summary, parentRevision);

            await tryApiCall(() => setCurrentRevision(newArticle, revision));

            await displaySuccess('Article was updated.');

            router.navigate(newArticle.generatePath(revision.revision.uuid));
        } catch (err) {
            // What happens if an error occurred and user didn't try again.
            console.error(err);
        }
    }

    // This is for both unpublishing and unscheduling
    const handleUnpublish = async ({ title, slug, content, summary, parentRevision }: IArticleValues) => {
        try {
            if (!article.article.id)
                throw new Error(`ID is missing from article: ${JSON.stringify(article)}`);

            const newArticle = await updateArticle(article, title, slug, null);

            if (dirty.mainImage) {
                await updateMainImage(newArticle, mainImage || null);
            }

            if (dirty.tags) {
                await syncTags(newArticle, tags);
            }

            const revision = await createRevision(newArticle, content, summary, parentRevision);

            await setCurrentRevision(newArticle, revision);

            await displaySuccess('Article is no longer published.');

            router.navigate(newArticle.generatePath(revision.revision.uuid));
        } catch (err) {
            // What happens if an error occurred and user didn't try again.
            console.error(err);
        }
    }

    const handlePublish = async ({ title, slug, content, summary, parentRevision }: IArticleValues, dateTime: DateTime) => {
        try {
            if (!article.article.id)
                throw new Error(`ID is missing from article: ${JSON.stringify(article)}`);

            const newArticle = await updateArticle(article, title, slug, dateTime);

            if (!newArticle.article.id)
                throw new Error(`ID is missing from article: ${JSON.stringify(article)}`);

            if (dirty.mainImage) {
                await updateMainImage(newArticle, mainImage || null);
            }

            if (dirty.tags) {
                await syncTags(newArticle, tags);
            }

            const revision = await createRevision(newArticle, content, summary, parentRevision);

            await setCurrentRevision(newArticle, revision);

            await displaySuccess(Math.abs(dateTime.diffNow().toMillis()) < 60 * 1000 ? 'The article has been published.' : 'The article has been scheduled.');

            router.navigate(newArticle.generatePath(revision.revision.uuid));
        } catch (err) {
            // What happens if an error occurred and user didn't try again.
            console.error(err);
        }
    }

    const handleDelete = async ({ }: IArticleValues) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `You will be able to restore the article.`,
            showConfirmButton: true,
            confirmButtonColor: 'red',
            showCancelButton: true
        });

        if (result.isConfirmed) {
            const response = await createAuthRequest().delete<Record<'success', string>>(`blog/articles/${article.article.id}`);

            await displaySuccess(response.data.success);

            router.navigate('/admin/posts');
        }
    }

    const handleTagsChanged = (tags: Tag[]) => {
        setTags(tags);
        setDirty((prev) => ({
            ...prev,
            tags: true
        }));
    }

    const handleMainImageChanged = (image: TMainImage | undefined) => {
        setMainImage(image);
        setDirty((prev) => ({
            ...prev,
            mainImage: true
        }));
    }

    const loadRevision = async () => {
        const response = await createAuthRequest().get<IRevision>(`blog/articles/${article.article.id}/revisions/${router.params.revision}`);

        return new Revision(response.data);
    }

    React.useEffect(() => {
        loadImagesAndTags();
    }, [article]);

    React.useEffect(() => {
        waitToLoadRevisionRef.current?.load();
    }, [router.params.revision]);

    return (
        <>

            <WaitToLoad
                ref={waitToLoadRevisionRef}
                callback={loadRevision}
                loading={<Loader display={{ type: 'over-element' }} />}
            >
                {(revision, err) => (
                    <>
                        {err && console.error(err)}
                        {revision && (
                            <EditForm
                                router={router}
                                article={article}
                                revision={revision}
                                tags={tags}
                                mainImage={mainImage}
                                setTags={handleTagsChanged}
                                setMainImage={handleMainImageChanged}
                                onUpdate={handleUpdate}
                                onSaveAsRevision={handleSaveAsRevision}
                                onPublish={handlePublish}
                                onUnpublish={handleUnpublish}
                                onUnschedule={handleUnpublish}
                                onDelete={handleDelete}
                            />
                        )}
                    </>
                )}
            </WaitToLoad>

        </>
    );
}

export default EditArticleWrapper;
