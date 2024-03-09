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

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { attachImage, attachTags, createArticle, setMainImage } from '@admin/utils/api/endpoints/articles';

import Article from '@admin/utils/api/models/Article';

interface IProps {

}

interface IState {
    created?: Article;
}

export default class Create extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };

        this.handleSave = this.handleSave.bind(this);
        this.handleSaveAndPublish = this.handleSaveAndPublish.bind(this);
    }

    private async saveArticle(title: string, slug: string, content: string, summary?: string, publishedAt?: DateTime) {
        return createArticle(title, slug, content, summary || null, publishedAt || null);
    }

    private async setMainImage(article: Article, mainImage: IImage) {
        await attachImage(article.article.id, mainImage.uuid);
        await setMainImage(article.article.id, mainImage.uuid);
    }

    private async associateImages(article: Article, images: IImage[]) {
        for (const image of images) {
            await attachImage(article.article.id, image.uuid);
        }
    }

    private async associateTags(article: Article, tags: Tag[]) {
        await attachTags(article.article.id, tags);
    }

    private async handleSave(params: TSaveArticleParams) {
        try {
            const { article, mainImage, images, tags } = params;

            const created = await this.saveArticle(article.title, article.slug, article.content, article.summary);

            if (mainImage !== undefined) {
                await this.setMainImage(created, mainImage);
            }

            if (images.length > 0) {
                await this.associateImages(created, images);
            }

            if (tags.length > 0) {
                this.associateTags(created, tags);
            }

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The article has been saved.`
            });

            this.setState({ created });
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
                await this.handleSave(params);
        }
    }

    private async handleSaveAndPublish(params: TSaveAndPublishArticleParams) {
        try {
            const { article, mainImage, images, tags } = params;

            const created = await this.saveArticle(article.title, article.slug, article.content, article.summary, article.publishedAt);

            if (mainImage !== undefined) {
                await this.setMainImage(created, mainImage);
            }

            if (images.length > 0) {
                await this.associateImages(created, images);
            }

            if (tags.length > 0) {
                this.associateTags(created, tags);
            }

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The article has been saved and published.`
            });

            this.setState({ created });
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
                await this.handleSaveAndPublish(params);
        }
    }

    public render() {
        const { created } = this.state;

        return (
            <>
                <Helmet>
                    <title>Create Post</title>
                </Helmet>

                <Heading title='Create Post' />

                {created !== undefined && <Navigate to={created.generatePath()} />}

                <CreateForm
                    onSaveClicked={this.handleSave}
                    onSaveAndPublishClicked={this.handleSaveAndPublish}
                />
            </>
        );
    }
}
