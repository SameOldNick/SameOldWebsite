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

interface IState {
    show: ArticleStatuses;
}

export default class ArticleList extends React.Component<IProps, IState> {
    private readonly _waitToLoadArticlesRef = React.createRef<IWaitToLoadHandle>();
    private readonly _paginatedTableRef = React.createRef<PaginatedTable<IArticle>>();

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            show: ArticleStatuses.all
        };

        this.loadArticles = this.loadArticles.bind(this);
        this.loadInitialArticles = this.loadInitialArticles.bind(this);
        this.updateArticles = this.updateArticles.bind(this);
        this.handleUpdateFormSubmitted = this.handleUpdateFormSubmitted.bind(this);
    }

    private async loadArticles(link?: string) {
        return link === undefined ? this.loadInitialArticles() : this.updateArticles(link);
    }

    private async loadInitialArticles() {
        const { show } = this.state;

        return fetchArticles(show);
    }

    private async updateArticles(link: string) {
        const { show } = this.state;

        const response = await createAuthRequest().get<IPaginateResponseCollection<IArticle>>(link, { show });

        return response.data;
    }

    private async handleUpdateFormSubmitted(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();

        this._paginatedTableRef.current?.reload();
    }

    public render() {
        const { show } = this.state;

        return (
            <>
                <Row>
                    <Col xs={12} className='d-flex justify-content-between mb-3'>
                        <div>
                            <Button tag={NavLink} to='create' color='primary'>
                                <FaPlus /> Create New
                            </Button>
                        </div>
                        <div className="text-end">
                            <Form className="row row-cols-lg-auto g-3" onSubmit={this.handleUpdateFormSubmitted}>
                                <Col xs={12}>
                                    <label className="visually-hidden" htmlFor="show">Show</label>

                                    <Input type='select' name='show' id='show' value={show} onChange={(e) => this.setState({ show: e.target.value as ArticleStatuses })}>
                                        <option value={ArticleStatuses.unpublished}>Unpublished Only</option>
                                        <option value={ArticleStatuses.published}>Published Only</option>
                                        <option value={ArticleStatuses.scheduled}>Scheduled Only</option>
                                        <option value={ArticleStatuses.removed}>Deleted Only</option>
                                        <option value={ArticleStatuses.all}>All</option>
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
                            ref={this._waitToLoadArticlesRef}
                            callback={this.loadInitialArticles}
                            loading={<Loader display={{ type: 'over-element' }} />}
                        >
                            {(response, err) => (
                                <>
                                    {err && console.error(err)}
                                    {response && (
                                        <PaginatedTable ref={this._paginatedTableRef} initialResponse={response} pullData={this.loadArticles}>
                                            {(data) => (
                                                <Table>
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
                                                                onUpdated={this.loadArticles}
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
}
