import React, { useRef } from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers, FormikProps } from 'formik';
import { Button, Col, FormFeedback, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';

import classNames from 'classnames';
import * as Yup from 'yup';
import CodeMirror from 'codemirror';
import useJavascript from "codemirror-ssr/mode/javascript/javascript";

import { IPromptModalProps } from '@admin/utils/modals';
import CodeEditor from '@admin/components/CodeEditor';

interface BlacklistEntry {
    input: 'name' | 'email';
    type: 'static' | 'regex';
    value: string;
}

type FormikValues = BlacklistEntry;

type CreatePromptProps = IPromptModalProps<BlacklistEntry>

const CreatePrompt = ({ onSuccess, onCancelled }: CreatePromptProps) => {
    const formikRef = useRef<FormikProps<FormikValues>>(null);

    const handleSubmit = React.useCallback(async (values: FormikValues, _helpers: FormikHelpers<FormikValues>) => {
        await onSuccess(values);
    }, [onSuccess]);

    const handleCodeMirrorLoaded = React.useCallback((cm: typeof CodeMirror) => {
        useJavascript(cm);
    }, []);

    const handleCodeMirrorEditorCreated = React.useCallback((editor: CodeMirror.EditorFromTextArea) => {
        editor.on('change', (cm) => {
            formikRef.current?.setFieldValue('value', cm.getValue());
        })
    }, [formikRef]);

    const schema = React.useMemo(() => Yup.object().shape({
        input: Yup.string().oneOf(['name', 'email']),
        type: Yup.string().oneOf(['static', 'regex']),
        value: Yup.string().required().min(1).max(255).when('type', {
            is: 'regex',
            then: (schema) => schema.test({
                name: 'is-regexp',
                message: 'You must enter a valid regular expression pattern.',
                test: (pattern) => {
                    try {
                        // A regex pattern to check if it's a regex pattern :D
                        // Source: https://stackoverflow.com/a/66769811/533242
                        const match = pattern.match(/^([/~@;%#'])(.*?)\1([gimsuy]*)$/);
                        return match ? !!new RegExp(match[2], match[3]) : false;
                    } catch (err) {
                        return false
                    }
                }
            }),
            otherwise: (schema) => schema.when('input', {
                is: 'email',
                then: (schema) => schema.email()
            })
        }),
    }), []);

    const initialValues = React.useMemo<FormikValues>(() => ({
        input: 'name',
        type: 'static',
        value: ''
    }), []);

    return (
        <>
            <Modal isOpen backdrop='static'>
                <Formik
                    innerRef={formikRef}
                    validationSchema={schema}
                    initialValues={initialValues}
                    onSubmit={handleSubmit}
                >
                    {({ values, errors, touched }) => (
                        <>
                            <Form>

                                <ModalHeader>
                                    Add Blacklist Entry
                                </ModalHeader>
                                <ModalBody>
                                    <FormGroup row className='mb-3 has-validation'>
                                        <Label for='input' xs={3} className='text-end'>Input:</Label>
                                        <Col xs={9}>
                                            <Field
                                                as={Input}
                                                type='select'
                                                name='input'
                                                id='input'
                                                className={classNames({ 'is-invalid': errors.input && touched.input })}
                                            >
                                                <option value='name'>Name</option>
                                                <option value='email'>E-mail address</option>
                                            </Field>
                                            <ErrorMessage name='input' component={FormFeedback} />
                                        </Col>

                                    </FormGroup>

                                    <FormGroup row className='has-validation'>
                                        <Label for='type' xs={3} className='text-end'>Type:</Label>
                                        <Col xs={9}>
                                            <Field
                                                as={Input}
                                                type='select'
                                                name='type'
                                                id='type'
                                                className={classNames({ 'is-invalid': errors.type && touched.type })}
                                            >
                                                <option value='static'>Static value</option>
                                                <option value='regex'>RegEx pattern</option>
                                            </Field>
                                            <ErrorMessage name='type' component={FormFeedback} />
                                        </Col>

                                    </FormGroup>

                                    <FormGroup row className='has-validation'>
                                        <Label for='value' xs={3} className='text-end'>
                                            {values.type === 'regex' ? 'Pattern:' : 'Value:'}
                                        </Label>
                                        <Col xs={9}>
                                            {values.type === 'regex' && (
                                                <div
                                                    className={classNames('cm-single-line-wrapper', { 'is-invalid': errors.value && touched.value })}
                                                >
                                                    <CodeEditor
                                                        onCodeMirrorLoaded={handleCodeMirrorLoaded}
                                                        onCodeMirrorEditorCreated={handleCodeMirrorEditorCreated}
                                                        options={{
                                                            value: '//',
                                                            lineNumbers: false, // No line numbers
                                                            lineWrapping: false, // Prevent wrapping
                                                            extraKeys: {
                                                                "Enter": () => {
                                                                    // Prevent newline creation on Enter
                                                                }
                                                            }
                                                        }}
                                                    />
                                                </div>
                                            )}

                                            {values.type === 'static' && (
                                                <Field
                                                    as={Input}
                                                    type='text'
                                                    name='value'
                                                    id='value'
                                                    className={classNames({ 'is-invalid': errors.value && touched.value })}
                                                />
                                            )}

                                            <ErrorMessage name='value' component={FormFeedback} />
                                        </Col>

                                    </FormGroup>
                                </ModalBody>
                                <ModalFooter>
                                    <Button color="primary" type='submit' disabled={Object.keys(errors).length > 0}>
                                        Create
                                    </Button>
                                    {' '}
                                    <Button color="secondary" onClick={() => onCancelled()}>
                                        Cancel
                                    </Button>
                                </ModalFooter>
                            </Form>
                        </>
                    )}
                </Formik>

            </Modal>
        </>
    );
}

export default CreatePrompt;
export type { CreatePromptProps, BlacklistEntry };