import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import classNames from 'classnames';
import * as Yup from 'yup';

import IconSelector from '@admin/components/icon-selector/IconSelector';
import { lookupIcon, IIconType } from '@admin/components/icon-selector/utils';
import Icon from '@admin/components/icon-selector/Icon';
import { IPromptModalProps } from '@admin/utils/modals';

interface IFormikValues {
    technology: string;
}

interface IProps extends IPromptModalProps<ITechnology> {
    existing?: ITechnology;
}

const TechnologyPrompt: React.FC<IProps> = ({ existing, onSuccess, onCancelled }) => {
    const [iconSelector, setIconSelector] = React.useState(false);
    const [selectedIcon, setSelectedIcon] = React.useState<IIconType | undefined>(existing !== undefined ? lookupIcon(existing.icon) : undefined);

    const handleSubmit = React.useCallback(async (values: IFormikValues, { }: FormikHelpers<IFormikValues>) => {
        await onSuccess({
            id: existing?.id,
            icon: `${selectedIcon?.prefix}-${selectedIcon?.name}`,
            technology: values.technology
        });
    }, [existing, selectedIcon]);

    const schema = React.useMemo(() =>
        Yup.object().shape({
            technology: Yup.string().required('Technology is required').max(255),
        })
    , []);

    const initialValues = React.useMemo<IFormikValues>(() => ({ technology: existing?.technology || '' }), [existing]);

    const handleIconSelect = React.useCallback((icon: IIconType) => {
        setSelectedIcon(icon);

        setIconSelector(false);
    }, []);

    return (
        <>
            {iconSelector && (
                <IconSelector
                    open
                    onSave={handleIconSelect}
                    onCancel={() => setIconSelector(false)}
                />
            )}
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
                                    {existing ? 'Update Technology' : 'Add Technology'}
                                </ModalHeader>
                                <ModalBody>
                                    <Row className="mb-3">
                                        <Col xs={12} className='text-center mb-3'>
                                            {selectedIcon && <Icon icon={selectedIcon} size={48} />}
                                        </Col>
                                        <Col xs={12} className='text-center'>
                                            <Button color='primary' onClick={() => setIconSelector(true)}>Choose Icon...</Button>
                                        </Col>
                                    </Row>

                                    <FormGroup row className='has-validation'>
                                        <Label for='link' xs={3} className='text-end'>Technology:</Label>
                                        <Col xs={9}>
                                            <Field
                                                as={Input}
                                                type='text'
                                                name='technology'
                                                id='technology'
                                                className={classNames({ 'is-invalid': errors.technology && touched.technology })}
                                            />
                                            <ErrorMessage name='technology' component='div' className='invalid-feedback' />
                                        </Col>

                                    </FormGroup>
                                </ModalBody>
                                <ModalFooter>
                                    <Button color="primary" type='submit' disabled={selectedIcon === undefined || Object.keys(errors).length > 0}>
                                        {existing ? 'Update' : 'Create'}
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

export default TechnologyPrompt;
