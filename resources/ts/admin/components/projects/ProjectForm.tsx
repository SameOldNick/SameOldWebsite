import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, FormGroup, Input, Label, Row } from 'reactstrap';

import { Tag } from 'react-tag-autocomplete';
import * as Yup from 'yup';
import classNames from 'classnames';

import ReactTagsWithSuggestions from '@admin/components/ReactTagsWithSuggestions';

export interface IFormikValues {
    name: string;
    description: string;
    url: string;
}

export interface IOnSubmitValues extends IFormikValues {
    tags: Tag[];
}

type TFormikProps = React.ComponentProps<typeof Formik<IFormikValues>>;

interface IProps extends Omit<TFormikProps, 'onSubmit'> {
    initialTags: Tag[];
    buttonContent: React.ReactNode;
    onSubmit: (values: IOnSubmitValues, helpers: Parameters<TFormikProps['onSubmit']>[1]) => Promise<void>;
}

interface IState {
    tags: Tag[];
}

export default class extends React.Component<IProps, IState> {
    public static defaultProps = {
        initialTags: []
    };

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            tags: []
        };

        this.onSubmit = this.onSubmit.bind(this);
    }

    componentDidMount(): void {
        const { initialTags } = this.props;

        if (initialTags.length > 0)
            this.setState({ tags: initialTags });
    }

    private get schema() {
        return Yup.object().shape({
            name: Yup.string().required('Project name is required').min(1, 'Project name cannot be empty'),
            description: Yup.string().required('Description is required'),
            url: Yup.string().url('Project URL is invalid').required('Project URL is required')
        });
    }

    private async onSubmit(values: IFormikValues, helpers: FormikHelpers<IFormikValues>) {
        const { onSubmit } = this.props;
        const { tags } = this.state;

        await onSubmit({ tags, ...values }, helpers);

        return Promise.resolve();
    }

    public render() {
        const { buttonContent, onSubmit, ...props } = this.props;
        const { tags } = this.state;

        return (
            <>
                <Formik<IFormikValues> validationSchema={this.schema} onSubmit={this.onSubmit} {...props}>
                    {({ errors, touched, isSubmitting }) => (
                        <>
                            <Form>
                                <FormGroup className='has-validation'>
                                    <Label for='name'>Name:</Label>
                                    <Field as={Input} type='text' name='name' id='name' className={classNames({ 'is-invalid': errors.name && touched.name })} />
                                    <ErrorMessage name='name' component='div' className='invalid-feedback' />

                                </FormGroup>
                                <FormGroup className='has-validation'>
                                    <Label for='description'>Description:</Label>
                                    <Field as={Input} type='textarea' name='description' id='description' rows={5} className={classNames({ 'is-invalid': errors.description && touched.description })} />
                                    <ErrorMessage name='description' component='div' className='invalid-feedback' />
                                </FormGroup>

                                <Row>
                                    <Col md={6}>
                                        <FormGroup className='has-validation'>
                                            <Label for='url'>URL:</Label>
                                            <Field as={Input} type='url' name='url' id='url' className={classNames({ 'is-invalid': errors.url && touched.url })} />
                                            <ErrorMessage name='url' component='div' className='invalid-feedback' />
                                        </FormGroup>
                                    </Col>

                                    <Col md={6}>
                                        <FormGroup className='has-validation'>
                                            <Label for='tags'>Tags:</Label>
                                            <ReactTagsWithSuggestions
                                                allowNew
                                                selected={tags}
                                                onAdd={(tag) => this.setState({ tags: [...tags, tag] })}
                                                onDelete={(i) => this.setState({ tags: tags.filter((_, index) => i !== index) })}
                                            />
                                            <ErrorMessage name='tags' component='div' className='invalid-feedback' />
                                        </FormGroup>
                                    </Col>
                                </Row>

                                <Row>
                                    <Col className='text-end'>
                                        <Button color='primary' type='submit' disabled={isSubmitting}>
                                            {buttonContent}
                                        </Button>
                                    </Col>
                                </Row>
                            </Form>
                        </>
                    )}
                </Formik>
            </>
        );
    }
}
