import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import axios from 'axios';
import Swal, { SweetAlertOptions } from 'sweetalert2';

import EditForm, { IArticleActionInputs, IEditFormProps, TArticleActionDirtyValues, TArticleActions } from './EditForm';
import Revision from '@admin/utils/api/models/Revision';
import Article from '@admin/utils/api/models/Article';

import { IHasRouter } from '@admin/components/hoc/WithRouter';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import ArticleInfoModal from './ArticleInfoModal';
import SelectRevisionModal from './article-form/SelectRevisionModal';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { createRevision, loadTags, setCurrentRevision, updateArticle, syncTags, setMainImage, unsetMainImage } from '@admin/utils/api/endpoints/articles';
import awaitModalPrompt from '@admin/utils/modals';

interface IEditArticleWrapperProps extends IHasRouter<'article' | 'revision'> {
    article: Article;
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

const EditArticleWrapper: React.FC<IEditArticleWrapperProps> = ({ article, router }) => {
    const waitToLoadRef = React.createRef<IWaitToLoadHandle>();

    const displaySuccess = React.useCallback(async (message: string, extra: SweetAlertOptions = {}) => {
        return withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            ...extra
        });
    }, []);

    const loadRevision = React.useCallback(async () => {
        const response = await createAuthRequest().get<IRevision>(`blog/articles/${article.article.id}/revisions/${router.params.revision}`);

        return new Revision(response.data);
    }, [article, router.params]);

    const handleActionButtonClicked = React.useCallback(async (action: TArticleActions, inputs: IArticleActionInputs, dirty: TArticleActionDirtyValues, currentRevision?: Revision) => {
        const {
            title,
            slug,
            publishedAt,
            mainImage,
            tags
        } = inputs;

        const isDirty = (keys: (keyof TArticleActionDirtyValues)[]) =>
            Object.entries(dirty).filter(([key, value]) => value && keys.includes(key as keyof TArticleActionDirtyValues)).length > 0;

        try {
            switch (action) {
                case 'save-as-revision': {
                    // Update article title or slug if changed
                    if (isDirty(['title', 'slug'])) {
                        await updateArticle(article.article.id, title, slug, publishedAt);
                    }

                    // Update main image if needed
                    if (isDirty(['mainImage'])) {
                        if (mainImage)
                            await setMainImage(article.article.id, mainImage.uuid);
                        else
                            await unsetMainImage(article.article.id);
                    }

                    // Update tags if needed
                    if (isDirty(['tags'])) {
                        await syncTags(article.article.id, tags);
                    }

                    // Create revision for article
                    const revision = await createRevision(article.article.id, inputs.content, inputs.summary, currentRevision ? currentRevision.revision.uuid : undefined);

                    // Display message
                    await displaySuccess('Revision was saved.');

                    // Redirect to revision
                    router.navigate(article.generatePath(revision.revision.uuid));

                    break;
                }

                case 'update': {
                    // Update article title or slug if changed
                    if (isDirty(['title', 'slug'])) {
                        await updateArticle(article.article.id, title, slug, publishedAt);
                    }

                    // Update main image if needed
                    if (isDirty(['mainImage'])) {
                        if (mainImage)
                            await setMainImage(article.article.id, mainImage.uuid);
                        else
                            await unsetMainImage(article.article.id);
                    }

                    // Update tags if needed
                    if (isDirty(['tags'])) {
                        await syncTags(article.article.id, tags);
                    }

                    // Create revision for article
                    const revision = await createRevision(article.article.id, inputs.content, inputs.summary, currentRevision ? currentRevision.revision.uuid : undefined);

                    // Set as current revision
                    setCurrentRevision(article.article.id, revision.revision.uuid);

                    // Display message
                    await displaySuccess('Article was updated.');

                    // Redirect to revision
                    router.navigate(article.generatePath(revision.revision.uuid));

                    break;
                }

                case 'publish': {
                    // Update article title or slug and set published date/time
                    await updateArticle(article.article.id, title, slug, publishedAt);

                    // Update main image if needed
                    if (isDirty(['mainImage'])) {
                        if (mainImage)
                            await setMainImage(article.article.id, mainImage.uuid);
                        else
                            await unsetMainImage(article.article.id);
                    }

                    // Update tags if needed
                    if (isDirty(['tags'])) {
                        await syncTags(article.article.id, tags);
                    }

                    const message = `Article has been published.`;

                    // Check if content is changed
                    if (isDirty(['content'])) {
                        // Create revision for article
                        const revision = await createRevision(article.article.id, inputs.content, inputs.summary, currentRevision ? currentRevision.revision.uuid : undefined);

                        // Set as current revision
                        setCurrentRevision(article.article.id, revision.revision.uuid);

                        // Display message
                        await displaySuccess(message);

                        // Redirect to revision
                        router.navigate(article.generatePath(revision.revision.uuid));
                    } else {
                        // Display message
                        await displaySuccess(message);

                        // Refresh current revision
                        window.location.reload();
                    }

                    break;
                }

                case 'unpublish':
                case 'unschedule': {
                    // Update article title or slug if changed
                    // Clear published at date/time
                    await updateArticle(article.article.id, title, slug, null);

                    // Update main image if needed
                    if (isDirty(['mainImage'])) {
                        if (mainImage)
                            await setMainImage(article.article.id, mainImage.uuid);
                        else
                            await unsetMainImage(article.article.id);
                    }

                    // Update tags if needed
                    if (isDirty(['tags'])) {
                        await syncTags(article.article.id, tags);
                    }

                    const message = `Article has been ${action}ed.`;

                    // Check if content is changed
                    if (isDirty(['content'])) {
                        // Create revision for article
                        const revision = await createRevision(article.article.id, inputs.content, inputs.summary, currentRevision ? currentRevision.revision.uuid : undefined);

                        // Set as current revision
                        await setCurrentRevision(article.article.id, revision.revision.uuid);

                        // Display message
                        await displaySuccess(message);

                        // Redirect to revision
                        router.navigate(article.generatePath(revision.revision.uuid));
                    } else {
                        // Display message
                        await displaySuccess(message);

                        // Refresh current revision
                        window.location.reload();
                    }

                    break;
                }

                case 'delete': {
                    // Prompt user to confirm deletion
                    const result = await withReactContent(Swal).fire({
                        icon: 'question',
                        title: 'Are You Sure?',
                        text: `You will be able to restore the article.`,
                        showConfirmButton: true,
                        confirmButtonColor: 'red',
                        showCancelButton: true
                    });

                    // Check if user confirmed
                    if (result.isConfirmed) {
                        // Delete article
                        const response = await createAuthRequest().delete<Record<'success', string>>(`blog/articles/${article.article.id}`);

                        // Display message
                        await displaySuccess(response.data.success);

                        // Redirect to posts
                        router.navigate('/admin/posts');
                    }

                    break;
                }
            }

        } catch (err) {
            // What happens if an error occurred and user didn't try again.
            logger.error(err);

            await handleApiError(err, () => handleActionButtonClicked(action, inputs, dirty));
        }

    }, [article]);

    const handleRestoreRevisionClicked = React.useCallback(async () => {
        try {
            const selected = await awaitModalPrompt(SelectRevisionModal, { articleId: article.article.id });

            router.navigate(article.generatePath(selected.uuid));
        } catch (e) {
            // Modal was cancelled.
        }
    }, [router, article]);

    const handlePreviewArticleClicked = React.useCallback(() => {
        window.open(article.article.private_url, '_blank')?.focus();
    }, []);

    const [articleInfoModal, setArticleInfoModal] = React.useState(false);

    const createEditFormPropsFromRevision = React.useCallback(async (revision: Revision): Promise<IEditFormProps> => {
        const tags = await loadTags(article.article.id);

        return {
            article,
            revision,
            original: {
                article: {
                    title: article.article.title,
                    content: revision.revision.content,
                    summary: revision.revision.summary,
                    summary_auto_generate: revision.revision.summary_auto,
                    slug: article.article.slug,
                    slug_auto_generate: article.isSlugAutoGenerated,
                },
                mainImage: article.article.main_image ?? undefined,
                tags,
            },
            status: article.status,

            onArticleInformationClicked: () => setArticleInfoModal(true),
            onRestoreRevisionClicked: handleRestoreRevisionClicked,
            onPreviewArticleClicked: handlePreviewArticleClicked,
            onActionButtonClicked: (action, inputs, dirty) => handleActionButtonClicked(action, inputs, dirty, revision)
        }
    }, [article]);

    const createEditFormProps = React.useCallback(async () => {
        const revision = await loadRevision();

        return createEditFormPropsFromRevision(revision);
    }, []);

    return (
        <>
            {articleInfoModal && (
                <ArticleInfoModal
                    article={article}
                    onClosed={() => setArticleInfoModal(false)}
                />
            )}

            <WaitToLoad
                ref={waitToLoadRef}
                callback={createEditFormProps}
                loading={<Loader display={{ type: 'over-element' }} />}
            >
                {(props, err) => (
                    <>
                        {err && console.error(err)}
                        {props && <EditForm {...props} />}
                    </>
                )}
            </WaitToLoad>

        </>
    );
}

export default EditArticleWrapper;
