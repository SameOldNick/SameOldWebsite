import React from 'react';
import { Tag } from 'react-tag-autocomplete';

import * as Yup from 'yup';
import { Formik } from 'formik';

import ArticleFormikLayout, { ArticleFormikLayoutChildren } from '@admin/components/blog/articles/containers/formik//ArticleFormikLayout';
import { ArticleEditorInputs } from '@admin/components/blog/articles/editor/ArticleEditorContext';

import Image from '@admin/utils/api/models/Image';

interface NewMainImage {
    file: File;
    src: string;
    description: string;
}

interface ExistingMainImage {
    src: string;
    description: string;
}

type SelectedMainImage = NewMainImage | ExistingMainImage;

interface ArticleFormValues {
    title: string;
    autoGenerateSlug: boolean;
    slug: string;
    content: string;
    autoGenerateSummary: boolean;
    summary: string;
    mainImage?: SelectedMainImage;
    uploadedImages: Image[];
    tags: Tag[];
}

interface ArticleFormikProviderProps {
    initialValues: ArticleFormValues;
    inputs: Partial<ArticleEditorInputs>;

    children: ArticleFormikLayoutChildren;
}

const ArticleFormikProvider: React.FC<ArticleFormikProviderProps> = ({
    initialValues,
    inputs,
    children,
}) => {
    const schema = React.useMemo(
        () =>
            Yup.object().shape({
                title: Yup.string().required('Title is required').max(255),
                content: Yup.string().required('Content is required'),
                summary: Yup.string().when('autoGenerateSummary', {
                    is: false,
                    then: (schema) => schema.required('Summary is required'),
                    otherwise: (schema) => schema.optional(),
                }),
                slug: Yup.string()
                    .required('Slug is required')
                    .matches(/^[a-z][a-z\d]*(-[a-z\d]+)*$/i),
            }),
        []
    );

    return (
        <Formik<ArticleFormValues>
            initialValues={initialValues}
            validationSchema={schema}
            onSubmit={() => {
                throw new Error('You must click one of the save buttons to submit.');
            }}
        >
            {(formik) => (
                <ArticleFormikLayout
                    formik={formik}
                    inputs={inputs}
                    children={children}
                />
            )}
        </Formik>
    );
}


export default ArticleFormikProvider;
export { ArticleFormikProviderProps, ArticleFormValues, SelectedMainImage, NewMainImage, ExistingMainImage };
