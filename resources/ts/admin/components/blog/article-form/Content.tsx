import React from 'react';
import { ErrorMessage, Field, FormikProps } from 'formik';
import { Col, FormGroup, Input, InputGroup, InputGroupText, Label, Row, Tooltip } from 'reactstrap';
import { FaInfoCircle } from 'react-icons/fa';

import classNames from 'classnames';
import S from 'string';

import MarkdownEditor from '@admin/components/MarkdownEditor';
import { IArticleFormValues } from '../FormWrapper';


interface IProps {
    formikProps: FormikProps<IArticleFormValues>;
}

export const generateSlugFromTitle = (title: string) => {
    const slug = S(title).slugify().s;

    return slug;
}

const Content: React.FC<IProps> = ({ formikProps: { errors, touched, values, handleChange, handleBlur, setFieldValue } }) => {
    const [slugTooltipOpen, setSlugTooltipOpen] = React.useState(false);

    const handleTitleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        handleChange(e);

        if (values.slug_auto_generate)
            setFieldValue('slug', generateSlugFromTitle(e.target.value));
    }

    const handleTitleBlur = (e: React.ChangeEvent<HTMLInputElement>) => {
        handleBlur(e);

        if (values.slug_auto_generate)
            setFieldValue('slug', generateSlugFromTitle(e.target.value));
    }

    return (
        <>
            <Row>
                <Col md={7}>
                    <FormGroup className='has-validation'>
                        <Label for='title'>Title:</Label>
                        <Field
                            as={Input}
                            type='text'
                            name='title'
                            id='title'
                            className={classNames({ 'is-invalid': errors.title && touched.title })}
                            onChange={handleTitleChange}
                            onBlur={handleTitleBlur}
                        />
                        <ErrorMessage name='title' component='div' className='invalid-feedback' />

                    </FormGroup>
                </Col>
                <Col md={5}>
                    <FormGroup className='has-validation'>
                        <Label for='slug'>
                            <a href='#' id='slugTooltip' className='text-decoration-none'>
                                Slug:{' '}
                                <FaInfoCircle />
                            </a>
                        </Label>

                        <InputGroup>
                            <InputGroupText>
                                <Field
                                    as={Input}
                                    type="checkbox"
                                    addon
                                    name='slug_auto_generate'
                                    id='slug_auto_generate'
                                    aria-label="Enable slug auto generation"
                                />
                            </InputGroupText>
                            <Field
                                as={Input}
                                type='text'
                                name='slug'
                                id='slug'
                                disabled={values.slug_auto_generate}
                                className={classNames({ 'is-invalid': errors.slug && touched.slug })}
                            />
                        </InputGroup>


                        <ErrorMessage name='slug' component='div' className='invalid-feedback' />
                    </FormGroup>

                    <Tooltip
                        isOpen={slugTooltipOpen}
                        target="slugTooltip"
                        toggle={() => setSlugTooltipOpen(!slugTooltipOpen)}
                    >
                        Select the checkbox to auto generate the slug from the title.
                    </Tooltip>
                </Col>

                <Col xs={12}>
                    <FormGroup className='has-validation'>
                        <Label for='description'>Content:</Label>

                        <MarkdownEditor mode='split' value={values.content} onChange={(v) => setFieldValue('content', v)} />

                        <ErrorMessage name='description' component='div' className='invalid-feedback' />
                    </FormGroup>
                </Col>
            </Row>

            <Row>
                <Col xs={12}>
                    <div className='has-validation mb-2'>
                        <Label for='summary'>Summary:</Label>
                        <Field as={Input} type='textarea' name='summary' id='summary' rows={5} disabled={values.summary_auto_generate} className={classNames({ 'is-invalid': errors.summary && touched.summary })} />
                        <ErrorMessage name='summary' component='div' className='invalid-feedback' />
                    </div>
                </Col>
                <Col xs={12}>
                    <FormGroup check className='mb-3'>
                        <Field as={Input} type='checkbox' name='summary_auto_generate' id='summary_auto_generate' className={classNames({ 'is-invalid': errors.summary_auto_generate && touched.summary_auto_generate })} />
                        <Label check htmlFor='summary_auto_generate'>Auto generate summary</Label>
                    </FormGroup>
                </Col>

            </Row>
        </>
    );
}

export default Content;
