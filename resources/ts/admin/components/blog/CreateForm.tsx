import React from 'react';
import { FormikHelpers, FormikProps } from 'formik';
import { Button, Card, CardBody, Col, Row } from 'reactstrap';
import { Tag } from 'react-tag-autocomplete';

import FormWrapper, { IArticleFormValues } from './FormWrapper';
import SelectMainImage, { TMainImage } from './article-form/main-image';
import Tags from './article-form/Tags';
import Content from './article-form/Content';

import UnsavedChangesWarning from '@admin/components/UnsavedChangesWarning';

export interface ICreateArticleFormValues extends IArticleFormValues {

}

interface IProps {
    tags: Tag[];
    mainImage: TMainImage | undefined;

    onTagsChanged: (tags: Tag[]) => void;
    onMainImageChanged: (image: TMainImage | undefined) => void;

    onSaveClicked: (values: ICreateArticleFormValues, helpers: FormikHelpers<ICreateArticleFormValues>) => Promise<void>;
    onFormSubmit: (values: ICreateArticleFormValues, helpers: FormikHelpers<ICreateArticleFormValues>) => Promise<void>;
}

const CreateForm = React.forwardRef<FormikProps<ICreateArticleFormValues>, IProps>(({ tags, mainImage, onTagsChanged, onMainImageChanged, onSaveClicked, onFormSubmit, ...props }, ref) => {
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

    return (
        <>
            <FormWrapper ref={ref} initialValues={initialValues} onSubmit={onFormSubmit} {...props}>
                {(formikProps) => (
                    <>
                        <UnsavedChangesWarning enabled={Object.values(formikProps.touched).filter((value) => value).length > 0} />

                        <Row>
                            <Col md={8}>
                                <Card>
                                    <CardBody>
                                        <Content formikProps={formikProps} />

                                    </CardBody>
                                </Card>
                            </Col>

                            <Col md={4}>
                                <SelectMainImage className='mb-3' current={mainImage} onChange={(image) => onMainImageChanged(image)} />

                                <Tags className='mb-3' tags={tags} onTagsChanged={onTagsChanged} />

                                <Card>
                                    <CardBody>
                                        <Row>
                                            <Col className='text-end'>
                                                <Button
                                                    type='button'
                                                    color='primary'
                                                    disabled={formikProps.isSubmitting}
                                                    className='me-1'
                                                    onClick={() => onSaveClicked(formikProps.values, formikProps)}
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
