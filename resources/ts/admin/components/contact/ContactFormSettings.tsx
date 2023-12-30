import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, Collapse, FormGroup, FormText, Input, Label, List, ListInlineItem, Row, Tooltip, Badge } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';
import { FaInfoCircle } from 'react-icons/fa';

import * as Yup from 'yup';
import classNames from 'classnames';
import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';
import S from 'string';

import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import { IHasRouter } from '@admin/components/hoc/WithRouter';
import MarkdownEditor from '@admin/components/MarkdownEditor';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

export interface IFormikValues {
    sender_replyto: string;
    sender_subject: string;
    sender_message: string;
    recipient_email: string;
    recipient_subject: string;
    recipient_template: string;
    require_recaptcha: boolean;
    require_confirmation: boolean;
    confirmation_required_by: string;
    confirmation_subject: string;
    honeypot_field: boolean;
    honeypot_field_name: string;
}

interface IProps extends IHasRouter {
}

interface IState {
    senderMessageTooltipOpen: boolean;
    recipientMessageTooltipOpen: boolean;
    recipientSubjectTooltipOpen: boolean;
}

export default class ContactFormSettings extends React.Component<IProps, IState> {
    private _waitToLoadRef = React.createRef<WaitToLoad<IPageMetaData[]>>();

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            senderMessageTooltipOpen: false,
            recipientSubjectTooltipOpen: false,
            recipientMessageTooltipOpen: false,
        };

        this.getMetaData = this.getMetaData.bind(this);
        this.onFormSubmitted = this.onFormSubmitted.bind(this);
        this.handleError = this.handleError.bind(this);
        this.getInitialFormValues = this.getInitialFormValues.bind(this);
    }

    private get schema() {
        return Yup.object().shape({
            sender_replyto: Yup.string().required('Sender reply-to e-mail is required').email().max(255),
            sender_subject: Yup.string().required('Sender subject is required').max(255),
            sender_message: Yup.string().required('Sender message is required'),
            recipient_email: Yup.string().required('Recipient e-mail is required').email().max(255),
            recipient_subject: Yup.string().required('Recipient subject is required').max(255),
            recipient_template: Yup.string().required('Recipient message template is required'),
            require_recaptcha: Yup.boolean(),
            require_confirmation: Yup.boolean(),
            confirmation_required_by: Yup.string().oneOf(['all_users', 'unregistered_users', 'unregistered_unverified_users']),
            confirmation_subject: Yup.string().max(255),
            honeypot_field: Yup.boolean(),
            honeypot_field_name: Yup.string().max(255).when('honeypot_field', {
                is: true,
                then: Yup.string().required()
            }),
        });
    }

    private async getMetaData() {
        const response = await createAuthRequest().get<IPageMetaData[]>('/pages/contact');

        return response.data;
    }

    private getInitialFormValues(metaData: IPageMetaData[]) {
        const initialValues: IFormikValues = {
            sender_replyto: '',
            sender_subject: '',
            sender_message: '',
            recipient_email: '',
            recipient_subject: '',
            recipient_template: '',
            require_recaptcha: false,
            require_confirmation: false,
            confirmation_required_by: 'all_users',
            confirmation_subject: '',
            honeypot_field: false,
            honeypot_field_name: '',
        }

        const values: Record<string, string | boolean> = {};

        for (const { key, value } of metaData) {
            if (['send_confirmation'].includes(key)) {
                values[key] = S(value).toBoolean();
            } else {
                values[key] = value;
            }
        }

        return Object.assign(initialValues, values) as IFormikValues;
    }

    private async onFormSubmitted(values: IFormikValues, helpers: FormikHelpers<IFormikValues>) {
        try {
            const response = await createAuthRequest().post<IPageMetaData[]>('/pages/contact', values);

            await this.onUpdated(response);
        } catch (e) {
            await this.onError(e);
        }
    }

    private async onUpdated(response: AxiosResponse<IPageMetaData[]>) {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Updated',
            text: 'Contact settings were successfully updated.',
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
            text: `Unable to retrieve meta data: ${message}`,
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
        const { senderMessageTooltipOpen, recipientSubjectTooltipOpen, recipientMessageTooltipOpen } = this.state;

        return (
            <>
                <WaitToLoad ref={this._waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={this.getMetaData}>
                    {(metaData, err) => (
                        <>
                            {err !== undefined && this.handleError(err)}
                            {metaData !== undefined && (
                                <>
                                    <Formik<IFormikValues>
                                        validationSchema={this.schema}
                                        initialValues={this.getInitialFormValues(metaData)}
                                        onSubmit={this.onFormSubmitted}
                                    >
                                        {({ values, errors, touched, isSubmitting, setFieldValue }) => (
                                            <>
                                                <Form>
                                                    <Row>
                                                        <FormGroup tag="fieldset">
                                                            <legend>General Settings</legend>

                                                            <Col md={12}>
                                                                <FormGroup className='has-validation'>
                                                                    <Label for='name'>Sender Reply To:</Label>
                                                                    <Field
                                                                        as={Input}
                                                                        type='text'
                                                                        name='sender_replyto'
                                                                        id='sender_replyto'
                                                                        className={classNames({ 'is-invalid': errors.sender_replyto && touched.sender_replyto })}
                                                                    />
                                                                    <ErrorMessage name='sender_replyto' component='div' className='invalid-feedback' />

                                                                </FormGroup>
                                                            </Col>

                                                            <Col md={12}>
                                                                <FormGroup className='has-validation'>
                                                                    <Label for='name'>Sender Subject:</Label>
                                                                    <Field
                                                                        as={Input}
                                                                        type='text'
                                                                        name='sender_subject'
                                                                        id='sender_subject'
                                                                        className={classNames({ 'is-invalid': errors.sender_subject && touched.sender_subject })}
                                                                    />
                                                                    <ErrorMessage name='sender_subject' component='div' className='invalid-feedback' />

                                                                </FormGroup>
                                                            </Col>

                                                            <Col md={12}>
                                                                <FormGroup className='has-validation'>
                                                                    <Label for='sender_message'>
                                                                        <a href='#' id='senderMessageTooltip' className='text-decoration-none'>
                                                                            Sender Message:{' '}
                                                                            <FaInfoCircle />
                                                                        </a>
                                                                    </Label>
                                                                    <MarkdownEditor mode='auto' value={values.sender_message} onChange={(v) => setFieldValue('sender_message', v)} />
                                                                    <ErrorMessage name='sender_message' component='div' className='invalid-feedback' />

                                                                </FormGroup>
                                                                <Tooltip
                                                                    isOpen={senderMessageTooltipOpen}
                                                                    target="senderMessageTooltip"
                                                                    toggle={() => this.setState((prevState) => ({ senderMessageTooltipOpen: !prevState.senderMessageTooltipOpen }))}
                                                                >
                                                                    This is the message sent to the user confirming the message was sent.
                                                                </Tooltip>
                                                            </Col>

                                                            <Col md={12}>
                                                                <FormGroup className='has-validation'>
                                                                    <Label for='name'>Recipient Email:</Label>
                                                                    <Field
                                                                        as={Input}
                                                                        type='email'
                                                                        name='recipient_email'
                                                                        id='recipient_email'
                                                                        className={classNames({ 'is-invalid': errors.recipient_email && touched.recipient_email })}
                                                                    />
                                                                    <ErrorMessage name='recipient_email' component='div' className='invalid-feedback' />

                                                                </FormGroup>
                                                            </Col>

                                                            <Col md={12}>
                                                                <FormGroup className='has-validation'>
                                                                    <Label for='name'>
                                                                        <a href='#' id='recipientSubjectTooltip' className='text-decoration-none'>
                                                                            Recipient Subject:{' '}
                                                                            <FaInfoCircle />
                                                                        </a>
                                                                    </Label>
                                                                    <Field
                                                                        as={Input}
                                                                        type='text'
                                                                        name='recipient_subject'
                                                                        id='recipient_subject'
                                                                        className={classNames({ 'is-invalid': errors.recipient_subject && touched.recipient_subject })}
                                                                    />
                                                                    <ErrorMessage name='recipient_subject' component='div' className='invalid-feedback' />
                                                                    <Tooltip
                                                                        isOpen={recipientSubjectTooltipOpen}
                                                                        target="recipientSubjectTooltip"
                                                                        toggle={() => this.setState((prevState) => ({ recipientMessageTooltipOpen: !prevState.recipientMessageTooltipOpen }))}
                                                                    >
                                                                        This is sent to you (the webmaster).
                                                                    </Tooltip>
                                                                </FormGroup>
                                                            </Col>

                                                            <Col md={12}>
                                                                <FormGroup className='has-validation'>
                                                                    <Label for='recipient_template'>
                                                                        <a href='#' id='recipientMessageTooltip' className='text-decoration-none'>
                                                                            Recipient Message Template:{' '}
                                                                            <FaInfoCircle />
                                                                        </a>
                                                                    </Label>
                                                                    <MarkdownEditor mode='auto' value={values.recipient_template} onChange={(v) => setFieldValue('recipient_template', v)} />

                                                                    <FormText className='mt-1'>
                                                                        Available tags:
                                                                    </FormText>

                                                                    <List type='inline'>
                                                                        {
                                                                            ['date-time', 'subject', 'message', 'user-agent', 'ip-address', 'chuck-norris-fact'].map(
                                                                                (value, index) => (
                                                                                    <ListInlineItem key={index}>
                                                                                        <Badge color='info'>{`[${value}]`}</Badge>
                                                                                    </ListInlineItem>
                                                                                )
                                                                            )
                                                                        }
                                                                    </List>

                                                                    <ErrorMessage name='recipient_message' component='div' className='invalid-feedback' />

                                                                    <Tooltip
                                                                        isOpen={recipientMessageTooltipOpen}
                                                                        target="recipientMessageTooltip"
                                                                        toggle={() => this.setState((prevState) => ({ recipientMessageTooltipOpen: !prevState.recipientMessageTooltipOpen }))}
                                                                    >
                                                                        This is the message sent to the webmaster.
                                                                    </Tooltip>
                                                                </FormGroup>
                                                            </Col>

                                                        </FormGroup>

                                                        <FormGroup tag="fieldset">
                                                            <legend>Spam Controls</legend>

                                                            <div className='mb-2'>
                                                                <Row>
                                                                    <Col md={12}>
                                                                        <FormGroup check>
                                                                            <Field
                                                                                as={Input}
                                                                                type='checkbox'
                                                                                name='require_recaptcha'
                                                                                id='require_recaptcha'
                                                                                className={classNames({ 'is-invalid': errors.require_recaptcha && touched.require_recaptcha })}
                                                                            />
                                                                            {' '}
                                                                            <Label check htmlFor='require_recaptcha'>
                                                                                Require reCAPTCHA
                                                                            </Label>
                                                                            <ErrorMessage name='require_recaptcha' component='div' className='invalid-feedback' />
                                                                        </FormGroup>
                                                                    </Col>
                                                                </Row>

                                                                <Collapse isOpen={values.require_recaptcha}>
                                                                    <Row>
                                                                        <Col xs={12}>
                                                                            <p className='mb-1'>Ensure reCAPTCHA is setup in the &apos;config/recaptcha.php&apos; file.</p>
                                                                        </Col>
                                                                    </Row>
                                                                </Collapse>

                                                            </div>

                                                            <div className='mb-2'>

                                                                <Row>
                                                                    <Col md={12}>
                                                                        <FormGroup check>
                                                                            <Field
                                                                                as={Input}
                                                                                type='checkbox'
                                                                                name='require_confirmation'
                                                                                id='require_confirmation'
                                                                                className={classNames({ 'is-invalid': errors.require_confirmation && touched.require_confirmation })}
                                                                            />
                                                                            {' '}
                                                                            <Label check htmlFor='require_confirmation'>
                                                                                Require Confirmation Email
                                                                            </Label>
                                                                            <ErrorMessage name='require_confirmation' component='div' className='invalid-feedback' />
                                                                        </FormGroup>
                                                                    </Col>
                                                                </Row>

                                                                <Collapse isOpen={values.require_confirmation}>
                                                                    <Row>
                                                                        <Col md={4}>
                                                                            <FormGroup className='has-validation'>
                                                                                <Label for='confirmation_required_by'>Confirmation Required By:</Label>
                                                                                <Field
                                                                                    as={Input}
                                                                                    type='select'
                                                                                    name='confirmation_required_by'
                                                                                    id='confirmation_required_by'
                                                                                    className={classNames({ 'is-invalid': errors.confirmation_required_by && touched.confirmation_required_by })}
                                                                                >
                                                                                    <option value='all_users'>All Users</option>
                                                                                    <option value='unregistered_users'>Unregistered Users</option>
                                                                                    <option value='unregistered_unverified_users'>Unregistered and Unverified Users</option>
                                                                                </Field>
                                                                                <ErrorMessage name='confirmation_required_by' component='div' className='invalid-feedback' />

                                                                            </FormGroup>
                                                                        </Col>

                                                                        <Col md={8}>
                                                                            <FormGroup className='has-validation'>
                                                                                <Label for='confirmation_subject'>Confirmation Email Subject:</Label>
                                                                                <Field
                                                                                    as={Input}
                                                                                    type='text'
                                                                                    name='confirmation_subject'
                                                                                    id='confirmation_subject'
                                                                                    className={classNames({ 'is-invalid': errors.confirmation_subject && touched.confirmation_subject })}
                                                                                />
                                                                                <ErrorMessage name='confirmation_subject' component='div' className='invalid-feedback' />

                                                                            </FormGroup>
                                                                        </Col>
                                                                    </Row>
                                                                </Collapse>

                                                            </div>

                                                            <div className='mb-2'>
                                                                <Row>
                                                                    <Col md={12}>
                                                                        <FormGroup check>
                                                                            <Field
                                                                                as={Input}
                                                                                type='checkbox'
                                                                                name='honeypot_field'
                                                                                id='honeypot_field'
                                                                                className={classNames({ 'is-invalid': errors.honeypot_field && touched.honeypot_field })}
                                                                            />
                                                                            {' '}
                                                                            <Label check htmlFor='honeypot_field'>
                                                                                Honeypot Field
                                                                            </Label>
                                                                            <ErrorMessage name='honeypot_field' component='div' className='invalid-feedback' />
                                                                        </FormGroup>
                                                                    </Col>


                                                                </Row>

                                                                <Collapse isOpen={values.honeypot_field}>
                                                                    <Row>
                                                                        <Col md={12}>
                                                                            <FormGroup className='has-validation'>
                                                                                <Label for='honeypot_field_name'>Honeypot Field Name:</Label>
                                                                                <Field
                                                                                    as={Input}
                                                                                    type='text'
                                                                                    name='honeypot_field_name'
                                                                                    id='honeypot_field_name'
                                                                                    className={classNames({ 'is-invalid': errors.honeypot_field_name && touched.honeypot_field_name })}
                                                                                />
                                                                                <ErrorMessage name='honeypot_field_name' component='div' className='invalid-feedback' />

                                                                            </FormGroup>
                                                                        </Col>
                                                                    </Row>
                                                                </Collapse>

                                                            </div>

                                                        </FormGroup>
                                                    </Row>

                                                    <Row>
                                                        <Col className='text-end'>
                                                            <Button color='primary' type='submit' disabled={isSubmitting}>
                                                                Save Settings
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
