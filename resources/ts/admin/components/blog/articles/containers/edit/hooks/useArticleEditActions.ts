import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';
import { DateTime } from 'luxon';
import { FormikProps } from 'formik';

import ArticleInfoModal from '@admin/components/blog/articles/modals/ArticleInfoModal';
import SelectRevisionModal from '@admin/components/blog/articles/modals/SelectRevisionModal';
import SelectDateTimeModal from '@admin/components/modals/SelectDateTimeModal';

import { ArticleEditActionHandler, ArticleEditActionsHandlerParams, ArticleEditActionsHook } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanel';

import awaitModalPrompt from '@admin/utils/modals';

import { createRevision, setCurrentRevision, setMainImage, syncTags, unsetMainImage, updateArticle, uploadImage } from '@admin/utils/api/endpoints/articles';
import { createAuthRequest } from '@admin/utils/api/factories';

/**
 * Checks if values for keys are dirty
 *
 * @template Values
 * @param {FormikProps<Values>} formik
 * @param {(keyof Values)[]} keys
 * @returns True if one or more keys are dirty.
 */
function isFormikValuesDirty<Values>(formik: FormikProps<Values>, keys: (keyof Values)[]) {
    if (!formik.dirty)
        return false;

    for (const key of keys) {
        if (formik.values[key] !== formik.initialValues[key])
            return true;
    }

    return false;
}

const handleArticleInformationClicked: ArticleEditActionHandler = async ({ article }) => {
    await awaitModalPrompt(ArticleInfoModal, { article });
}

const handlePreviewArticleClicked: ArticleEditActionHandler = ({ article }) => {
    window.open(article.url, '_blank')?.focus();
}

const handleRestoreRevisionClicked: ArticleEditActionHandler = async ({ article, helpers: { onNavigate } }) => {
    try {
        const selected = await awaitModalPrompt(SelectRevisionModal, { articleId: article.article.id });

        // TODO: Revert to revision

        onNavigate(article.generatePath(selected.uuid));
    } catch (e) {
        // Modal was cancelled.
    }
}

const handleSaveAsRevisionClicked: ArticleEditActionHandler = async ({
    formik,
    article,
    revision: currentRevision,
    helpers: {
        onSuccess,
        onNavigate
    }
}) => {
    const {
        title,
        content,
        summary,
        slug,
        mainImage,
        tags
    } = formik.values;

    // Update article title or slug if changed
    if (isFormikValuesDirty(formik, ['title', 'slug'])) {
        await updateArticle(article.article.id, title, slug, article.publishedAt);
    }

    // Update main image if needed
    if (isFormikValuesDirty(formik, ['mainImage'])) {
        if (mainImage) {
            const image = await uploadImage(mainImage.file, mainImage.description);
            await setMainImage(article.article.id, image.uuid);
        } else {
            await unsetMainImage(article.article.id);
        }
    }

    // Update tags if needed
    if (isFormikValuesDirty(formik, ['tags'])) {
        await syncTags(article.article.id, tags);
    }

    // Create revision for article
    const revision = await createRevision(article.article.id, content, summary, currentRevision.revision.uuid);

    // Display message
    await onSuccess('Revision was saved.');

    // Redirect to revision
    onNavigate(article.generatePath(revision.revision.uuid));
}

const handleUpdateClicked: ArticleEditActionHandler = async ({
    formik,
    article,
    revision: currentRevision,
    helpers: {
        onSuccess,
        onNavigate
    }
}) => {
    const {
        title,
        content,
        summary,
        slug,
        mainImage,
        tags
    } = formik.values;

    // Update article title or slug if changed
    if (isFormikValuesDirty(formik, ['title', 'slug'])) {
        await updateArticle(article.article.id, title, slug, article.publishedAt);
    }

    // Update main image if needed
    if (isFormikValuesDirty(formik, ['mainImage'])) {
        if (mainImage) {
            const image = await uploadImage(mainImage.file, mainImage.description);
            await setMainImage(article.article.id, image.uuid);
        } else {
            await unsetMainImage(article.article.id);
        }
    }

    // Update tags if needed
    if (isFormikValuesDirty(formik, ['tags'])) {
        await syncTags(article.article.id, tags);
    }

    // Create revision for article
    const revision = await createRevision(article.article.id, content, summary, currentRevision ? currentRevision.revision.uuid : undefined);

    // Set as current revision
    setCurrentRevision(article.article.id, revision.revision.uuid);

    // Display message
    await onSuccess('Article was updated.');

    // Redirect to revision
    onNavigate(article.generatePath(revision.revision.uuid));
}

const handleUnpublishClicked: ArticleEditActionHandler = async (params) => {
    unpublishArticle(params, 'unschedule');
}

