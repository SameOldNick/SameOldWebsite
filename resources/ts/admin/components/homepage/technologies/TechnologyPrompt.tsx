import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import classNames from 'classnames';
import * as Yup from 'yup';

import IconSelector, { IIconType } from '@admin/components/icon-selector/IconSelector';
import { lookupIcon } from '@admin/components/icon-selector/utils';
import Icon from '@admin/components/icon-selector/Icon';

interface IFormikValues {
    technology: string;
}

interface IProps {
    technology?: ITechnology;
    onSubmitted: (technology: ITechnology) => Promise<void>;
    onClose: () => void;
}

const TechnologyPrompt: React.FC<IProps> = ({ technology, onSubmitted, onClose }) => {
    const [iconSelector, setIconSelector] = React.useState(false);
    const [selectedIcon, setSelectedIcon] = React.useState<IIconType | undefined>(technology !== undefined ? lookupIcon(technology.icon) : undefined);

    const handleSubmit = async (values: IFormikValues, { }: FormikHelpers<IFormikValues>) => {
        await onSubmitted({
            id: technology?.id,
            icon: `${selectedIcon?.prefix}-${selectedIcon?.name}`,
            technology: values.technology
        });

        onClose();
    }

    const schema = React.useMemo(() =>
        Yup.object().shape({
            technology: Yup.string().required('Technology is required').max(255),
        })
    , []);

    const initialValues = React.useMemo<IFormikValues>(() => ({ technology: technology?.technology || '' }), [technology]);

    const handleIconSelect = (icon: IIconType) => {
        setSelectedIcon(icon);

        setIconSelector(false);
    }

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
                                    {technology ? 'Update Technology' : 'Add Technology'}
                                </ModalHeader>
                                <ModalBody>
                                    <Row className="mb-3">
                                        <Col xs={12} className='text-center mb-3'>
                                            {selectedIcon && <Icon icon={selectedIcon} size={48} />}
                                        </Col>
                                        <Col xs={12} className='text-center'>
                                            <Button onClick={() => setIconSelector(true)}>Choose Icon...</Button>
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
                                        {technology ? 'Update' : 'Create'}
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
        </>
    );
}

export default TechnologyPrompt;
