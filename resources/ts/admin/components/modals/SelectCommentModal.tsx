import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Row, Col, Input, Form, Table, InputGroup } from 'reactstrap';
import { FaSearch } from 'react-icons/fa';

import S from 'string';
import classNames from 'classnames';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import PaginatedTable, { PaginatedTableHandle } from '@admin/components/paginated-table/PaginatedTable';

import { createAuthRequest } from '@admin/utils/api/factories';
import Comment from '@admin/utils/api/models/Comment';
import { IPromptModalProps } from '@admin/utils/modals';

interface ISelectCommentModalAllowAllProps extends IPromptModalProps<Comment | undefined> {
    allowAll: true;
}

interface ISelectCommentModalSpecificProps extends IPromptModalProps<Comment> {
    allowAll?: false;
}

interface ISelectCommentModalSharedProps {
    existing?: Comment;
    onCancelled: () => void;
}

type TSelectCommentModalProps = (ISelectCommentModalAllowAllProps | ISelectCommentModalSpecificProps) & ISelectCommentModalSharedProps;

interface ICommentRowProps {
    comment: Comment;
    selected: boolean;
    onSelected: (selected: boolean, comment: Comment) => void;
}

const CommentRow: React.FC<ICommentRowProps> = ({ comment, selected, onSelected }) => {
    const tdClassName = React.useMemo(() => classNames({ 'bg-secondary': selected }), [selected]);

    return (
        <tr
            onClick={() => onSelected(!selected, comment)}
            style={{ cursor: 'pointer' }}
        >
            <th scope='row' className={tdClassName}>{comment.comment.id}</th>
            <td className={tdClassName}>{comment.comment.title}</td>
            <td className={tdClassName}>{S(comment.comment.comment).truncate(75).s}</td>
            <td className={tdClassName}>{S(comment.status).humanize().s}</td>
        </tr>
    );
}

const SelectCommentModal: React.FC<TSelectCommentModalProps> = ({ existing, allowAll, onSuccess, onCancelled }) => {
    const waitToLoadCommentsRef = React.useRef<IWaitToLoadHandle>(null);
    const paginatedTableRef = React.useRef<PaginatedTableHandle>(null);

    const [selected, setSelected] = React.useState<Comment | undefined>(existing);
    const [show, _setShow] = React.useState('all');
    const [search, setSearch] = React.useState('');

    const loadComments = React.useCallback(async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IComment>>(link ?? 'blog/comments', { show });

        return response.data;
    }, [show]);

    const handleSubmit = React.useCallback((e: React.FormEvent) => {
        e.preventDefault();

        if (allowAll) {
            onSuccess(selected);
        } else {
            if (!selected) {
                logger.error('No comment selected.');
                return;
            }

            onSuccess(selected);
        }
    }, [selected, onSuccess]);

    const passCommentsThru = React.useCallback((comments: IComment[]) => {
        return comments
            .map((comment) => new Comment(comment))
            .filter((comment) =>
                comment.comment.id?.toString().includes(search) ||
                comment.comment.title?.includes(search) ||
                comment.comment.comment.includes(search)
            );
    }, [search]);

    const handleCommentSelected = React.useCallback((selected: boolean, comment: Comment) => {
        setSelected(selected ? comment : undefined);
    }, []);

    return (
        <>
            <Modal isOpen backdrop='static' size='xl'>
                <Form onSubmit={handleSubmit}>
                    <ModalHeader>
                        Select Comment
                    </ModalHeader>
                    <ModalBody>
                        <Row>
                            <Col xs={12}>
                                <div className="row row-cols-xl-auto g-3">
                                    <Col xs={12}>
                                        <InputGroup>
                                            <Input
                                                name='search'
                                                id='search'
                                                onChange={(e) => setSearch(e.currentTarget.value)}
                                                onBlur={(e) => setSearch(e.currentTarget.value)}
                                            />
                                            <Button
                                                type='button'
                                                color='primary'
                                                onClick={() => paginatedTableRef.current?.reload()}
                                            >
                                                <FaSearch />
                                            </Button>
                                        </InputGroup>
                                    </Col>
                                </div>
                            </Col>
                            <Col xs={12}>
                                <WaitToLoad
                                    ref={waitToLoadCommentsRef}
                                    callback={loadComments}
                                    loading={<Loader display={{ type: 'over-element' }} />}
                                >
                                    {(response, err) => (
                                        <>
                                            {err && logger.error(err)}
                                            {response && (
                                                <PaginatedTable ref={paginatedTableRef} initialResponse={response} pullData={loadComments}>
                                                    {(data) => (
                                                        <Table hover>
                                                            <thead>
                                                                <tr>
                                                                    <th scope='col'>ID</th>
                                                                    <th scope='col'>Title</th>
                                                                    <th scope='col'>Comment</th>
                                                                    <th scope='col'>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {allowAll && (
                                                                    <tr style={{ cursor: 'pointer' }} onClick={() => setSelected(undefined)}>
                                                                        <td
                                                                            colSpan={4}
                                                                            className={classNames('text-center fw-bold', { 'bg-secondary': selected === undefined })}
                                                                        >
                                                                            All Comments
                                                                        </td>
                                                                    </tr>
                                                                )}
                                                                {passCommentsThru(data).map((comment, index) => (
                                                                    <CommentRow
                                                                        key={index}
                                                                        comment={comment}
                                                                        selected={selected ? selected.comment.id === comment.comment.id : false}
                                                                        onSelected={handleCommentSelected}
                                                                    />
                                                                ))}
                                                            </tbody>
                                                        </Table>
                                                    )}

                                                </PaginatedTable>
                                            )}
                                        </>
                                    )}
                                </WaitToLoad>
                            </Col>

                        </Row>
                    </ModalBody>
                    <ModalFooter>
                        <Button type='submit' color="primary" disabled={!allowAll && !selected}>
                            Select
                        </Button>{' '}
                        <Button color="secondary" onClick={onCancelled}>
                            Cancel
                        </Button>
                    </ModalFooter>
                </Form>
            </Modal>
        </>
    );
}

export default SelectCommentModal;
