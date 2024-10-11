import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import classNames from 'classnames';
import * as Yup from 'yup';

import IconSelectorModal from '@admin/components/icon-selector/IconSelectorModal';
import { IIconType } from '@admin/components/icon-selector/utils';
import Icon from '@admin/components/icon-selector/Icon';
import awaitModalPrompt, { IPromptModalProps } from '@admin/utils/modals';
import { IHasIconsFile, withIconsFile } from '@admin/components/icon-selector/WithIcons';

interface IFormikValues {
    skill: string;
}

interface IProps extends IPromptModalProps<ISkill> {
    existing?: ISkill;
}

type TProps = IProps & IHasIconsFile;

const SkillPrompt: React.FC<TProps> = ({ lookupIcon, existing, onSuccess, onCancelled }) => {
    const [selectedIcon, setSelectedIcon] = React.useState<IIconType | undefined>(existing !== undefined ? lookupIcon(existing.icon) : undefined);

    const handleSubmit = React.useCallback(async (values: IFormikValues, { }: FormikHelpers<IFormikValues>) => {
        await onSuccess({
            id: existing?.id,
            icon: `${selectedIcon?.prefix}-${selectedIcon?.name}`,
            skill: values.skill
        });
    }, [existing, onSuccess, selectedIcon]);

    const schema = React.useMemo(() => Yup.object().shape({
        skill: Yup.string().required('Skill is required').max(255),
    }), []);

    const initialValues = React.useMemo<IFormikValues>(() => ({ skill: existing?.skill || '' }), [existing]);

    const handleChooseIconClicked = React.useCallback(async () => {
        try {
            const icon = await awaitModalPrompt(IconSelectorModal);

            setSelectedIcon(icon);
        } catch (err) {
            // User cancelled modal
        }
    }, []);

    return (
        <>
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
                                    {existing ? 'Update Skill' : 'Add Skill'}
                                </ModalHeader>
                                <ModalBody>
                                    <Row className="mb-3">
                                        <Col xs={12} className='text-center mb-3'>
                                            {selectedIcon && <Icon icon={selectedIcon} size={48} />}
                                        </Col>
                                        <Col xs={12} className='text-center'>
                                            <Button color='primary' onClick={handleChooseIconClicked}>Choose Icon...</Button>
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

export default withIconsFile(SkillPrompt);
