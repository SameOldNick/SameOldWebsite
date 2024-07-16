import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers, FormikProps } from 'formik';
import { Button, Col, FormGroup, Input, InputGroup, Label, Row } from 'reactstrap';

import * as Yup from 'yup';
import classNames from 'classnames';

import Countries from './Countries';
import States from './States';
import SelectRolesModal from './SelectRolesModal';

import awaitModalPrompt from '@admin/utils/modals';

export interface IFormikValues {
    name: string;
    email: string;
    password: string;
    confirm_password: string;
    state: string;
    country: string;
    roles: TRole[];
}

type TFormikProps = React.ComponentProps<typeof Formik<IFormikValues>>;

interface IProps  {
    fields: 'create' | 'edit';
    buttonContent: React.ReactNode;
    onSubmit: (values: IFormikValues, helpers: Parameters<TFormikProps['onSubmit']>[1]) => Promise<void>;
}

export type TForwardedRef = FormikProps<IFormikValues>;

type TProps = IProps & Omit<TFormikProps, 'onSubmit' | 'innerRef'>;

const UserForm = React.forwardRef<TForwardedRef, TProps>(({ buttonContent, fields, onSubmit, ...props }, forwardedRef) => {
    const innerRef = React.useRef<TForwardedRef>();

    const schema = React.useMemo(() => {
        if (fields === 'create') {
            return Yup.object().shape({
                name: Yup.string().optional(),
                email: Yup.string().required('E-mail is required').email(),
                password: Yup.string().required('Password is required'),
                confirm_password: Yup.string().required('Please confirm your password').oneOf([Yup.ref('password')], 'Your passwords do not match.'),
                state: Yup.string(),
                country: Yup.string().length(3),
                roles: Yup.array().optional().of(Yup.string())
            });
        } else {
            return Yup.object().shape({
                name: Yup.string().optional(),
                email: Yup.string().required('E-mail is required').email(),
                password: Yup.string().notRequired(),
                confirm_password: Yup.string().oneOf([Yup.ref('password')], 'Your passwords do not match.'),
                state: Yup.string(),
                country: Yup.string().length(3),
                roles: Yup.array().optional().of(Yup.string())
            });
        }
    }, [fields]);

    const handleSubmit = React.useCallback(async (values: IFormikValues, helpers: FormikHelpers<IFormikValues>) => {
        await onSubmit({ ...values }, helpers);

        return Promise.resolve();
    }, [onSubmit]);

    const handleSelectRolesClicked = React.useCallback(async (e: React.MouseEvent, roles: TRole[]) => {
        e.preventDefault();

        const updated = await awaitModalPrompt(SelectRolesModal, { roles });

        innerRef.current?.setFieldValue('roles', updated);
    }, [innerRef.current]);

    const assignRef = (instance: TForwardedRef) => {
        innerRef.current = instance;

        if (typeof forwardedRef === "function")
            forwardedRef(instance);
        else if (forwardedRef !== null)
            forwardedRef.current = instance;
    };

    return (
        <>
            <Formik<IFormikValues> innerRef={assignRef} validationSchema={schema} onSubmit={handleSubmit} {...props}>
                {({ errors, touched, isSubmitting, values, ...helpers }) => (
                    <>
                        <Form>

                            <Row>
                                <Col md={6}>
                                    <FormGroup className='has-validation'>
                                        <Label for='name'>Name:</Label>
                                        <Field as={Input} type='text' name='name' id='name' className={classNames({ 'is-invalid': errors.name && touched.name })} />
                                        <ErrorMessage name='name' component='div' className='invalid-feedback' />

                                    </FormGroup>
                                </Col>
                                <Col md={6}>
                                    <FormGroup className='has-validation'>
                                        <Label for='email'>E-mail:</Label>
                                        <Field as={Input} type='email' name='email' id='email' className={classNames({ 'is-invalid': errors.email && touched.email })} />
                                        <ErrorMessage name='email' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>
                            </Row>

                            <Row>
                                <Col md={6}>
                                    <FormGroup className='has-validation'>
                                        <Label for='password'>Password:</Label>
                                        <Field as={Input} type='password' name='password' id='password' className={classNames({ 'is-invalid': errors.password && touched.password })} />
                                        <ErrorMessage name='password' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>

                                <Col md={6}>
                                    <FormGroup className='has-validation'>
                                        <Label for='confirm_password'>Confirm Password:</Label>
                                        <Field as={Input} type='password' name='confirm_password' id='confirm_password' className={classNames({ 'is-invalid': errors.confirm_password && touched.confirm_password })} />
                                        <ErrorMessage name='confirm_password' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>

                            </Row>
                            <Row>

                                <Col md={4}>
                                    <FormGroup className='has-validation'>
                                        <Label for='country'>Country:</Label>
                                        <Field as={Countries} name='country' id='country' className={classNames({ 'is-invalid': errors.country && touched.country })} />
                                        <ErrorMessage name='country' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>
                                <Col md={3}>
                                    <FormGroup className='has-validation'>
                                        <Label for='state'>State/Province:</Label>
                                        <Field as={States} name='state' id='state' optional country={values.country} className={classNames({ 'is-invalid': errors.state && touched.state })} />
                                        <ErrorMessage name='state' component='div' className='invalid-feedback' />
                                    </FormGroup>
                                </Col>
                                <Col md={5}>
                                    <FormGroup className='has-validation'>
                                        <Label for='roles'>Roles:</Label>

                                        <InputGroup>
                                            <Input type='text' readOnly value={`${values.roles.length} roles selected`} />
                                            <Button type='button' color='primary' onClick={(e) => handleSelectRolesClicked(e, values.roles)}>Select...</Button>
                                        </InputGroup>

                                        <ErrorMessage name='roles' component='div' className='invalid-feedback' />
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
});

export default UserForm;