const handleScheduleClicked: ArticleEditActionHandler = async (params) => {
    try {
        const publishedAt = await awaitModalPrompt(SelectDateTimeModal);

        await publishArticle(params, publishedAt);
    } catch (err) {
        // User cancelled modal
    }

}

const handlePublishClicked: ArticleEditActionHandler = async (params) => {
    publishArticle(params, DateTime.now());
}

const handleUnscheduleClicked: ArticleEditActionHandler = async (params) => {
    unpublishArticle(params, 'unpublish');
}

const handleDeleteClicked: ArticleEditActionHandler = async ({
    article,
    helpers: {
        onSuccess,
        onNavigate
    }
}) => {
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
        await onSuccess(response.data.success);

        // Redirect to posts
        onNavigate('/admin/posts');
    }
}

const publishArticle = async ({
    formik,
    article,
    revision: currentRevision,
    helpers: {
        onSuccess,
        onNavigate,
        onReload
    }
}: ArticleEditActionsHandlerParams,
    dateTime: DateTime
) => {
    const {
        title,
        content,
        summary,
        slug,
        mainImage,
        tags
    } = formik.values;

    // Update article title or slug and set published date/time
    await updateArticle(article.article.id, title, slug, dateTime ?? DateTime.now());

    // Update main image if needed
    if (isFormikValuesDirty(formik, ['mainImage'])) {
        if (mainImage) {
            const image = await uploadImage(mainImage.file, mainImage.description);
            await setMainImage(article.article.id, image.uuid);
        } else {
            await unsetMainImage(article.article.id);
        }
    }

    // Update tags if needed
    if (isFormikValuesDirty(formik, ['tags'])) {
        await syncTags(article.article.id, tags);
    }

    const message = `Article has been published.`;

    // Check if content is changed
    if (isFormikValuesDirty(formik, ['content'])) {
        // Create revision for article
        const revision = await createRevision(article.article.id, content, summary, currentRevision ? currentRevision.revision.uuid : undefined);

        // Set as current revision
        setCurrentRevision(article.article.id, revision.revision.uuid);

        // Display message
        await onSuccess(message);

        // Redirect to revision
        onNavigate(article.generatePath(revision.revision.uuid));
    } else {
        // Display message
        await onSuccess(message);

        // Refresh current revision
        onReload();
    }
}

const unpublishArticle = async ({
    formik,
    article,
    revision: currentRevision,
    helpers: {
        onSuccess,
        onNavigate,
        onReload
    }
}: ArticleEditActionsHandlerParams,
    action: 'unpublish' | 'unschedule'
) => {
    const {
        title,
        content,
        summary,
        slug,
        mainImage,
        tags
    } = formik.values;

    // Update article title or slug if changed
    // Clear published at date/time
    await updateArticle(article.article.id, title, slug, null);

    // Update main image if needed
    if (isFormikValuesDirty(formik, ['mainImage'])) {
        if (mainImage) {
            const image = await uploadImage(mainImage.file, mainImage.description);
            await setMainImage(article.article.id, image.uuid);
        } else {
            await unsetMainImage(article.article.id);
        }
    }

    // Update tags if needed
    if (isFormikValuesDirty(formik, ['tags'])) {
        await syncTags(article.article.id, tags);
    }

    const message = `Article has been ${action}ed.`;

    // Check if content is changed
    if (isFormikValuesDirty(formik, ['content', 'summary'])) {
        // Create revision for article
        const revision = await createRevision(article.article.id, content, summary, currentRevision ? currentRevision.revision.uuid : undefined);

        // Set as current revision
        await setCurrentRevision(article.article.id, revision.revision.uuid);

        // Display message
        await onSuccess(message);

        // Redirect to revision
        onNavigate(article.generatePath(revision.revision.uuid));
    } else {
        // Display message
        await onSuccess(message);

        // Refresh current revision
        onReload();
    }
}

const useArticleEditActions: ArticleEditActionsHook = (params) => ({
    handleArticleInformationClicked: () => handleArticleInformationClicked(params),
    handlePreviewArticleClicked: () => handlePreviewArticleClicked(params),
    handleRestoreRevisionClicked: () => handleRestoreRevisionClicked(params),
    handleSaveAsRevisionClicked: () => handleSaveAsRevisionClicked(params),
    handleUpdateClicked: () => handleUpdateClicked(params),
    handleUnpublishClicked: () => handleUnpublishClicked(params),
    handleScheduleClicked: () => handleScheduleClicked(params),
    handlePublishClicked: () => handlePublishClicked(params),
    handleUnscheduleClicked: () => handleUnscheduleClicked(params),
    handleDeleteClicked: () => handleDeleteClicked(params),
});

export default useArticleEditActions;
