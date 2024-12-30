import React from 'react';
import { FaSync } from 'react-icons/fa';
import { Button, Col, Form, Input, InputGroup, Row, Table } from 'reactstrap';

import { createAuthRequest } from '@admin/utils/api/factories';

import PaginatedTable, { PaginatedTableHandle } from '@admin/components/paginated-table/PaginatedTable';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import SingleComment from './SingleComment';
import SelectArticleModal from '@admin/components/modals/SelectArticleModal';
import SelectUserModal from '@admin/components/modals/SelectUserModal';
import LoadError from '@admin/components/LoadError';

import { CommentStatuses, loadAll } from '@admin/utils/api/endpoints/comments';

import Article from '@admin/utils/api/models/Article';
import User from '@admin/utils/api/models/User';
import Comment from '@admin/utils/api/models/Comment';

import awaitModalPrompt from '@admin/utils/modals';

const CommentList: React.FC = () => {
    const waitToLoadCommentsRef = React.useRef<IWaitToLoadHandle>(null);
    const paginatedTableRef = React.useRef<PaginatedTableHandle>(null);

    const [show, setShow] = React.useState<CommentStatuses | 'all'>('all');
    const [article, setArticle] = React.useState<Article | undefined>();
    const [user, setUser] = React.useState<User | undefined>();

    const load = React.useCallback(async (link?: string) => link === undefined ? loadInitial() : loadUpdate(link), [show, article, user]);

    const loadInitial = React.useCallback(async () => loadAll({
        show: show !== 'all' ? show : undefined,
        article: article ? article.article.id : undefined,
        user: user ? user.user.id : undefined
    }), [show, article, user]);

    const loadUpdate = React.useCallback(async (link: string) =>
        (await createAuthRequest().get<IPaginateResponseCollection<IComment>>(link, {
            show: show !== 'all' ? show : undefined,
            article: article ? article.article.id : undefined,
            user: user ? user.user.id : undefined
        })).data, [show, article, user]);

    const handleUpdateFormSubmitted = React.useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        waitToLoadCommentsRef.current?.load();
    }, [waitToLoadCommentsRef.current]);

    const handleChooseUserButtonClicked = React.useCallback(async () => {
        try {
            const selected = await awaitModalPrompt(SelectUserModal, { allowAll: true, existing: user });

            setUser(selected);
        } catch (err) {
            // User cancelled modal.
        }
    }, [user]);

    const handleChooseArticleButtonClicked = React.useCallback(async () => {
        try {
            const selected = await awaitModalPrompt(SelectArticleModal, { allowAll: true, existing: article });

            setArticle(selected);
        } catch (err) {
            // User cancelled modal.
        }
    }, [article]);

    const userDisplay = React.useMemo(() => user !== undefined ? `User ID: ${user.user.id}` : '(All Users)', [user]);
    const articleDisplay = React.useMemo(() => article !== undefined ? `Article ID: ${article.article.id}` : '(All Articles)', [article]);

    React.useEffect(() => {
        waitToLoadCommentsRef.current?.load();
    }, [article, user]);

    return (
        <>
            <Row>
                <Col xs={12} className='d-flex justify-content-between mb-3'>
                    <div />
                    <div className="text-end">
                        <Form className="row row-cols-lg-auto g-3" onSubmit={handleUpdateFormSubmitted}>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="user">User</label>
                                <InputGroup>
                                    <Input readOnly type='text' name='user' id='user' value={userDisplay} />
                                    <Button color='primary' onClick={handleChooseUserButtonClicked}>Choose...</Button>
                                </InputGroup>
                            </Col>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="article">Article</label>
                                <InputGroup>
                                    <Input readOnly type='text' name='article' id='article' value={articleDisplay} />
                                    <Button color='primary' onClick={handleChooseArticleButtonClicked}>Choose...</Button>
                                </InputGroup>
                            </Col>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="show">Show</label>

                                <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value as CommentStatuses)}>
                                    <option value={CommentStatuses.AwaitingVerification}>Awaiting Verification</option>
                                    <option value={CommentStatuses.AwaitingApproval}>Awaiting Approval</option>
                                    <option value={CommentStatuses.Approved}>Approved</option>
                                    <option value={CommentStatuses.Denied}>Denied</option>
                                    <option value={CommentStatuses.Flagged}>Flagged</option>

                                    <option value='all'>All</option>
                                </Input>
                            </Col>
                            <Col xs={12} className='d-flex flex-column flex-md-row'>
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
                        {(response, err, { reload }) => (
                            <>
                                {err && (
                                    <LoadError
                                        error={err}
                                        onTryAgainClicked={() => reload()}
                                        onGoBackClicked={() => window.history.back()}
                                    />
                                )}
                                {response && (
                                    <PaginatedTable ref={paginatedTableRef} initialResponse={response} pullData={load}>
                                        {(data) => (
                                            <Table responsive>
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
