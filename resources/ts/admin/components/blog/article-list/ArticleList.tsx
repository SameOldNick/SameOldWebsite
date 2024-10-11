import React from 'react';
import { NavLink } from 'react-router-dom';
import { FaPlus, FaSync } from 'react-icons/fa';
import { Button, Col, Form, Input, Row, Table } from 'reactstrap';

import PaginatedTable from '@admin/components/PaginatedTable';
import Loader from '@admin/components/Loader';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import ArticleRow from './ArticleRow';

import Article from '@admin/utils/api/models/Article';

import { createAuthRequest } from '@admin/utils/api/factories';
import { fetchArticles, ArticleStatuses } from '@admin/utils/api/endpoints/articles';

interface IProps {

}

const ArticleList: React.FC<IProps> = ({ }) => {
    const waitToLoadArticlesRef = React.createRef<IWaitToLoadHandle>();
    const paginatedTableRef = React.createRef<PaginatedTable<IArticle>>();

    const [show, setShow] = React.useState<ArticleStatuses>(ArticleStatuses.all);



    const loadInitialArticles = React.useCallback(async () => {
        return fetchArticles(show);
    }, [show]);

    const updateArticles = React.useCallback(async (link: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IArticle>>(link, { show });

        return response.data;
    }, [show]);

    const loadArticles = React.useCallback(async (link?: string) => {
        return link === undefined ? loadInitialArticles() : updateArticles(link);
    }, [loadInitialArticles, updateArticles]);

    const handleUpdateFormSubmitted = React.useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        paginatedTableRef.current?.reload();
    }, [paginatedTableRef]);

    const handleUpdated = React.useCallback(() => {
        waitToLoadArticlesRef.current?.load();
    }, [waitToLoadArticlesRef]);

    return (
        <>
            <Row>
                <Col xs={12} className='d-flex flex-column flex-md-row justify-content-between mb-3'>
                    <div className="mb-3 mb-md-0 d-flex flex-column flex-md-row">
                        <Button tag={NavLink} to='create' color='primary'>
                            <FaPlus /> Create New
                        </Button>
                    </div>

                    <div className="text-start text-md-end">
                        <Form className="row row-cols-1 row-cols-lg-auto g-3" onSubmit={handleUpdateFormSubmitted}>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="show">Show</label>
                                <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value as ArticleStatuses)}>
                                    <option value={ArticleStatuses.unpublished}>Unpublished Only</option>
                                    <option value={ArticleStatuses.published}>Published Only</option>
                                    <option value={ArticleStatuses.scheduled}>Scheduled Only</option>
                                    <option value={ArticleStatuses.removed}>Deleted Only</option>
                                    <option value={ArticleStatuses.all}>All</option>
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
                        ref={waitToLoadArticlesRef}
                        callback={loadInitialArticles}
                        loading={<Loader display={{ type: 'over-element' }} />}
                    >
                        {(response, err) => (
                            <>
                                {err && logger.error(err)}
                                {response && (
                                    <PaginatedTable ref={paginatedTableRef} initialResponse={response} pullData={loadArticles}>
                                        {(data) => (
                                            <Table responsive>
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Title</th>
                                                        <th>Summary</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {data.length === 0 && (
                                                        <tr>
                                                            <tr>
                                                                <td colSpan={5} className='text-center text-muted'>(No articles found)</td>
                                                            </tr>
                                                        </tr>
                                                    )}
                                                    {data.length > 0 && data.map((article, index) =>
                                                        <ArticleRow
                                                            key={index}
                                                            article={new Article(article)}
                                                            onUpdated={handleUpdated}
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

export default ArticleList;
