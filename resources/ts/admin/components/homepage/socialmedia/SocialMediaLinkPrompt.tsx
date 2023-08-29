import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';

import classNames from 'classnames';
import * as Yup from 'yup';

interface IFormikValues {
    link: string;
}

interface IProps {
    link?: TSocialMediaLink;
    onSubmitted: (newLink: string) => Promise<void>;
    onClose: () => void;
}

const SocialMediaLinkPrompt: React.FC<IProps> = ({ link, onSubmitted, onClose }) => {
    const handleSubmit = async ({ link }: IFormikValues, { }: FormikHelpers<IFormikValues>) => {
        await onSubmitted(link);

        onClose();
    }

    const schema = Yup.object().shape({
        link: Yup.string().required('Link is required').max(255),
    });

    const initialValues: IFormikValues = { link: link || '' };

    return (
        <Modal isOpen backdrop='static'>
            <Formik<IFormikValues>
                validationSchema={schema}
                initialValues={initialValues}
                onSubmit={handleSubmit}
            >
                {({ errors, touched }) => (
                    <>
                        <Form>

                            <ModalHeader>
                                {link ? 'Update Link' : 'Add Link'}
                            </ModalHeader>
                            <ModalBody>

                                <FormGroup className='has-validation'>
                                    <Label for='link'>Link:</Label>
                                    <Field
                                        as={Input}
                                        type='url'
                                        name='link'
                                        id='link'
                                        placeholder="http://"
                                        className={classNames({ 'is-invalid': errors.link && touched.link })}
                                    />
                                    <ErrorMessage name='link' component='div' className='invalid-feedback' />
                                </FormGroup>
                            </ModalBody>
                            <ModalFooter>
                                <Button color="primary" type='submit' disabled={Object.keys(errors).length > 0}>
                                    {link ? 'Update' : 'Create'}
                                </Button>
                                {' '}
                                <Button color="secondary" onClick={onClose}>
                                    Cancel
                                </Button>
                            </ModalFooter>
                        </Form>
                    </>
                )}
            </Formik>

        </Modal>
    );
}

export default SocialMediaLinkPrompt;
