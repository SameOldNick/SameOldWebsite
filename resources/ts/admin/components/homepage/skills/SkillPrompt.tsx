import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import classNames from 'classnames';
import * as Yup from 'yup';

import IconSelector, { IIconType } from '@admin/components/icon-selector/IconSelector';
import { lookupIcon } from '@admin/components/icon-selector/utils';
import Icon from '@admin/components/icon-selector/Icon';

interface IFormikValues {
    skill: string;
}

interface IProps {
    skill?: ISkill;
    onSubmitted: (skill: ISkill) => Promise<void>;
    onClose: () => void;
}

const SkillPrompt: React.FC<IProps> = ({ skill, onSubmitted, onClose }) => {
    const [iconSelector, setIconSelector] = React.useState(false);
    const [selectedIcon, setSelectedIcon] = React.useState<IIconType | undefined>(skill !== undefined ? lookupIcon(skill.icon) : undefined);

    const handleSubmit = async (values: IFormikValues, { }: FormikHelpers<IFormikValues>) => {
        await onSubmitted({
            id: skill?.id,
            icon: `${selectedIcon?.prefix}-${selectedIcon?.name}`,
            skill: values.skill
        });

        onClose();
    }

    const schema = React.useMemo(() =>
        Yup.object().shape({
            skill: Yup.string().required('Skill is required').max(255),
        })
    , []);

    const initialValues = React.useMemo<IFormikValues>(() => ({ skill: skill?.skill || '' }), [skill]);

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
                                    {skill ? 'Update Skill' : 'Add Skill'}
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
                                        <Label for='link' xs={2} className='text-end'>Skill:</Label>
                                        <Col xs={10}>
                                            <Field
                                                as={Input}
                                                type='text'
                                                name='skill'
                                                id='skill'
                                                className={classNames({ 'is-invalid': errors.skill && touched.skill })}
                                            />
                                            <ErrorMessage name='skill' component='div' className='invalid-feedback' />
                                        </Col>

                                    </FormGroup>
                                </ModalBody>
                                <ModalFooter>
                                    <Button color="primary" type='submit' disabled={selectedIcon === undefined || Object.keys(errors).length > 0}>
                                        {skill ? 'Update' : 'Create'}
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

export default SkillPrompt;
