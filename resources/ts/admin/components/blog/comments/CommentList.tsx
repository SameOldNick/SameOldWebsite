import React from 'react';
import { FaCheckCircle, FaEdit, FaSync, FaTimesCircle, FaToolbox, FaTrash } from 'react-icons/fa';
import { Button, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Form, Input, InputGroup, Row, Table } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import { createAuthRequest } from '@admin/utils/api/factories';
import Comment from '@admin/utils/api/models/Comment';
import S from 'string';
import { DateTime } from 'luxon';
import axios from 'axios';
import Swal from 'sweetalert2';

import PaginatedTable from '@admin/components/PaginatedTable';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import { approve, deny } from '@admin/utils/api/endpoints/comments';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

import Article from '@admin/utils/api/models/Article';
import User from '@admin/utils/api/models/User';
import SelectArticleModal from '@admin/components/SelectArticleModal';
import SelectUserModal from '@admin/components/SelectUserModal';

interface IProps {

}

interface ICommentProps {
    comment: Comment;
    onUpdated: () => void;
    setArticle: (article?: Article) => void;
    setUser: (user?: User) => void;
}

const SingleComment: React.FC<ICommentProps> = ({ comment, onUpdated, setArticle, setUser }) => {
    const [actionDropdown, setActionDropdown] = React.useState(false);

    const handleApproveClicked = async (e: React.MouseEvent) => {
        e.preventDefault();

        try {
            if (!comment.comment.id)
                throw new Error('Comment ID is missing.');

            await approve(comment);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been approved.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            onUpdated();

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
    }

    const handleDenyClicked = async (e: React.MouseEvent) => {
        e.preventDefault();

        try {
            if (!comment.comment.id)
                throw new Error('Comment ID is missing.');

            await deny(comment);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment has been denied.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            onUpdated();

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
    }

    return (
        <>
            <tr>
                <th scope='row'>{comment.comment.id}</th>
                <td>
                    <a href='#' title={comment.comment.article.title} onClick={() => setArticle(comment.article)}>
                        {comment.comment.article_id}
                    </a>
                </td>
                <td>
                    <a href='#' onClick={() => setUser(comment.postedBy ?? undefined)}>
                        {comment.postedBy?.user.email || comment.postedBy?.user.id}
                    </a>
                </td>

                <td>{S(comment.comment.comment).truncate(100).s}</td>
                <td>{comment.createdAt.toLocaleString(DateTime.DATETIME_SHORT)}</td>
                <td>{S(comment.status).humanize().s}</td>
                <td>
                    <Dropdown toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                        <DropdownToggle caret color='primary'>
                            <FaToolbox />{' '}
                            Actions
                        </DropdownToggle>
                        <DropdownMenu>
                            <DropdownItem href={comment.generatePath()}><FaEdit />{' '}Edit</DropdownItem>
                            {comment.status !== Comment.STATUS_APPROVED && (
                                <DropdownItem onClick={handleApproveClicked}><FaCheckCircle />{' '}Approve</DropdownItem>
                            )}
                            {comment.status !== Comment.STATUS_DENIED && (
                                <DropdownItem onClick={handleDenyClicked}><FaTimesCircle />{' '}Deny</DropdownItem>
                            )}
                        </DropdownMenu>
                    </Dropdown>
                </td>
            </tr>
        </>
    );
}

const CommentList: React.FC<IProps> = ({ }) => {
    const waitToLoadCommentsRef = React.createRef<WaitToLoad<IPaginateResponseCollection<IComment>>>();
    const paginatedTableRef = React.createRef<PaginatedTable<IComment>>();

    const [selectArticleModal, showSelectArticleModal] = React.useState(false);
    const [selectUserModal, showSelectUserModal] = React.useState(false);
    const [show, setShow] = React.useState('all');
    const [article, setArticle] = React.useState<Article | undefined>();
    const [user, setUser] = React.useState<User | undefined>();

    const load = async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IComment>>(link ?? 'blog/comments', {
            show,
            article: article ? article.article.id : undefined,
            user: user ? user.user.id : undefined
        });

        return response.data;
    }

    const handleUpdateFormSubmitted = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        waitToLoadCommentsRef.current?.load();
    }

    const handleUserSelected = (user?: User) => {
        setUser(user);
        showSelectUserModal(false);
    }

    const handleArticleSelected = (article?: Article) => {
        setArticle(article);
        showSelectArticleModal(false);
    }

    const userDisplay = React.useMemo(() => user !== undefined ? `User ID: ${user.user.id}` : '(All Users)', [user]);
    const articleDisplay = React.useMemo(() => article !== undefined ? `Article ID: ${article.article.id}` : '(All Articles)', [article]);

    React.useEffect(() => {
        waitToLoadCommentsRef.current?.load();
    }, [article, user]);

    return (
        <>
            {selectUserModal && (
                <SelectUserModal
                    allowAll
                    existing={user}
                    onSelected={handleUserSelected}
                    onCancelled={() => showSelectUserModal(false)}
                />
            )}
            {selectArticleModal && (
                <SelectArticleModal
                    allowAll
                    existing={article}
                    onSelected={handleArticleSelected}
                    onCancelled={() => showSelectArticleModal(false)}
                />
            )}
            <Row>
                <Col xs={12} className='d-flex justify-content-between mb-3'>
                    <div />
                    <div className="text-end">
                        <Form className="row row-cols-lg-auto g-3" onSubmit={handleUpdateFormSubmitted}>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="user">User</label>
                                <InputGroup>
                                    <Input readOnly type='text' name='user' id='user' value={userDisplay} />
                                    <Button color='primary' onClick={() => showSelectUserModal(true)}>Choose...</Button>
                                </InputGroup>
                            </Col>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="article">Article</label>
                                <InputGroup>
                                    <Input readOnly type='text' name='article' id='article' value={articleDisplay} />
                                    <Button color='primary' onClick={() => showSelectArticleModal(true)}>Choose...</Button>
                                </InputGroup>
                            </Col>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="show">Show</label>

                                <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value)}>
                                    <option value="awaiting">Awaiting Only</option>
                                    <option value="approved">Approved Only</option>
                                    <option value="denied">Denied Only</option>
                                    <option value="all">All</option>
                                </Input>
                            </Col>
                            <Col xs={12}>
                                <Button type='submit' color='primary'>
                                    <FaSync /> Update
                                </Button>
                            </Col>
                        </Form>

                    </div>
                </Col>
                <Col xs={12}>
                    <WaitToLoad
                        ref={waitToLoadCommentsRef}
                        callback={load}
                        loading={<Loader display={{ type: 'over-element' }} />}
                    >
                        {(response, err) => (
                            <>
                                {err && console.error(err)}
                                {response && (
                                    <PaginatedTable ref={paginatedTableRef} initialResponse={response} pullData={load}>
                                        {(data) => (
                                            <Table>
                                                <thead>
                                                    <tr>
                                                        <th scope='col'>ID</th>
                                                        <th scope='col'>Article ID</th>
                                                        <th scope='col'>Posted By</th>
                                                        <th scope='col'>Comment</th>
                                                        <th scope='col'>Posted At</th>
                                                        <th scope='col'>Status</th>
                                                        <th scope='col'>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {data.length === 0 && (
                                                        <tr>
                                                            <td colSpan={7} className='text-center text-muted'>(No comments found)</td>
                                                        </tr>
                                                    )}
                                                    {data.length > 0 && data.map((comment) => new Comment(comment)).map((comment, index) =>
                                                        <SingleComment
                                                            key={index}
                                                            comment={comment}
                                                            onUpdated={() => paginatedTableRef.current?.reload()}
                                                            setArticle={setArticle}
                                                            setUser={setUser}
                                                        />
                                                    )}
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

        </>
    );
}

export default CommentList;
