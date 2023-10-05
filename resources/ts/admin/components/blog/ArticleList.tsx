import React from 'react';
import { NavLink } from 'react-router-dom';
import { FaCalendarAlt, FaEdit, FaExternalLinkAlt, FaFileAlt, FaPlus, FaPrint, FaSave, FaSync, FaToolbox, FaTrash, FaUndo } from 'react-icons/fa';
import { Button, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Form, Input, Row, Table } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import S from 'string';
import axios from 'axios';
import Swal from 'sweetalert2';
import { DateTime } from 'luxon';

import SelectDateTimeModal from '@admin/components/SelectDateTimeModal';

import Article from '@admin/utils/api/models/Article';

import { createAuthRequest } from '@admin/utils/api/factories';
import { updateArticle, restoreArticle as restoreArticleApi, deleteArticle as deleteArticleApi } from '@admin/utils/api/calls/articles';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface IProps {

}

interface IState {
    articles: Article[];
    show: string;
}

interface IArticleProps {
    article: Article;
    onUpdated: () => void;
}

export default class ArticleList extends React.Component<IProps, IState> {
    static Article: React.FC<IArticleProps> = ({ article, onUpdated }) => {
        const [actionDropdown, setActionDropdown] = React.useState(false);
        const [showScheduleModal, setShowScheduleModal] = React.useState(false);

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
                await updateArticle(article, article.article.title, article.article.slug, DateTime.now());
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
            setShowScheduleModal(true);
        }

        const scheduleArticle = async (dateTime: DateTime) => {
            try {
                await updateArticle(article, article.article.title, article.article.slug, dateTime);

                setShowScheduleModal(false);

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
                await updateArticle(article, article.article.title, article.article.slug, null);
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
                const { success } = await deleteArticleApi(article);

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
                const { success } = await restoreArticleApi(article);

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
                {showScheduleModal && (
                    <SelectDateTimeModal onSelected={scheduleArticle} onCancelled={() => setShowScheduleModal(false)} />
                )}
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
                                            <DropdownItem href={`edit/${article.article.id}`}><FaEdit />{' '}Edit</DropdownItem>
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

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            articles: [],
            show: 'all'
        };

        this.loadArticles = this.loadArticles.bind(this);
        this.onUpdateFormSubmitted = this.onUpdateFormSubmitted.bind(this);
    }

    componentDidMount(): void {
        this.loadArticles();
    }

    private async loadArticles() {
        const { show } = this.state;

        try {
            const response = await createAuthRequest().get<IArticle[]>('blog/articles', { show });

            this.setState({ articles: response.data.map((article) => new Article(article)) });
        } catch (e) {
            console.error(e);
        }
    }

    private async onUpdateFormSubmitted(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();

        this.loadArticles();
    }

    public render() {
        const { articles, show } = this.state;

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
                            <Form className="row row-cols-lg-auto g-3" onSubmit={this.onUpdateFormSubmitted}>
                                <Col xs={12}>
                                    <label className="visually-hidden" htmlFor="show">Show</label>

                                    <Input type='select' name='show' id='show' value={show} onChange={(e) => this.setState({ show: e.target.value })}>
                                        <option value="unpublished">Unpublished Only</option>
                                        <option value="published">Published Only</option>
                                        <option value="scheduled">Scheduled Only</option>
                                        <option value="removed">Deleted Only</option>
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
                                {articles.map((article, index) =>
                                    <ArticleList.Article
                                        key={index}
                                        article={article}
                                        onUpdated={this.loadArticles}
                                    />
                                )}
                            </tbody>
                        </Table>
                    </Col>
                </Row>

            </>
        );
    }
}
