import React from 'react';
import { Navigate } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import { Tag } from 'react-tag-autocomplete';
import withReactContent from 'sweetalert2-react-content';

import { DateTime } from 'luxon';
import Swal from 'sweetalert2';
import axios from 'axios';

import Heading from '@admin/layouts/admin/Heading';
import CreateForm, { TSaveArticleParams, TSaveAndPublishArticleParams } from '@admin/components/blog/CreateForm';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { attachImage, attachTags, createArticle, setMainImage as setMainImageApi } from '@admin/utils/api/endpoints/articles';

import Article from '@admin/utils/api/models/Article';

interface IProps {

}

const Create: React.FC<IProps> = ({ }) => {
    const [created, setCreated] = React.useState<Article | undefined>();

    const saveArticle = React.useCallback(async (title: string, slug: string, content: string, summary?: string, publishedAt?: DateTime) => {
        return createArticle(title, slug, content, summary || null, publishedAt || null);
    }, []);

    const setMainImage = React.useCallback(async (article: Article, mainImage: IImage) => {
        await attachImage(article.article.id, mainImage.uuid);
        await setMainImageApi(article.article.id, mainImage.uuid);
    }, []);

    const associateImages = React.useCallback(async (article: Article, images: IImage[]) => {
        for (const image of images) {
            await attachImage(article.article.id, image.uuid);
        }
    }, []);

    const associateTags = React.useCallback(async (article: Article, tags: Tag[]) => {
        await attachTags(article.article.id, tags);
    }, []);

    const handleSave = React.useCallback(async (params: TSaveArticleParams) => {
        try {
            const { article, mainImage, images, tags } = params;

            const created = await saveArticle(article.title, article.slug, article.content, article.summary);

            if (mainImage !== undefined) {
                await setMainImage(created, mainImage);
            }

            if (images.length > 0) {
                await associateImages(created, images);
            }

            if (tags.length > 0) {
                associateTags(created, tags);
            }

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The article has been saved.`
            });

            setCreated(created);
        } catch (err) {
            console.error(err);

            // TODO: Revert API calls that were successful.

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to save article: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await handleSave(params);
        }
    }, []);

    const handleSaveAndPublish = React.useCallback(async (params: TSaveAndPublishArticleParams) => {
        try {
            const { article, mainImage, images, tags } = params;

            const created = await saveArticle(article.title, article.slug, article.content, article.summary, article.publishedAt);

            if (mainImage !== undefined) {
                await setMainImage(created, mainImage);
            }

            if (images.length > 0) {
                await associateImages(created, images);
            }

            if (tags.length > 0) {
                associateTags(created, tags);
            }

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The article has been saved and published.`
            });

            setCreated(created);
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to save and publish article: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await handleSaveAndPublish(params);
        }
    }, []);

    return (
        <>
            <Helmet>
                <title>Create Post</title>
            </Helmet>

            <Heading title='Create Post' />

            {created !== undefined && <Navigate to={created.generatePath()} />}

            <CreateForm
                onSaveClicked={handleSave}
                onSaveAndPublishClicked={handleSaveAndPublish}
            />
        </>
    );
}

export default requiresRolesForPage(Create, ['write_posts']);
