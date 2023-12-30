import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, FormGroup, Input, Label, Row } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import * as Yup from 'yup';
import classNames from 'classnames';
import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';

import MarkdownEditor from '@admin/components/MarkdownEditor';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import { IHasRouter } from '@admin/components/hoc/WithRouter';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

export interface IFormikValues {
    name: string;
    headline: string;
    location: string;
    biography: string;
}

interface IProps extends IHasRouter {
}

interface IState {
}

export default class HomepageForm extends React.Component<IProps, IState> {
    private _waitToLoadRef = React.createRef<WaitToLoad<IPageMetaData[]>>();

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };

        this.getHomepageMetaData = this.getHomepageMetaData.bind(this);
        this.onHomepageFormSubmitted = this.onHomepageFormSubmitted.bind(this);
        this.handleError = this.handleError.bind(this);
        this.getInitialHomePageValues = this.getInitialHomePageValues.bind(this);
    }


    private get schema() {
        return Yup.object().shape({
            name: Yup.string().required('Name is required').max(255),
            headline: Yup.string().required('Headline is required').max(255),
            location: Yup.string().required('Location is required').max(255),
            biography: Yup.string().required('Biography is required'),
        });
    }

    private async getHomepageMetaData() {
        const response = await createAuthRequest().get<IPageMetaData[]>('/pages/homepage');

        return response.data;
    }

    private getInitialHomePageValues(metaData: IPageMetaData[]) {
        const initialValues = {
            name: '',
            headline: '',
            location: '',
            biography: '',
        }

        for (const { key, value } of metaData) {
            if (initialValues[key as keyof IFormikValues] !== undefined) {
                initialValues[key as keyof IFormikValues] = value;
            }
        }

        return initialValues;
    }

    private async onHomepageFormSubmitted({ name, headline, location, biography }: IFormikValues, helpers: FormikHelpers<IFormikValues>) {
        try {
            const response = await createAuthRequest().post<IPageMetaData[]>('/pages/homepage', {
                name,
                headline,
                location,
                biography
            });

            await this.onUpdated(response);
        } catch (e) {
            await this.onError(e);
        }
    }

    private async onUpdated(response: AxiosResponse<IPageMetaData[]>) {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Updated',
            text: 'The homepage was successfully updated.',
        });

        this._waitToLoadRef.current?.load();
    }

    private async onError(err: unknown) {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred: ${message}`,
        });
    }

    private async handleError(err: unknown) {
        const { router: { navigate } } = this.props;

        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `Unable to retrieve homepage meta data: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            this._waitToLoadRef.current?.load();
        } else {
            navigate(-1);
        }
    }

    public render() {
        const { } = this.props;
        const { } = this.state;

        return (
            <>
                <WaitToLoad ref={this._waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={this.getHomepageMetaData}>
                    {(metaData, err) => (
                        <>
                            {err !== undefined && this.handleError(err)}
                            {metaData !== undefined && (
                                <>
                                    <Formik<IFormikValues>
                                        validationSchema={this.schema}
                                        initialValues={this.getInitialHomePageValues(metaData)}
                                        onSubmit={this.onHomepageFormSubmitted}
                                    >
                                        {({ errors, touched, isSubmitting, values, setFieldValue }) => (
                                            <>
                                                <Form>

                                                    <Row>
                                                        <Col md={12}>
                                                            <FormGroup className='has-validation'>
                                                                <Label for='name'>Name:</Label>
                                                                <Field as={Input} type='text' name='name' id='name' className={classNames({ 'is-invalid': errors.name && touched.name })} />
                                                                <ErrorMessage name='name' component='div' className='invalid-feedback' />

                                                            </FormGroup>
                                                        </Col>

                                                        <Col md={12}>
                                                            <FormGroup className='has-validation'>
                                                                <Label for='headline'>Headline:</Label>
                                                                <Field as={Input} type='text' name='headline' id='headline' className={classNames({ 'is-invalid': errors.headline && touched.headline })} />
                                                                <ErrorMessage name='headline' component='div' className='invalid-feedback' />

                                                            </FormGroup>
                                                        </Col>

                                                        <Col md={12}>
                                                            <FormGroup className='has-validation'>
                                                                <Label for='location'>Location:</Label>
                                                                <Field as={Input} type='text' name='location' id='location' className={classNames({ 'is-invalid': errors.location && touched.location })} />
                                                                <ErrorMessage name='location' component='div' className='invalid-feedback' />

                                                            </FormGroup>
                                                        </Col>

                                                        <Col md={12}>
                                                            <FormGroup className='has-validation'>
                                                                <Label for='biography'>Biography:</Label>
                                                                <MarkdownEditor mode='split' value={values.biography} onChange={(v) => setFieldValue('biography', v)} />
                                                                <ErrorMessage name='biography' component='div' className='invalid-feedback' />

                                                            </FormGroup>
                                                        </Col>
                                                    </Row>

                                                    <Row>
                                                        <Col className='text-end'>
                                                            <Button color='primary' type='submit' disabled={isSubmitting}>
                                                                Update Profile
                                                            </Button>
                                                        </Col>
                                                    </Row>
                                                </Form>
                                            </>
                                        )}
                                    </Formik>
                                </>
                            )}
                        </>
                    )}
                </WaitToLoad>

            </>
        );
    }
}
