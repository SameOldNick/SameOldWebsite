import React from 'react';
import { Button, Col, FormGroup, Input, InputGroup, Label, Row } from 'reactstrap';
import { ErrorMessage, Field, Form, Formik, FormikProps } from 'formik';
import * as Yup from 'yup';
import classNames from 'classnames';
import awaitModalPrompt from '@admin/utils/modals';
import SelectFiltersModal from './SelectFiltersModal';
import S from 'string';

interface ISettingsProps {
    settings: ICommentSettings;
    onSave: (settings: ICommentSettings) => Promise<void>;
}

export interface ICommentSettings {
    user_authentication: 'guest_verified' | 'guest_unverified' | 'registered';
    comment_moderation: 'manual' | 'auto';
    use_captcha: 'guest' | 'all' | 'disabled';
    moderators: TFilters;
}

export type TFilters = string[];

type TFormikValues = Omit<ICommentSettings, 'moderators'>;

const Settings: React.FC<ISettingsProps> = ({ settings, onSave }) => {
    const formikRef = React.useRef<FormikProps<TFormikValues>>(null);

    const [filters, setFilters] = React.useState<TFilters>(settings.moderators);

    const schema = React.useMemo(() => Yup.object().shape({
        user_authentication: Yup.string().required().oneOf(['guest_verified', 'guest_unverified', 'registered']),
        comment_moderation: Yup.string().required().oneOf(['manual', 'auto', 'disabled']),
        use_captcha: Yup.string().required().oneOf(['guest', 'all', 'disabled']),
    }), []);

    const initialValues = React.useMemo<TFormikValues>(() => ({
        user_authentication: settings.user_authentication,
        comment_moderation: settings.comment_moderation,
        use_captcha: settings.use_captcha,
    }), [settings]);

    const handleSelectFiltersClicked = React.useCallback(async (e: React.MouseEvent) => {
        e.preventDefault();

        const filters = await awaitModalPrompt(SelectFiltersModal, { filters: settings.moderators });

        setFilters(filters);
    }, []);

    const handleFormSubmit = React.useCallback(async (values: TFormikValues) => {
        await onSave({ ...values, moderators: filters });
    }, [onSave, filters]);

    const displayedFilters = React.useMemo(() => filters.length > 0 ? filters.map((filter) => S(filter).humanize().s).join(', ') : '(None)', [filters]);

    return (
        <Formik
            innerRef={formikRef}
            validationSchema={schema}
            initialValues={initialValues}
            onSubmit={handleFormSubmit}
        >
            {({ values, dirty, touched, errors, isSubmitting }) => (
                <>
                    <Form>
                        <FormGroup row>
                            <Col md={6} className='has-validation'>
                                <Label for='user_authentication'>User Authentication</Label>
                                <Field as={Input} type='select' name='user_authentication' id='user_authentication' className={classNames({ 'is-invalid': errors.user_authentication && touched.user_authentication })}>
                                    <option value="guest_verified">Allow Guest Comments with Email Verification</option>
                                    <option value="guest_unverified">Allow Guest Comments without Email Verification</option>
                                    <option value="registered">Require Registration</option>
                                </Field>
                                <ErrorMessage name='user_authentication' component='div' className='invalid-feedback' />
                            </Col>

                            <Col md={6} className='has-validation'>
                                <Label for='comment_moderation'>Comment Moderation</Label>
                                <Field as={Input} type='select' name='comment_moderation' id='comment_moderation' className={classNames({ 'is-invalid': errors.comment_moderation && touched.comment_moderation })}>
                                    <option value="manual">Manual Approval</option>
                                    <option value="auto">Auto Approval</option>
                                    <option value="disabled">Disabled</option>
                                </Field>
                                <ErrorMessage name='comment_moderation' component='div' className='invalid-feedback' />
                            </Col>
                        </FormGroup>

                        <FormGroup row>
                            <Col md={6} className='has-validation'>
                                <Label for='use_captcha'>Use CAPTCHA</Label>
                                <Field as={Input} type='select' name='use_captcha' id='use_captcha' className={classNames({ 'is-invalid': errors.use_captcha && touched.use_captcha })}>
                                    <option value="guest">Guests</option>
                                    <option value="all">All Users</option>
                                    <option value="disabled">Disabled</option>
                                </Field>
                                <ErrorMessage name='use_captcha' component='div' className='invalid-feedback' />
                            </Col>

                            <Col md={6}>
                                <Label for='filters'>Filters</Label>
                                <InputGroup>
                                    <Input type='text' readOnly value={displayedFilters} />
                                    <Button type='button' color='primary' onClick={handleSelectFiltersClicked}>Select...</Button>
                                </InputGroup>
                            </Col>
                        </FormGroup>

                        <Row className='justify-content-end'>
                            <Col xs='auto'>
                                <Button type='submit' color='primary' disabled={isSubmitting}>Save Settings</Button>
                            </Col>
                        </Row>
                    </Form>
                </>
            )}

        </Formik>
    );
}

export default Settings;
