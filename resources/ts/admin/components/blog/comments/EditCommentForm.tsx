import React from 'react';
import withReactContent from 'sweetalert2-react-content';
import { FaExternalLinkAlt, FaSave } from 'react-icons/fa';
import { Badge, Button, Card, CardBody, Col, FormGroup, Input, InputGroup, Label, Row } from 'reactstrap';
import { ErrorMessage, Field, Form, Formik, FormikProps } from 'formik';

import Heading, { HeadingTitle } from '@admin/layouts/admin/Heading';

import axios from 'axios';
import classNames from 'classnames';
import S from 'string';
import Swal from 'sweetalert2';
import * as Yup from 'yup';

import { update } from '@admin/utils/api/endpoints/comments';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

import Comment from '@admin/utils/api/models/Comment';

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

    const statuses = React.useMemo<TCommentStatuses[]>(() => ['approved', 'denied', 'flagged', 'awaiting_verification', 'awaiting_approval', 'locked'], []);

    const schema = React.useMemo(() => Yup.object().shape({
        title: Yup.string().max(255),
        comment: Yup.string().required('Comment is required'),
        status: Yup.string().oneOf(statuses)
    }), []);

    const initialValues = React.useMemo(() => ({
        title: comment.comment.title || '',
        comment: comment.comment.comment,
        status: comment.status
    }), [comment]);

    const confirmSave = React.useCallback(async () => {
        const response = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Please confirm the changes to the comment.`,
            showConfirmButton: true,
            showCancelButton: true
        });

        return response.isConfirmed;
    }, []);

    const handleSave = React.useCallback(async ({ title, comment: content, status }: IFormikValues) => {
        try {
            if (!await confirmSave())
                return;

            const updated = await update(comment, { title: title.trim() || undefined, content, status });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been saved.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            setComment(updated);
            formikRef.current?.resetForm();
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
    }, [comment, setComment]);

    return (
        <>
            <Formik<IFormikValues>
                innerRef={formikRef}
                validationSchema={schema}
                initialValues={initialValues}
                onSubmit={handleSave}
            >
                {({ values, dirty, touched, errors }) => (
                    <Form>
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
                                <Button
                                    color="primary"
                                    className='me-1'
                                    type='submit'
                                >
                                    <FaSave />{' '}Save
                                </Button>
                            </div>
                        </Heading>
                        <>
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
                                                        <Label for='status'>Status:</Label>
                                                        <Field
                                                            as={Input}
                                                            type='select'
                                                            name='status'
                                                            id='status'
                                                            className={classNames({ 'is-invalid': errors.status && touched.status })}
                                                        >
                                                            {statuses.map((status, index) => (
                                                                <option key={index} value={status}>{S(status).humanize().titleCase().s}</option>
                                                            ))}
                                                        </Field>
                                                    </FormGroup>
                                                </Col>

                                                <Col xs={12}>
                                                    <FormGroup className='has-validation'>
                                                        <Label for='comment_parent'>Parent Comment:</Label>
                                                        <InputGroup>
                                                            <Input name='comment_parent' readOnly value={comment.comment.parent_id ?? '(N/A)'} />

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
                                                        <Label for='comment_posted_by'>Posted By:</Label>
                                                        <InputGroup>
                                                            <Input name='comment_posted_by' readOnly value={comment.commenterInfo.display_name} />
                                                            {comment.postedBy.user && (
                                                                <Button color='primary' tag='a' href={comment.postedBy.user.generatePath()}>
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
                        </>
                    </Form>
                )}

            </Formik>
        </>
    );
}

export default EditCommentForm;
