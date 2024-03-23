import React from 'react';
import withReactContent from 'sweetalert2-react-content';

import S from 'string';
import axios from 'axios';
import Swal from 'sweetalert2';
import { DateTime } from 'luxon';

import SelectDateTimeModal from '@admin/components/modals/SelectDateTimeModal';
import ArticleActionButtons from './ArticleActionButtons';

import Article from '@admin/utils/api/models/Article';

import { updateArticle, restoreArticle as restoreArticleApi, deleteArticle as deleteArticleApi } from '@admin/utils/api/endpoints/articles';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';

interface IArticleProps {
    article: Article;
    onUpdated: () => void;
}

const ArticleRow: React.FC<IArticleProps> = ({ article, onUpdated }) => {
    const handlePreviewClicked = React.useCallback((e: React.MouseEvent) => {
        e.preventDefault();

        // From https://stackoverflow.com/a/11384018/533242: Opens URL in new tab.
        window.open(article.article.private_url, '_blank')?.focus();
    }, [article]);

    const handleEditClicked = React.useCallback((e: React.MouseEvent) => {
        e.preventDefault();

        // TODO: Use router instead
        window.location.href = `posts/edit/${article.article.id}`;
    }, []);

    const handlePublishClicked = React.useCallback(async (e: React.MouseEvent) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `The article will become visible to all users.`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await publishArticle();

            onUpdated();
        }
    }, [onUpdated]);

    const handleScheduleClicked = React.useCallback(async (e: React.MouseEvent) => {
        try {
            const dateTime = await awaitModalPrompt(SelectDateTimeModal);

            scheduleArticle(dateTime);
        } catch (err) {
            // User cancelled modal.
        }
    }, []);

    const handleUnpublishClicked = React.useCallback(async (e: React.MouseEvent) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `The article will become hidden.`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await unpublishArticle();
            onUpdated();
        }
    }, [onUpdated]);

    const handleDeleteClicked = React.useCallback(async (e: React.MouseEvent) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `The article can be restored later, if needed.`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await deleteArticle();
            onUpdated();
        }
    }, [onUpdated]);

    const handleRestoreClicked = React.useCallback(async (e: React.MouseEvent) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `The article maybe made public.`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            await restoreArticle();
            onUpdated();
        }
    }, [onUpdated]);

    const publishArticle = React.useCallback(async () => {
        try {
            await updateArticle(article.article.id, article.article.title, article.article.slug, DateTime.now());
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `An error occurred: ${message}`,
                showConfirmButton: true,
                confirmButtonText: 'Try Again',
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await publishArticle();
            }
        }
    }, []);

    const scheduleArticle = React.useCallback(async (dateTime: DateTime) => {
        try {
            await updateArticle(article.article.id, article.article.title, article.article.slug, dateTime);

            onUpdated();
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `An error occurred: ${message}`,
                showConfirmButton: true,
                confirmButtonText: 'Try Again',
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await scheduleArticle(dateTime);
            }
        }
    }, [onUpdated]);

    const unpublishArticle = React.useCallback(async () => {
        try {
            await updateArticle(article.article.id, article.article.title, article.article.slug, null);
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `An error occurred: ${message}`,
                showConfirmButton: true,
                confirmButtonText: 'Try Again',
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await unpublishArticle();
            }
        }
    }, []);

    const deleteArticle = React.useCallback(async () => {
        try {
            await deleteArticleApi(article.article.id);

            onUpdated();
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `An error occurred: ${message}`,
                showConfirmButton: true,
                confirmButtonText: 'Try Again',
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await unpublishArticle();
            }
        }
    }, [onUpdated]);

    const restoreArticle = React.useCallback(async () => {
        try {
            await restoreArticleApi(article.article.id);

            onUpdated();
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Ooops...',
                text: `An error occurred: ${message}`,
                showConfirmButton: true,
                confirmButtonText: 'Try Again',
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await restoreArticle();
            }
        }
    }, [onUpdated]);

    return (
        <>
            <tr>
                <td>{article.article.id}</td>
                <td>{article.article.title}</td>
                <td>{S(article.article.revision?.summary).truncate(75).s}</td>
                <td>{S(article.status).capitalize().s}</td>
                <td>
                    <ArticleActionButtons
                        article={article}
                        onPreviewClicked={handlePreviewClicked}
                        onEditClicked={handleEditClicked}
                        onPublishNowClicked={handlePublishClicked}
                        onScheduleClicked={handleScheduleClicked}
                        onDeleteClicked={handleDeleteClicked}
                        onUnpublishClicked={handleUnpublishClicked}
                        onRestoreClicked={handleRestoreClicked}
                    />
                </td>
            </tr>
        </>
    );
}

export default ArticleRow;
