import React from 'react';
import { FormikHelpers, FormikProps } from 'formik';
import { Button, Card, CardBody, Col, Row } from 'reactstrap';
import { Tag } from 'react-tag-autocomplete';

import { DateTime } from 'luxon';

import FormWrapper, { IArticleFormValues } from './FormWrapper';
import MainImage from './article-form/MainImage';
import Tags from './article-form/Tags';
import Content, { TMarkdownImage, TUploadImagesCallback, transformImageToMarkdownImage } from './article-form/Content';
import UploadImageModal from './UploadImageModal';
import UnsavedChangesWarning from '@admin/components/UnsavedChangesWarning';

import { uploadImage } from '@admin/utils/api/endpoints/articles';
import awaitModalPrompt from '@admin/utils/modals';

export interface ICreateArticleFormValues extends IArticleFormValues {
}

interface ISaveArticleParamsBase {
    article: {
        title: string;
        slug: string;
        content: string;
        summary?: string;
    }
    mainImage?: IImage;
    images: IImage[];
    tags: Tag[];
}

export type TSaveArticleParams = ISaveArticleParamsBase;
export type TSaveAndPublishArticleParams = ISaveArticleParamsBase & { article: { publishedAt: DateTime; } };

interface IProps {
    onSaveClicked: (params: TSaveArticleParams) => Promise<void>;
    onSaveAndPublishClicked: (params: TSaveAndPublishArticleParams) => Promise<void>;
}

const CreateForm = React.forwardRef<FormikProps<ICreateArticleFormValues>, IProps>(({ onSaveClicked, onSaveAndPublishClicked, ...props }, ref) => {
    const initialValues = React.useMemo(
        () => ({
            title: '',
            content: '',
            summary: '',
            summary_auto_generate: true,
            slug: '',
            slug_auto_generate: true
        }),
        []
    );

    const [mainImage, setMainImage] = React.useState<IImage | undefined>(undefined);
    const [images, setImages] = React.useState<IImage[]>([]);
    const [tags, setTags] = React.useState<Tag[]>([]);

    const handleUploadMainImageClicked = React.useCallback(async () => {
        try {
            const uploaded = await awaitModalPrompt(UploadImageModal);

            setMainImage(uploaded);
        } catch (e) {
            // Modal was cancelled.
        }
    }, []);

    const handleRemoveMainImageClicked = React.useCallback(async () => {
        setMainImage(undefined);
    }, []);

    const handleImageUpload: TUploadImagesCallback = React.useCallback(async (files) => {
        const uploaded: TMarkdownImage[] = [];

        // Upload images
        for (const file of files) {
            const image = await uploadImage(file);

            uploaded.push(transformImageToMarkdownImage(image));

            // Add image to state so they're attached to article once it's created
            setImages((images) => [...images, image]);
        }

        return uploaded;
    }, []);

    const handleFormSubmit = React.useCallback(async (values: ICreateArticleFormValues, { setSubmitting }: FormikHelpers<ICreateArticleFormValues>) => {
        try {
            setSubmitting(true);

            const publishedAt = DateTime.now();

            onSaveAndPublishClicked({
                article: {
                    title: values.title,
                    slug: values.slug,
                    content: values.content,
                    summary: !values.summary_auto_generate ? values.summary : undefined,
                    publishedAt
                },
                mainImage,
                images,
                tags
            });
        } finally {
            setSubmitting(false);
        }
    }, [onSaveAndPublishClicked]);

    const handleSaveButtonClicked = React.useCallback(async (e: React.MouseEvent<HTMLButtonElement>, { values, setSubmitting }: FormikProps<ICreateArticleFormValues>) => {
        try {
            e.preventDefault();

            setSubmitting(true);

            onSaveClicked({
                article: {
                    title: values.title,
                    slug: values.slug,
                    content: values.content,
                    summary: !values.summary_auto_generate ? values.summary : undefined
                },
                mainImage,
                images,
                tags
            });
        } finally {
            setSubmitting(false);
        }
    }, [onSaveClicked]);

    return (
        <>
            <FormWrapper ref={ref} initialValues={initialValues} onSubmit={handleFormSubmit} {...props}>
                {(formikProps) => (
                    <>
                        <UnsavedChangesWarning enabled={Object.values(formikProps.touched).filter((value) => value).length > 0} />

                        <Row>
                            <Col md={8}>
                                <Card>
                                    <CardBody>
                                        <Content formikProps={formikProps} uploadImages={handleImageUpload} />
                                    </CardBody>
                                </Card>
                            </Col>

                            <Col md={4}>
                                <MainImage
                                    className='mb-3'
                                    current={mainImage}
                                    onUploadClicked={handleUploadMainImageClicked}
                                    onRemoveClicked={handleRemoveMainImageClicked}
                                />

                                <Tags className='mb-3' tags={tags} onTagsChanged={(tags) => setTags(tags)} />

                                <Card>
                                    <CardBody>
                                        <Row>
                                            <Col className='text-end'>
                                                <Button
                                                    type='button'
                                                    color='primary'
                                                    disabled={formikProps.isSubmitting}
                                                    className='me-1'
                                                    onClick={(e) => handleSaveButtonClicked(e, formikProps)}
                                                >
                                                    Save
                                                </Button>
                                                <Button
                                                    type='submit'
                                                    color='primary'
                                                    disabled={formikProps.isSubmitting}
                                                >
                                                    Save &amp; Publish
                                                </Button>
                                            </Col>
                                        </Row>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </>
                )}
            </FormWrapper>
        </>
    );
});

CreateForm.displayName = 'CreateForm';

export default CreateForm;
