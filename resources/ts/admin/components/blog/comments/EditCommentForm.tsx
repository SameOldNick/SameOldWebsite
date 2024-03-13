import React from 'react';
import withReactContent from 'sweetalert2-react-content';
import { FaCheckCircle, FaExternalLinkAlt, FaSave, FaTimesCircle } from 'react-icons/fa';
import { Badge, Button, Card, CardBody, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, FormGroup, Input, InputGroup, Label, Row } from 'reactstrap';
import { ErrorMessage, Field, Form, Formik, FormikProps } from 'formik';

import Heading, { HeadingTitle } from '@admin/layouts/admin/Heading';

import axios from 'axios';
import classNames from 'classnames';
import S from 'string';
import Swal from 'sweetalert2';
import * as Yup from 'yup';

import { approve, deny, loadOne, update } from '@admin/utils/api/endpoints/comments';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

import Comment, { TCommentStatuses } from '@admin/utils/api/models/Comment';

interface IEditCommentFormProps {
    comment: Comment;
    setComment: (comment: Comment) => void;
}

interface IFormikValues {
    title: string;
    comment: string;
    status: TCommentStatuses;
}

const EditCommentForm: React.FC<IEditCommentFormProps> = ({ comment, setComment }) => {
    const formikRef = React.createRef<FormikProps<IFormikValues>>();

    const [buttonDropdownOpen, setButtonDropdownOpen] = React.useState(false);

    const schema =
        React.useMemo(
            () => Yup.object().shape({
                title: Yup.string().max(255),
                comment: Yup.string().required('Comment is required'),
                status: Yup.string().oneOf(['approved', 'denied', 'deleted'])
            }),
            []
        );

    const initialValues =
        React.useMemo(
            () => ({
                title: comment.comment.title || '',
                comment: comment.comment.comment,
                status: comment.status
            }),
            [comment]
        );

    const confirmSave = React.useCallback(async () => {
        const response = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Speak now or forever hold your peace.`,
            showConfirmButton: true,
            showCancelButton: true
        });

        return response.isConfirmed;
    }, []);

    const handleSave = React.useCallback(async ({ title, comment: content }: IFormikValues) => {
        try {
            if (!await confirmSave())
                return;

            const updated = await update(comment, title.trim() || null, content);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been saved.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            setComment(updated);
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred saving comment: ${message}\nPlease try again.`,
                showConfirmButton: false,
                showCancelButton: true
            });
        }
    }, [setComment]);

    const handleSaveAndApprove = React.useCallback(async ({ title, comment: content }: IFormikValues) => {
        try {
            if (!await confirmSave())
                return;

            const updated = await update(comment, title.trim() || null, content);

            await approve(comment);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been saved and approved.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            setComment(updated);
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred saving and approving comment: ${message}\nPlease try again.`,
                showConfirmButton: false,
                showCancelButton: true
            });
        }
    }, [setComment]);

    const handleSaveAndDeny = React.useCallback(async ({ title, comment: content }: IFormikValues) => {
        try {
            if (!await confirmSave())
                return;

            const updated = await update(comment, title.trim() || null, content);

            await deny(comment);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been saved and denied.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            setComment(updated);
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred saving and denying comment: ${message}\nPlease try again.`,
                showConfirmButton: false,
                showCancelButton: true
            });
        }
    }, [setComment]);

    const handleApprove = React.useCallback(async () => {
        try {
            if (!comment.comment.id)
                throw new Error('Comment ID is missing.');

            if (!await confirmSave())
                return;

            await approve(comment);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been approved.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            const updated = await loadOne(comment.comment.id);
            setComment(updated);
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred approving comment: ${message}\nPlease try again.`,
                showConfirmButton: false,
                showCancelButton: true
            });
        }
    }, [setComment]);

    const handleDeny = React.useCallback(async () => {
        try {
            if (!comment.comment.id)
                throw new Error('Comment ID is missing.');

            if (!await confirmSave())
                return;

            await deny(comment);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been denied.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            const updated = await loadOne(comment.comment.id);
            setComment(updated);
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred denying comment: ${message}\nPlease try again.`,
                showConfirmButton: false,
                showCancelButton: true
            });
        }
    }, [setComment]);

    return (
        <>
            <Formik<IFormikValues>
                innerRef={formikRef}
                validationSchema={schema}
                initialValues={initialValues}
                onSubmit={handleSave}
            >
                {({ values, dirty, touched, errors }) => (
                    <>
                        <Heading>
                            <HeadingTitle>
                                Edit Comment
                                {dirty && (
                                    <small className='ms-1 text-body-secondary'>
                                        <Badge color='secondary'>Unsaved Changes</Badge>
                                    </small>
                                )}
                            </HeadingTitle>

                            <div className='d-flex'>
                                <Button
                                    color="info"
                                    className='me-1'
                                    onClick={() => {}}
                                >
                                    <FaExternalLinkAlt />{' '}
                                    Preview
                                </Button>
                                <Dropdown toggle={() => setButtonDropdownOpen((prev) => !prev)} isOpen={buttonDropdownOpen} className='me-1'>
                                    <DropdownToggle caret color='primary'>
                                        <FaSave />{' '}
                                        Save
                                    </DropdownToggle>
                                    <DropdownMenu>
                                            <>
                                                <DropdownItem type='submit'>Save</DropdownItem>

                                                {comment.status !== Comment.STATUS_APPROVED && (
                                                    <DropdownItem onClick={() => handleSaveAndApprove(values)}>Save &amp; Approve</DropdownItem>
                                                )}

                                                {comment.status !== Comment.STATUS_DENIED && (
                                                    <DropdownItem onClick={() => handleSaveAndDeny(values)}>Save &amp; Deny</DropdownItem>
                                                )}
                                            </>
                                    </DropdownMenu>
                                </Dropdown>
                                {(comment.status === Comment.STATUS_AWAITING || comment.status === Comment.STATUS_DENIED) && (
                                    <Button
                                        color="success"
                                        className='me-1'
                                        onClick={() => handleApprove()}
                                    >
                                        <FaCheckCircle />{' '}
                                        Approve
                                    </Button>
                                )}

                                {(comment.status === Comment.STATUS_AWAITING || comment.status === Comment.STATUS_APPROVED) && (
                                    <Button
                                        color="danger"
                                        onClick={() => handleDeny()}
                                    >
                                        <FaTimesCircle />{' '}
                                        Deny
                                    </Button>
                                )}
                            </div>
                        </Heading>
                        <Form>
                            <Row>
                                <Col md={9}>
                                    <Card>
                                        <CardBody>
                                            <Row>
                                                <Col md={12}>
                                                    <FormGroup className='has-validation'>
                                                        <Label for='title'>Title:</Label>
                                                        <Field
                                                            as={Input}
                                                            type='text'
                                                            name='title'
                                                            id='title'
                                                            className={classNames({ 'is-invalid': errors.title && touched.title })}
                                                        />
                                                        <ErrorMessage name='title' component='div' className='invalid-feedback' />
                                                    </FormGroup>
                                                </Col>

                                                <Col md={12}>
                                                    <FormGroup className='has-validation'>
                                                        <Label for='comment'>Comment:</Label>
                                                        <Field
                                                            as={Input}
                                                            type='textarea'
                                                            name='comment'
                                                            id='comment'
                                                            className={classNames({ 'is-invalid': errors.comment && touched.comment })}
                                                            rows={8}
                                                        />
                                                        <ErrorMessage name='comment' component='div' className='invalid-feedback' />
                                                    </FormGroup>
                                                </Col>


                                            </Row>
                                        </CardBody>
                                    </Card>
                                </Col>
                                <Col md={3}>
                                    <Card>
                                        <CardBody>
                                            <Row>
                                                <Col xs={12}>
                                                    <FormGroup className='has-validation'>
                                                        <Label for='comment'>Status:</Label>
                                                        <Input plaintext value={S(comment.status).humanize().s} />
                                                    </FormGroup>
                                                </Col>

                                                <Col xs={12}>
                                                    <FormGroup className='has-validation'>
                                                        <Label for='comment'>Parent Comment:</Label>
                                                        <InputGroup>
                                                            <Input readOnly value={comment.comment.parent_id ?? '(N/A)'} />

                                                            {comment.comment.parent_id && (
                                                                <Button color='primary' tag='a'>
                                                                    <FaExternalLinkAlt />
                                                                </Button>
                                                            )}

                                                        </InputGroup>
                                                    </FormGroup>
                                                </Col>

                                                <Col xs={12}>
                                                    <FormGroup className='has-validation'>
                                                        <Label for='comment'>Posted By:</Label>
                                                        <InputGroup>
                                                            <Input readOnly value={comment.postedBy?.displayName} />
                                                            <Button color='primary' tag='a' href={comment.postedBy?.generatePath()}>
                                                                <FaExternalLinkAlt />
                                                            </Button>
                                                        </InputGroup>
                                                    </FormGroup>
                                                </Col>

                                                <Col xs={12}>
                                                    <FormGroup className='has-validation'>
                                                        <Label for='comment'>Approved By:</Label>
                                                        <InputGroup>
                                                            <Input readOnly value={comment.approvedBy?.displayName ?? '(N/A)'} />

                                                            {comment.approvedBy && (
                                                                <Button tag='a' color='primary' href={comment.approvedBy.generatePath()}>
                                                                    <FaExternalLinkAlt />
                                                                </Button>
                                                            )}

                                                        </InputGroup>
                                                    </FormGroup>
                                                </Col>
                                            </Row>
                                        </CardBody>
                                    </Card>
                                </Col>
                            </Row>


                        </Form>
                    </>
                )}

            </Formik>
        </>
    );
}

export default EditCommentForm;
