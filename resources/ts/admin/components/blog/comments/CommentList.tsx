import React from 'react';
import { FaSync } from 'react-icons/fa';
import { Button, Col, Form, Input, InputGroup, Row, Table } from 'reactstrap';

import { createAuthRequest } from '@admin/utils/api/factories';

import PaginatedTable from '@admin/components/PaginatedTable';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import SingleComment from './SingleComment';
import SelectArticleModal from '@admin/components/modals/SelectArticleModal';
import SelectUserModal from '@admin/components/modals/SelectUserModal';

import { CommentStatuses, loadAll } from '@admin/utils/api/endpoints/comments';

import Article from '@admin/utils/api/models/Article';
import User from '@admin/utils/api/models/User';
import Comment from '@admin/utils/api/models/Comment';

import awaitModalPrompt from '@admin/utils/modals';

interface IProps {

}

const CommentList: React.FC<IProps> = ({ }) => {
    const waitToLoadCommentsRef = React.createRef<IWaitToLoadHandle>();
    const paginatedTableRef = React.createRef<PaginatedTable<IComment>>();

    const [show, setShow] = React.useState<CommentStatuses>(CommentStatuses.All);
    const [article, setArticle] = React.useState<Article | undefined>();
    const [user, setUser] = React.useState<User | undefined>();

    const load = React.useCallback(async (link?: string) => {
        return link === undefined ? loadInitial() : loadUpdate(link);
    }, []);

    const loadInitial = React.useCallback(async () => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IComment>>('blog/comments', {
            show,
            article: article ? article.article.id : undefined,
            user: user ? user.user.id : undefined
        });

        return loadAll({
            show,
            article: article ? article.article.id : undefined,
            user: user ? user.user.id : undefined
        });
    }, [article, user]);

    const loadUpdate = React.useCallback(async (link: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IComment>>(link, {
            show,
            article: article ? article.article.id : undefined,
            user: user ? user.user.id : undefined
        });

        return response.data;
    }, [article, user]);

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
                                    <option value={CommentStatuses.Awaiting}>Awaiting Only</option>
                                    <option value={CommentStatuses.Approved}>Approved Only</option>
                                    <option value={CommentStatuses.Denied}>Denied Only</option>
                                    <option value={CommentStatuses.All}>All</option>
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
