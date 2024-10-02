import React from 'react';
import { FormikProps } from 'formik';
import { Badge, Button, Card, CardBody, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Row } from 'reactstrap';
import { FaExternalLinkAlt, FaInfoCircle, FaUndo } from 'react-icons/fa';
import { Tag } from 'react-tag-autocomplete';

import { DateTime } from 'luxon';
import S from 'string';

import FormWrapper, { IArticleFormValues } from './FormWrapper';
import MainImage from './article-form/MainImage';
import Tags from './article-form/Tags';
import Content, { TUploadImagesCallback } from './article-form/Content';
import UploadImageModal from './UploadImageModal';

import UnsavedChangesWarning from '@admin/components/UnsavedChangesWarning';
import Heading, { HeadingTitle } from '@admin/layouts/admin/Heading';
import SelectDateTimeModal from '@admin/components/modals/SelectDateTimeModal';

import Revision from '@admin/utils/api/models/Revision';
import Article, { TArticleStatus } from '@admin/utils/api/models/Article';
import { uploadImage } from '@admin/utils/api/endpoints/articles';
import awaitModalPrompt from '@admin/utils/modals';

export interface IArticleValues {
    title: string;
    slug: string;
    content: string;
    summary: string | null;
}


export type TArticleActions = 'save-as-revision' | 'update' | 'publish' | 'schedule' | 'unpublish' | 'unschedule' | 'delete';

export interface IEditArticleValues {
    article: IArticleFormValues;
    mainImage?: IImage;
    tags: Tag[];
}

export interface IArticleActionInputs {
    title: string,
    slug: string,
    content: string;
    summary: string | null;
    publishedAt: DateTime | null;
    images: IImage[];
    mainImage?: IImage;
    tags: Tag[];
}

export type TArticleActionDirtyValues = Record<keyof Omit<IArticleActionInputs, 'publishedAt'>, boolean>;

export interface IEditFormProps {
    article: Article;
    revision: Revision;
    original: IEditArticleValues;
    status: TArticleStatus;

    onArticleInformationClicked: () => void;
    onRestoreRevisionClicked: () => void;
    onPreviewArticleClicked: () => void;
    onActionButtonClicked: (action: TArticleActions, inputs: IArticleActionInputs, dirty: TArticleActionDirtyValues) => Promise<void>;

    onUpdate: (values: IArticleValues) => Promise<void>;
    onPublish: (values: IArticleValues, dateTime: DateTime) => Promise<void>;
    onUnpublish: (values: IArticleValues) => Promise<void>;
    onUnschedule: (values: IArticleValues) => Promise<void>;
}

