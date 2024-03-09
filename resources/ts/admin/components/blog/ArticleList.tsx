import React from 'react';
import { NavLink } from 'react-router-dom';
import { FaCalendarAlt, FaEdit, FaExternalLinkAlt, FaFileAlt, FaPlus, FaSave, FaSync, FaToolbox, FaTrash, FaUndo } from 'react-icons/fa';
import { Button, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Form, Input, Row, Table } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import S from 'string';
import axios from 'axios';
import Swal from 'sweetalert2';
import { DateTime } from 'luxon';

import SelectDateTimeModal from '@admin/components/SelectDateTimeModal';
import PaginatedTable from '@admin/components/PaginatedTable';
import Loader from '@admin/components/Loader';
import WaitToLoad from '@admin/components/WaitToLoad';

import Article from '@admin/utils/api/models/Article';

import { createAuthRequest } from '@admin/utils/api/factories';
import { updateArticle, restoreArticle as restoreArticleApi, deleteArticle as deleteArticleApi, fetchArticles, ArticleStatuses } from '@admin/utils/api/endpoints/articles';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';

interface IProps {

}

interface IState {
    show: ArticleStatuses;
}

interface IArticleProps {
    article: Article;
    onUpdated: () => void;
}

export default class ArticleList extends React.Component<IProps, IState> {
    static Article: React.FC<IArticleProps> = ({ article, onUpdated }) => {
        const [actionDropdown, setActionDropdown] = React.useState(false);

        const handlePublishClicked = async () => {
            const result = await withReactContent(Swal).fire({
                icon: 'question',
                title: 'Are You Sure?',
                text: `The article will become visible to all users.`,
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await publishArticle();

                onUpdated();
            }
        }

        const publishArticle = async () => {
            try {
                await updateArticle(article.article.id, article.article.title, article.article.slug, DateTime.now());
            } catch (err) {
                console.error(err);

                const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

                const result = await withReactContent(Swal).fire({
                    icon: 'error',
                    title: 'Ooops...',
                    text: `An error occurred: ${message}`,
                    showConfirmButton: true,
                    confirmButtonText: 'Try Again',
                    showCancelButton: true
                });

                if (result.isConfirmed) {
                    await publishArticle();
                }
            }
        }

        const handleScheduleClicked = async () => {
            try {
                const dateTime = await awaitModalPrompt(SelectDateTimeModal);

                scheduleArticle(dateTime);
            } catch (err) {
                // User cancelled modal.
            }
        }

        const scheduleArticle = async (dateTime: DateTime) => {
            try {
                await updateArticle(article.article.id, article.article.title, article.article.slug, dateTime);

                onUpdated();
            } catch (err) {
                console.error(err);

                const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

                const result = await withReactContent(Swal).fire({
                    icon: 'error',
                    title: 'Ooops...',
                    text: `An error occurred: ${message}`,
                    showConfirmButton: true,
                    confirmButtonText: 'Try Again',
                    showCancelButton: true
                });

                if (result.isConfirmed) {
                    await scheduleArticle(dateTime);
                }
            }
        }

        const handleUnpublishClicked = async () => {
            const result = await withReactContent(Swal).fire({
                icon: 'question',
                title: 'Are You Sure?',
                text: `The article will become hidden.`,
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await unpublishArticle();
                onUpdated();
            }
        }

        const unpublishArticle = async () => {
            try {
                await updateArticle(article.article.id, article.article.title, article.article.slug, null);
            } catch (err) {
                console.error(err);

                const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

                const result = await withReactContent(Swal).fire({
                    icon: 'error',
                    title: 'Ooops...',
                    text: `An error occurred: ${message}`,
                    showConfirmButton: true,
                    confirmButtonText: 'Try Again',
                    showCancelButton: true
                });

                if (result.isConfirmed) {
                    await unpublishArticle();
                }
            }
        }

        const handleDeleteClicked = async () => {
            const result = await withReactContent(Swal).fire({
                icon: 'question',
                title: 'Are You Sure?',
                text: `The article can be restored later, if needed.`,
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await deleteArticle();
                onUpdated();
            }
        }

        const deleteArticle = async () => {
            try {
                await deleteArticleApi(article.article.id);

                onUpdated();
            } catch (err) {
                console.error(err);

                const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

                const result = await withReactContent(Swal).fire({
                    icon: 'error',
                    title: 'Ooops...',
                    text: `An error occurred: ${message}`,
                    showConfirmButton: true,
                    confirmButtonText: 'Try Again',
                    showCancelButton: true
                });

                if (result.isConfirmed) {
                    await unpublishArticle();
                }
            }
        }

        const handleRestoreClicked = async () => {
            const result = await withReactContent(Swal).fire({
                icon: 'question',
                title: 'Are You Sure?',
                text: `The article maybe made public.`,
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await restoreArticle();
                onUpdated();
            }
        }

        const restoreArticle = async () => {
            try {
                await restoreArticleApi(article.article.id);

                onUpdated();
            } catch (err) {
                console.error(err);

                const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

                const result = await withReactContent(Swal).fire({
                    icon: 'error',
                    title: 'Ooops...',
                    text: `An error occurred: ${message}`,
                    showConfirmButton: true,
                    confirmButtonText: 'Try Again',
                    showCancelButton: true
                });

                if (result.isConfirmed) {
                    await restoreArticle();
                }
            }
        }

        return (
            <>
                <tr>
                    <td>{article.article.id}</td>
                    <td>{article.article.title}</td>
                    <td>{S(article.article.revision?.summary).truncate(75).s}</td>
                    <td>{S(article.status).capitalize().s}</td>
                    <td>
                        {article.status === Article.ARTICLE_STATUS_UNPUBLISHED &&
                            (
                                <>
                                    <Dropdown group toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                                        <DropdownToggle caret color='primary'>
                                            <FaToolbox />{' '}
                                            Actions
                                        </DropdownToggle>
                                        <DropdownMenu>
                                            <DropdownItem href={article.article.private_url} target='_blank'><FaExternalLinkAlt />{' '}Preview</DropdownItem>
                                            <DropdownItem href={`posts/edit/${article.article.id}`}><FaEdit />{' '}Edit</DropdownItem>
                                            <DropdownItem onClick={handlePublishClicked}><FaSave />{' '}Publish Now</DropdownItem>
                                            <DropdownItem onClick={handleScheduleClicked}><FaCalendarAlt />{' '}Schedule</DropdownItem>
                                            <DropdownItem onClick={handleDeleteClicked}><FaTrash />{' '}Delete</DropdownItem>
                                        </DropdownMenu>
                                    </Dropdown>
                                </>
                            )
                        }

                        {(article.status === Article.ARTICLE_STATUS_PUBLISHED || article.status === Article.ARTICLE_STATUS_SCHEDULED) &&
                            (
                                <>
                                    <Dropdown group toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                                        <DropdownToggle caret color='primary'>
                                            <FaToolbox />{' '}
                                            Actions
                                        </DropdownToggle>
                                        <DropdownMenu>
                                            <DropdownItem href={article.article.private_url} target='_blank'><FaExternalLinkAlt />{' '}Preview</DropdownItem>
                                            <DropdownItem href={`posts/edit/${article.article.id}`}><FaEdit />{' '}Edit</DropdownItem>
                                            <DropdownItem onClick={handleUnpublishClicked}><FaFileAlt />{' '}Unpublish</DropdownItem>
                                            <DropdownItem onClick={handleDeleteClicked}><FaTrash />{' '}Delete</DropdownItem>
                                        </DropdownMenu>
                                    </Dropdown>
                                </>
                            )
                        }

                        {article.status === Article.ARTICLE_STATUS_DELETED &&
                            (
                                <>
                                    <Button color='primary' onClick={handleRestoreClicked} title='Undelete' className='me-1'>
                                        <FaUndo />{' '}
                                        Restore
                                    </Button>
                                </>
                            )
                        }

                    </td>
                </tr>
            </>
        );
    }

    private readonly _waitToLoadArticlesRef = React.createRef<WaitToLoad<IPaginateResponseCollection<IArticle>>>();
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
                                                            <ArticleList.Article
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
