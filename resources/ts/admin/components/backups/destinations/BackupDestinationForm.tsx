import React from 'react';
import { Button, Col, FormGroup, Input, Label, Row } from 'reactstrap';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';

import * as Yup from 'yup';
import classNames from 'classnames';

import Loader from '@admin/components/Loader';
import FormikAlerts from '@admin/components/alerts/hoc/FormikAlerts';

interface BackupDestinationFormValues {
    enable: boolean;
    name: string;
    type: 'ftp' | 'sftp';
    host: string;
    port: number;
    auth_type?: 'password' | 'key';
    username: string;
    password: string | null;
    confirm_password: string | null;
    private_key: string | null;
    passphrase: string | null;
}

interface BackupDestinationFormProps {
    existing?: BackupDestinationFormValues;
    onSubmit: (values: BackupDestinationFormValues) => Promise<void>;
    onError: (err: unknown) => Promise<void>;
}

const BackupDestinationForm: React.FC<BackupDestinationFormProps> = ({ existing, onSubmit, onError }) => {
    const [enabled, setEnabled] = React.useState(existing ? existing.enable : false);

    const initialValues = React.useMemo<BackupDestinationFormValues>(() => existing ?? ({
        enable: false,
        name: '',
        type: 'ftp',
        host: '',
        port: 21,
        auth_type: 'password',
        username: '',
        password: '',
        confirm_password: '',
        private_key: null,
        passphrase: null
    }), [existing]);

    const schema = React.useMemo(() =>
        !existing ? Yup.object().shape({
            name: Yup.string().required('The destination name is required.').max(255).matches(/^[a-z0-9]+(?:-[a-z0-9]+)*$/, 'The name must be slug-i-fied'),
            type: Yup.string().required('The destination type is required.').oneOf(['ftp', 'sftp']),
            host: Yup.string().required('The destination host is required.').max(255),
            port: Yup.number().min(1, 'The destination port must be between 1 and 65535.').max(65535, 'The destination port must be between 1 and 65535.'),
            auth_type: Yup.string().when('type', {
                is: (type: string) => type === 'sftp',
                then: (schema) => schema.required('Authorization type is required when "SFTP" is selected.').oneOf(['password', 'key']),
                otherwise: (schema) => schema.nullable()
            }),
            username: Yup.string().required('The username is required.').max(255),
            password: Yup.string().when(['type', 'auth_type'], {
                is: (type: string, authType: string) => (type === 'sftp' && authType === 'password') || (type === 'ftp'),
                then: (schema) => schema.required('Password is required.').max(255),
                otherwise: (schema) => schema.nullable()
            }),
            confirm_password: Yup.string().when(['type', 'auth_type'], {
                is: (type: string, authType: string) => (type === 'sftp' && authType === 'password') || (type === 'ftp'),
                then: (schema) => schema.required('Confirmation password is required.').oneOf([Yup.ref('password')], 'Your passwords do not match.'),
                otherwise: (schema) => schema.nullable()
            }),
            private_key: Yup.string().when(['type', 'auth_type'], {
                is: (type: string, authType: string) => type === 'sftp' && authType === 'key',
                then: (schema) => schema.required('Private key is required.'),
                otherwise: (schema) => schema.nullable()
            }),
            passphrase: Yup.string().when(['type', 'auth_type'], {
                is: (type: string, authType: string) => type === 'sftp' && authType === 'key',
                then: (schema) => schema.optional().max(255),
                otherwise: (schema) => schema.nullable()
            }),
        }) : Yup.object().shape({
            name: Yup.string().required('The destination name is required.').max(255).matches(/^[a-z0-9]+(?:-[a-z0-9]+)*$/, 'The name must be slug-i-fied'),
            type: Yup.string().required('The destination type is required.').oneOf(['ftp', 'sftp']),
            host: Yup.string().required('The destination host is required.').max(255),
            port: Yup.number().min(1, 'The destination port must be between 1 and 65535.').max(65535, 'The destination port must be between 1 and 65535.'),
            auth_type: Yup.string().when('type', {
                is: (type: string) => type === 'sftp',
                then: (schema) => schema.required('Authorization type is required when "SFTP" is selected.').oneOf(['password', 'key']),
                otherwise: (schema) => schema.nullable()
            }),
            username: Yup.string().required('The username is required.').max(255),
            password: Yup.string().nullable().max(255),
            confirm_password: Yup.string().when(['password', 'type', 'auth_type'], {
                is: (password: string, type: string, authType: string) => password && ((type === 'sftp' && authType === 'password') || (type === 'ftp')),
                then: (schema) => schema.required('Your passwords do not match.').oneOf([Yup.ref('password')], 'Your passwords do not match.'),
                otherwise: (schema) => schema.nullable()
            }),
            private_key: Yup.string().nullable(),
            passphrase: Yup.string().nullable(),
        }), []);

    const handleSubmit = React.useCallback(async (
        values: BackupDestinationFormValues,
        { setSubmitting }: FormikHelpers<BackupDestinationFormValues>
    ) => {
        try {
            setSubmitting(true);

            if ((values.auth_type === 'password' && !values.password) ||
                (values.auth_type === 'key' && !values.private_key)) {
                values.auth_type = undefined;
            }

            await onSubmit({
                ...values,
                enable: enabled
            });
        } catch (err) {
            await onError(err);
        } finally {
            setSubmitting(false);
        }
    }, [enabled]);

    return (
        <>
            <Formik<BackupDestinationFormValues>
                initialValues={initialValues}
                validationSchema={schema}
                onSubmit={handleSubmit}
            >
                {({ errors, touched, isSubmitting, values }) => (
                    <>
                        {isSubmitting && <Loader display={{ type: 'over-element' }} />}
                        <Form>
                            <Row>
                                <Col xs={12}>
                                    <FormikAlerts errors={errors} />
                                </Col>
                                <Col xs={12}>
                                    <FormGroup switch>
                                        {/* The switch isn't hooked into formik because it causes all error messages to appear when first checked */}
                                        <Input type="switch" role="switch" name="enable" checked={enabled} onChange={() => setEnabled((value) => !value)} />
                                        <Label check>Enable as backup destination</Label>
                                    </FormGroup>
                                </Col>
                                <Col md={8}>
                                    <FormGroup className='has-validation'>
                                        <Label for="name">Name</Label>
                                        <Field as={Input} type="text" name="name" className={classNames({ 'is-invalid': errors.name && touched.name })} />
                                        <ErrorMessage name='name' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>
                                <Col md={4}>
                                    <FormGroup className='has-validation'>
                                        <Label for="type">Type</Label>
                                        <Field as={Input} type='select' name='type' className={classNames({ 'is-invalid': errors.type && touched.type })}>
                                            <option value="ftp">FTP</option>
                                            <option value="sftp">SFTP</option>
                                        </Field>
                                        <ErrorMessage name='type' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>
                                <Col md={8}>
                                    <FormGroup className='has-validation'>
                                        <Label for="host">Host</Label>
                                        <Field as={Input} type="text" name="host" className={classNames({ 'is-invalid': errors.host && touched.host })} />
                                        <ErrorMessage name='host' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>
                                <Col md={4}>

                                    <FormGroup className='has-validation'>
                                        <Label for="port">Port</Label>
                                        <Field as={Input} type="number" name="port" min={1} max={65535} className={classNames({ 'is-invalid': errors.port && touched.port })} />
                                        <ErrorMessage name='port' component='div' className='invalid-feedback' />
                                    </FormGroup>

                                </Col>
                                <Col md={12}>

                                    <FormGroup className='has-validation'>
                                        <Label for="username">Username</Label>
                                        <Field as={Input} type="text" name="username" className={classNames({ 'is-invalid': errors.username && touched.username })} />
                                        <ErrorMessage name='username' component='div' className='invalid-feedback' />
                                    </FormGroup>

                                    {values.type === 'sftp' && (
                                        <>
                                            <FormGroup className='has-validation'>
                                                <Label for="auth_type">Auth Type</Label>
                                                <Field as={Input} type='select' name='auth_type' className={classNames({ 'is-invalid': errors.auth_type && touched.auth_type })}>
                                                    <option value="password">Password</option>
                                                    <option value="key">Key</option>
                                                </Field>
                                                <ErrorMessage name='auth_type' component='div' className='invalid-feedback' />
                                            </FormGroup>
                                        </>
                                    )}

                                    {((values.type === 'sftp' && values.auth_type === 'password') || (values.type === 'ftp')) && (
                                        <>
                                            <FormGroup className='has-validation'>
                                                <Label for="password">Password</Label>
                                                <Field
                                                    as={Input}
                                                    type="password"
                                                    name="password"
                                                    placeholder={existing ? '(Unchanged)' : ''}
                                                    className={classNames({ 'is-invalid': errors.password && touched.password })}
                                                />
                                                <ErrorMessage name='password' component='div' className='invalid-feedback' />
                                            </FormGroup>
                                            {((existing && values.password) || !existing) && (
                                                <FormGroup className='has-validation'>
                                                    <Label for="confirm_password">Confirm Password</Label>
                                                    <Field
                                                        as={Input}
                                                        type="password"
                                                        name="confirm_password"
                                                        className={classNames({ 'is-invalid': errors.confirm_password && touched.confirm_password })}
                                                    />
                                                    <ErrorMessage name='confirm_password' component='div' className='invalid-feedback' />
                                                </FormGroup>
                                            )}
                                        </>
                                    )}
                                    {values.type === 'sftp' && values.auth_type === 'key' && (
                                        <>
                                            <FormGroup className='has-validation'>
                                                <Label for="private_key">Private Key</Label>
                                                <Field as={Input} type='textarea' name="private_key" className={classNames({ 'is-invalid': errors.private_key && touched.private_key })} />
                                                <ErrorMessage name='private_key' component='div' className='invalid-feedback' />
                                            </FormGroup>
                                            <FormGroup className='has-validation'>
                                                <Label for="passphrase">Passphrase (optional)</Label>
                                                <Field as={Input} type="text" name="passphrase" className={classNames({ 'is-invalid': errors.passphrase && touched.passphrase })} />
                                                <ErrorMessage name='passphrase' component='div' className='invalid-feedback' />
                                            </FormGroup>
                                        </>
                                    )}
                                </Col>

                                <Col className='text-end'>
                                    <Button color="primary" type='submit' disabled={isSubmitting || Object.entries(errors).length > 0}>
                                        Save Backup Destination
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

export default BackupDestinationForm;
export { BackupDestinationFormValues }