const EditForm = React.forwardRef<FormikProps<IArticleFormValues>, IEditFormProps>((props, ref) => {
    const {
        article,
        revision,
        original,
        status,
        onArticleInformationClicked,
        onRestoreRevisionClicked,
        onPreviewArticleClicked,
        onActionButtonClicked
    } = props;

    const formikRef = React.useRef<FormikProps<IArticleFormValues> | null>();


    const [buttonDropdownOpen, setButtonDropdownOpen] = React.useState(false);

    const [tags, setTags] = React.useState<Tag[]>(original.tags);
    const [mainImage, setMainImage] = React.useState<IImage | undefined>(original.mainImage);
    const [images, setImages] = React.useState<IImage[]>([]);

    React.useEffect(() => {
        if (original.tags !== tags)
            setTags(original.tags);
    }, [original.tags]);

    React.useEffect(() => {
        if (original.mainImage !== mainImage)
            setMainImage(original.mainImage);
    }, [original.mainImage]);


    const getDirty = React.useCallback((values: IArticleFormValues): TArticleActionDirtyValues => {
        const { content, summary_auto_generate, summary, title, slug } = values;

        return {
            title: title !== original.article.title,
            slug: slug !== original.article.slug,
            content: content !== original.article.content,
            summary: summary !== original.article.summary || summary_auto_generate !== original.article.summary_auto_generate,
            mainImage: mainImage !== original.mainImage,
            tags: tags !== original.tags,
            images: images.length > 0
        };
    }, [original, images, tags, mainImage]);

    const hasDirty = React.useCallback((values: IArticleFormValues): boolean => Object.values(getDirty(values)).includes(true), [getDirty]);

    const handleActionButtonClick = React.useCallback(async (action: TArticleActions) => {
        if (!formikRef.current) {
            logger.error('No reference to formik.');
            return;
        }

        const { values: { content, summary_auto_generate, summary, title, slug }, values } = formikRef.current;

        const dirty = getDirty(values);

        // TODO: Indicate loading to user

        switch (action) {
            case 'save-as-revision':
            case 'update': {
                onActionButtonClicked(action, {
                    title,
                    slug,
                    content,
                    summary: !summary_auto_generate ? summary : null,
                    publishedAt: article.publishedAt,
                    mainImage,
                    images,
                    tags
                }, dirty);

                break;
            }

            case 'publish': {
                onActionButtonClicked(action, {
                    title,
                    slug,
                    content,
                    summary: !summary_auto_generate ? summary : null,
                    publishedAt: DateTime.now(),
                    mainImage,
                    images,
                    tags
                }, dirty);

                break;
            }

            case 'schedule': {
                const dateTime = await awaitModalPrompt(SelectDateTimeModal);

                onActionButtonClicked(action, {
                    title,
                    slug,
                    content,
                    summary: !summary_auto_generate ? summary : null,
                    publishedAt: dateTime,
                    mainImage,
                    images,
                    tags
                }, dirty);

                break;
            }

            case 'unpublish':
            case 'unschedule': {
                onActionButtonClicked(action, {
                    title,
                    slug,
                    content,
                    summary: !summary_auto_generate ? summary : null,
                    publishedAt: null,
                    mainImage,
                    images,
                    tags
                }, dirty);

                break;
            }

            case 'delete': {
                onActionButtonClicked('delete', {
                    title,
                    slug,
                    content,
                    summary: !summary_auto_generate ? summary : null,
                    publishedAt: null,
                    mainImage,
                    images,
                    tags
                }, dirty);

                break;
            }
        }
    }, [onActionButtonClicked]);

    const handleSaveAsRevisionClicked = React.useCallback(() => handleActionButtonClick('save-as-revision'), [handleActionButtonClick]);
    const handleUpdateClicked = React.useCallback(() => handleActionButtonClick('update'), [handleActionButtonClick]);
    const handlePublishClicked = React.useCallback(() => handleActionButtonClick('publish'), [handleActionButtonClick]);
    const handleUnpublishClicked = React.useCallback(() => handleActionButtonClick('unpublish'), [handleActionButtonClick]);
    const handleScheduleClicked = React.useCallback(() => handleActionButtonClick('schedule'), [handleActionButtonClick]);
    const handleUnscheduleClicked = React.useCallback(() => handleActionButtonClick('unschedule'), [handleActionButtonClick]);
    const handleDeleteClicked = React.useCallback(() => handleActionButtonClick('delete'), [handleActionButtonClick]);
    const handleUploadMainImageClicked = React.useCallback(async () => {
        try {
            const uploaded = await awaitModalPrompt(UploadImageModal);

            setMainImage(uploaded);

        } catch (e) {
            // Modal was cancelled.
        }
    }, []);

    const handleRemoveMainImageClicked = React.useCallback(async () => {
        // TODO: Confirm with user first
        setMainImage(undefined);
    }, []);

    const handleTagsChanged = React.useCallback((tags: Tag[]) => {
        setTags(tags);
    }, []);

    const handleUploadImages: TUploadImagesCallback = React.useCallback(async (files: File[]) => {
        const images: Awaited<ReturnType<TUploadImagesCallback>> = [];

        for (const file of files) {
            const uploaded = await uploadImage(file);

            setImages((prev) => prev.concat(uploaded));


            images.push({
                url: uploaded.file.url as string,
                alt: uploaded.description,
                title: uploaded.file.name
            });
        }

        return images;
    }, []);

    return (
        <>
            <FormWrapper
                ref={(instance) => formikRef.current = React.assignRef(ref, instance)}
                enableReinitialize
                initialValues={original.article}
                onSubmit={() => logger.error('Form submit is not implemented.')}
                {...props}
            >
                {(formikProps) => (
                    <>
                        <UnsavedChangesWarning enabled={hasDirty(formikProps.values)} />

                        <Heading>
                            <HeadingTitle>
                                Edit Post
                                {hasDirty(formikProps.values) && (
                                    <small className='ms-1 text-body-secondary'>
                                        <Badge color='secondary'>Unsaved Changes</Badge>
                                    </small>
                                )}
                            </HeadingTitle>

                            <div className='d-flex'>
                                <Button
                                    color="primary"
                                    outline
                                    className='me-1'
                                    title='Article Information'
                                    onClick={onArticleInformationClicked}
                                >
                                    <FaInfoCircle />
                                </Button>

                                <Button
                                    color="primary"
                                    outline
                                    className='me-1'
                                    title='Restore Revision'
                                    onClick={onRestoreRevisionClicked}
                                >
                                    <FaUndo />
                                </Button>

                                <Button
                                    color='primary'
                                    outline
                                    className='me-1'
                                    title='Preview Article'
                                    onClick={onPreviewArticleClicked}
                                >
                                    <FaExternalLinkAlt />
                                </Button>

                                {/* TODO: Move dropdown to seperate component. */}
                                <Dropdown toggle={() => setButtonDropdownOpen((prev) => !prev)} isOpen={buttonDropdownOpen}>
                                    <DropdownToggle caret color='primary'>
                                        {`Status: ${S(status).capitalize().s}`}
                                    </DropdownToggle>
                                    <DropdownMenu>
                                        {status === Article.ARTICLE_STATUS_PUBLISHED && (
                                            <>
                                                <DropdownItem onClick={handleSaveAsRevisionClicked}>Save as Revision</DropdownItem>
                                                <DropdownItem onClick={handleUpdateClicked}>Update</DropdownItem>
                                                <DropdownItem divider />
                                                <DropdownItem onClick={handleUnpublishClicked}>Unpublish</DropdownItem>
                                                <DropdownItem onClick={handleScheduleClicked}>Schedule</DropdownItem>
                                                <DropdownItem onClick={handleDeleteClicked}>Delete</DropdownItem>
                                            </>
                                        )}
                                        {status === Article.ARTICLE_STATUS_UNPUBLISHED && (
                                            <>
                                                <DropdownItem onClick={handleSaveAsRevisionClicked}>Save as Revision</DropdownItem>
                                                <DropdownItem onClick={handlePublishClicked}>Save &amp; Publish</DropdownItem>
                                                <DropdownItem onClick={handleScheduleClicked}>Schedule</DropdownItem>
                                                <DropdownItem divider />
                                                <DropdownItem onClick={handleDeleteClicked}>Delete</DropdownItem>
                                            </>
                                        )}
                                        {status === Article.ARTICLE_STATUS_SCHEDULED && (
                                            <>
                                                <DropdownItem onClick={handleSaveAsRevisionClicked}>Save as Revision</DropdownItem>
                                                <DropdownItem onClick={handleUpdateClicked}>Update</DropdownItem>
                                                <DropdownItem onClick={handlePublishClicked}>Publish Immediately</DropdownItem>
                                                <DropdownItem onClick={handleScheduleClicked}>Reschedule</DropdownItem>
                                                <DropdownItem divider />
                                                <DropdownItem onClick={handleUnscheduleClicked}>Unschedule</DropdownItem>
                                                <DropdownItem onClick={handleDeleteClicked}>Delete</DropdownItem>
                                            </>
                                        )}
                                    </DropdownMenu>
                                </Dropdown>
                            </div>
                        </Heading>

                        <Row>
                            <Col md={8}>
                                <Card>
                                    <CardBody>
                                        <Content
                                            formikProps={formikProps}
                                            uploadImages={handleUploadImages}
                                        />
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

                                <Tags tags={tags} onTagsChanged={handleTagsChanged} />
                            </Col>
                        </Row>
                    </>
                )}
            </FormWrapper>
        </>
    );
});

EditForm.displayName = 'EditForm';

export default EditForm;
