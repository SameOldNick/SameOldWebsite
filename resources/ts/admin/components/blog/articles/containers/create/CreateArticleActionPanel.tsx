import React from 'react';
import { Tag } from 'react-tag-autocomplete';
import { Button } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import { useFormikContext } from 'formik';
import Swal from 'sweetalert2';
import { DateTime } from 'luxon';
import axios from 'axios';

import { ArticleFormValues, SelectedMainImage } from '@admin/components/blog/articles/containers/formik/ArticleFormikProvider';

import Image from '@admin/utils/api/models/Image';
import Article from '@admin/utils/api/models/Article';

import { attachImage, attachTags, createArticle, uploadImage, setMainImage as setMainImageApi } from '@admin/utils/api/endpoints/articles';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface CreateArticleActionPanelProps {
    onArticleCreated: (article: Article) => void;
}

const CreateArticleActionPanel: React.FC<CreateArticleActionPanelProps> = ({ onArticleCreated }) => {
    const formik = useFormikContext<ArticleFormValues>();

    const performSave = React.useCallback(async (publish: boolean = false) => {
        try {
            formik.setSubmitting(true);

            const { title, slug, content, summary, autoGenerateSummary, mainImage, uploadedImages, tags } = formik.values;
            const publishDate = publish ? DateTime.now() : null;

            // Create the article with either a publish date or null
            const created = await createArticle(title, slug, content, !autoGenerateSummary ? summary : null, publishDate);

            // Handle image and tag uploads
            await handleImageUpload(created.article.id, mainImage, uploadedImages);
            await handleTagAttachment(created.article.id, tags);

            // Show success notification
            await showSuccessAlert(publish);

            onArticleCreated(created);
        } catch (err) {
            await handleSaveError(err, publish);
        } finally {
            formik.setSubmitting(false);
        }
    }, [formik, onArticleCreated]);

    // Helper function for image upload
    const handleImageUpload = React.useCallback(async (articleId: number, mainImage: SelectedMainImage | undefined, uploadedImages: Image[]) => {
        if (mainImage) {
            if (!mainImage.file) {
                throw new Error('Main image is missing for new article');
            }

            const image = new Image(await uploadImage(mainImage.file));
            await attachImage(articleId, image.uuid);
            await setMainImageApi(articleId, image.uuid);
        }

        if (uploadedImages.length > 0) {
            for (const image of uploadedImages) {
                await attachImage(articleId, image.uuid);
            }
        }
    }, []);

    // Helper function for attaching tags
    const handleTagAttachment = React.useCallback(async (articleId: number, tags: Tag[]) => {
        if (tags.length > 0) {
            await attachTags(articleId, tags);
        }
    }, []);

    // Helper function for success alert
    const showSuccessAlert = React.useCallback(async (publish: boolean) => {
        return withReactContent(Swal).fire({
            icon: 'success',
            title: publish ? 'Published!' : 'Success!',
            text: publish ? 'The article has been published.' : 'The article has been saved.',
        });
    }, []);

    // Helper function for error handling and retry logic
    const handleSaveError = React.useCallback(async (err: any, publish: boolean) => {
        logger.error(err);

        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `Unable to ${publish ? 'publish' : 'save'} article: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            performSave(publish); // Retry save/publish
        }
    }, []);

    const handleSaveButtonClicked = React.useCallback(async (e: React.MouseEvent<HTMLButtonElement>, publish: boolean) => {
        e.preventDefault();

        const errors = await formik.validateForm();

        if (Object.keys(errors).length > 0) {
            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `One or more fields are invalid. Please fix them and try again.`,
            });

            return;
        }

        performSave(publish);
    }, [formik, performSave]);

    return (
        <>
            <Button
                type='button'
                color='primary'
                disabled={formik.isSubmitting}
                className='me-1'
                onClick={(e) => handleSaveButtonClicked(e, false)}
            >
                Save
            </Button>
            <Button
                type='button'
                color='primary'
                disabled={formik.isSubmitting}
                onClick={(e) => handleSaveButtonClicked(e, true)}
            >
                Save &amp; Publish
            </Button>
        </>
    );
}

export default CreateArticleActionPanel;
