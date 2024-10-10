import React from 'react';
import { Tag } from 'react-tag-autocomplete';

import { FormikProps } from 'formik';

import { ArticleFormValues } from '@admin/components/blog/articles/containers/formik/ArticleFormikProvider';
import { ArticleEditorErrors } from '@admin/components/blog/articles/editor/ArticleEditor';
import { ArticleEditorInputs } from '@admin/components/blog/articles/editor/ArticleEditorContext';
import ChooseImageModal from '@admin/components/blog/articles/modals/ChooseImageModal';

import awaitModalPrompt from '@admin/utils/modals';
import { uploadImage } from '@admin/utils/api/endpoints/articles';

import Image from '@admin/utils/api/models/Image';
import Article from '@admin/utils/api/models/Article';

interface ArticleFormikLayoutChildrenParams {
    formik: FormikProps<ArticleFormValues>;
    errors: ArticleEditorErrors;
    inputs: ArticleEditorInputs;
}

interface ArticleFormikLayoutChildren {
    (params: ArticleFormikLayoutChildrenParams): React.ReactNode;
}

interface ArticleFormikLayoutProps {
    formik: FormikProps<ArticleFormValues>;
    inputs: Partial<ArticleEditorInputs>;
    children: ArticleFormikLayoutChildren;
}

const ArticleFormikLayout: React.FC<ArticleFormikLayoutProps> = ({
    formik,
    inputs,
    children,
}) => {
    const errors = React.useMemo(() => {
        return Object.entries(formik.errors).reduce((acc, [key, value]) => {
            if (!(key in acc))
                acc[key] = [];

            const messages = Array.isArray(value) ? value : [value];

            acc[key].push(...messages.filter((message) => typeof message === 'string'));

            return acc;
        }, {} as Record<string, string[]>);
    }, [formik.errors]);

    const handleTitleChanged = React.useCallback((title: string) => {
        formik.setFieldValue('title', title);

        if (formik.values.autoGenerateSlug) {
            const slug = Article.generateSlugFromTitle(title);
            formik.setFieldValue('slug', slug);
        }
    }, [formik]);

    const handleMainImageSelected = React.useCallback(async () => {
        try {
            const image = await awaitModalPrompt(ChooseImageModal);
            const selected = { file: image.file, src: image.content, description: image.description };
            formik.setFieldValue('mainImage', selected);
        } catch (err) {
            logger.info('User cancelled modal');
        }
    }, []);

    const handleMainImageRemoved = React.useCallback(async () => {
        formik.setFieldValue('mainImage', undefined);
    }, []);

    const handleUploadMarkdownImage = React.useCallback(async (files: File[]) => {
        const uploaded = files.map((file) => uploadImage(file));
        const images = (await Promise.all(uploaded)).map((image) => new Image(image));

        const prev = formik.values.uploadedImages ?? [];

        formik.setFieldValue('uploadedImages', prev.concat(images));

        return images.map((image) => ({
            url: image.url,
            alt: image.description,
            title: image.image.file.name,
        }));
    }, []);

    const formikInputs = React.useMemo<ArticleEditorInputs>(() => ({
        title: formik.values.title,
        onTitleChanged: handleTitleChanged,
        slug: formik.values.slug,
        onSlugChanged: (slug: string) => formik.setFieldValue('slug', slug),
        autoGenerateSlug: formik.values.autoGenerateSlug,
        onAutoGenerateSlugChanged: (checked: boolean) => formik.setFieldValue('autoGenerateSlug', checked),
        content: formik.values.content,
        onContentChange: (content: string) => formik.setFieldValue('content', content),
        autoGenerateSummary: formik.values.autoGenerateSummary,
        summary: formik.values.autoGenerateSummary ? '(Generated after saving)' : formik.values.summary,
        onAutoGenerateSummaryChanged: (checked: boolean) => formik.setFieldValue('autoGenerateSummary', checked),
        onSummaryChanged: (summary: string) => formik.setFieldValue('summary', summary),
        mainImage: formik.values.mainImage ? { src: formik.values.mainImage.src, description: formik.values.mainImage.description } : undefined,
        onMainImageSelected: handleMainImageSelected,
        onMainImageRemoved: handleMainImageRemoved,
        tags: formik.values.tags,
        onTagsChanged: (tags: Tag[]) => formik.setFieldValue('tags', tags),
        onUploadImage: handleUploadMarkdownImage,
    }), [formik, handleTitleChanged, handleMainImageSelected, handleMainImageRemoved, handleUploadMarkdownImage]);

    if (!children) return null;

    return children({
        formik,
        inputs: { ...formikInputs, ...inputs },
        errors,
    });
};


export default ArticleFormikLayout;
export { ArticleFormikLayoutProps, ArticleFormikLayoutChildren, ArticleFormikLayoutChildrenParams };
