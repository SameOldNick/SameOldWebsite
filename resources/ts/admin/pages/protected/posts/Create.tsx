import React from 'react';
import { Navigate } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import { Tag } from 'react-tag-autocomplete';
import withReactContent from 'sweetalert2-react-content';
import { FormikHelpers } from 'formik';

import { DateTime } from 'luxon';
import Swal from 'sweetalert2';
import axios from 'axios';

import Heading from '@admin/layouts/admin/Heading';
import CreateForm, { ICreateArticleFormValues } from '@admin/components/blog/CreateForm';
import { TMainImage, isMainImageNew } from '@admin/components/blog/article-form/main-image';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import Article from '@admin/utils/api/models/Article';
import { attachTags, createArticle, setMainImage, uploadMainImage } from '@admin/utils/api/calls/articles';

interface IProps {

}

interface IState {
    created?: Article;
    tags: Tag[];
    mainImage: TMainImage | undefined;
}

export default class Create extends React.Component<IProps, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            tags: [],
            mainImage: undefined
        };

        this.handleSave = this.handleSave.bind(this);
        this.handleSaveAndPublish = this.handleSaveAndPublish.bind(this);
    }

    private async saveArticle(values: ICreateArticleFormValues, publishedAt: DateTime | null) {
        const { tags, mainImage } = this.state;

        let article: Article | undefined;
        let articleImage: IArticleImage | undefined;

        try {
            article = await createArticle(values.title, values.slug, values.content, !values.summary_auto_generate ? values.summary : null, publishedAt);

            if (mainImage !== undefined && isMainImageNew(mainImage)) {
                articleImage = await uploadMainImage(article, mainImage);

                //articleImage = await this.uploadMainImage(article, mainImage);
                await setMainImage(article, articleImage);
            }

            await attachTags(article, tags);

            return article;
        } catch (err) {
            console.error(err);

            if (articleImage && articleImage.id) {
                createAuthRequest().delete(`blog/articles/${article?.article.id}/images/${articleImage.id}`);
            }

            if (article && article.article.revision && article.article.revision.uuid) {
                createAuthRequest().delete(`blog/articles/${article?.article.id}/revisions/${article.article.revision.uuid}`);
            }

            if (article && article.article.id) {
                createAuthRequest().delete(`blog/articles/${article?.article.id}`);
            }

            throw err;
        }
    }

    private async handleSave(values: ICreateArticleFormValues, { setSubmitting }: FormikHelpers<ICreateArticleFormValues>) {
        const trySaveArticle = async () => {
            try {
                const article = await this.saveArticle(values, null);

                await withReactContent(Swal).fire({
                    icon: 'success',
                    title: 'Success!',
                    text: `The article has been saved.`
                });

                this.setState({ created: article });
            } catch (err) {
                console.error(err);

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
                    await trySaveArticle();
            }
        }

        setSubmitting(true);

        await trySaveArticle();

        setSubmitting(false);
    }

    private async handleSaveAndPublish(values: ICreateArticleFormValues) {
        const publishedAt = DateTime.now();

        const trySaveArticle = async () => {
            try {
                const article = await this.saveArticle(values, publishedAt);

                await withReactContent(Swal).fire({
                    icon: 'success',
                    title: 'Success!',
                    text: `The article has been saved and published.`
                });

                this.setState({ created: article });
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
                    await trySaveArticle();
            }
        }

        await trySaveArticle();
    }

    public render() {
        const { created, tags, mainImage } = this.state;

        return (
            <>
                <Helmet>
                    <title>Create Post</title>
                </Helmet>

                <Heading title='Create Post' />

                {created !== undefined && <Navigate to={created.generatePath()} />}

                <CreateForm
                    tags={tags}
                    mainImage={mainImage}
                    onTagsChanged={(tags) => this.setState({ tags })}
                    onMainImageChanged={(image) => this.setState({ mainImage: image })}
                    onSaveClicked={this.handleSave}
                    onFormSubmit={this.handleSaveAndPublish}
                />
            </>
        );
    }
}
