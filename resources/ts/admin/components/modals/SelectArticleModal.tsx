import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Row, Col, Input, Form, Table, InputGroup } from 'reactstrap';
import { FaSearch } from 'react-icons/fa';

import S from 'string';
import classNames from 'classnames';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import PaginatedTable from '@admin/components/PaginatedTable';

import { createAuthRequest } from '@admin/utils/api/factories';

import Article from '@admin/utils/api/models/Article';
import { IPromptModalProps } from '@admin/utils/modals';

interface ISelectArticleModalAllowAllProps extends IPromptModalProps<Article | undefined> {
    allowAll: true;
}

interface ISelectArticleModalSpecificProps extends IPromptModalProps<Article> {
    allowAll?: false;
}

interface ISelectArticleModalSharedProps {
    existing?: Article;
}

type TSelectArticleModalProps = (ISelectArticleModalAllowAllProps | ISelectArticleModalSpecificProps) & ISelectArticleModalSharedProps;

interface IArticleRowProps {
    article: Article;
    selected: boolean;
    onSelected: (selected: boolean, article: Article) => void;
}

const ArticleRow: React.FC<IArticleRowProps> = ({ article, selected, onSelected }) => {
    const tdClassName = React.useMemo(() => classNames({ 'bg-secondary': selected }), [selected]);

    return (
        <tr
            onClick={() => onSelected(!selected, article)}
            style={{ cursor: 'pointer' }}
        >
            <th scope='row' className={tdClassName}>{article.article.id}</th>
            <td className={tdClassName}>{article.article.title}</td>
            <td className={tdClassName}>{S(article.article.revision?.summary).truncate(75).s}</td>
            <td className={tdClassName}>{S(article.status).capitalize().s}</td>
        </tr>
    );
}

const SelectArticleModal: React.FC<TSelectArticleModalProps> = ({ existing, allowAll, onSuccess, onCancelled }) => {
    const waitToLoadArticlesRef = React.createRef<IWaitToLoadHandle>();
    const paginatedTableRef = React.createRef<PaginatedTable<IArticle>>();

    const [selected, setSelected] = React.useState<Article | undefined>(existing);
    const [show, setShow] = React.useState('all');
    const [search, setSearch] = React.useState('');

    const loadArticles = React.useCallback(async (link?: string) => {
        const response = await createAuthRequest().get<IPaginateResponseCollection<IArticle>>(link ?? 'blog/articles', { show });

        return response.data;
    }, [show]);

    const handleSubmit = React.useCallback((e: React.FormEvent) => {
        e.preventDefault();

        if (allowAll) {
            onSuccess(selected);
        } else {
            if (!selected) {
                console.error('No article selected.');
                return;
            }

            onSuccess(selected);
        }
    }, [selected, onSuccess]);

    const passArticlesThru = React.useCallback((articles: IArticle[]) => {
        return articles
            .map((article) => new Article(article))
            .filter((article) =>
                article.article.id?.toString().includes(search) ||
                article.article.title.includes(search) ||
                article.article.slug.includes(search) ||
                article.currentRevision?.revision.summary.includes(search) ||
                article.currentRevision?.revision.content.includes(search)
            );
    }, [search]);

    const handleArticleSelected = React.useCallback((selected: boolean, article: Article) => {
        setSelected(selected ? article : undefined);
    }, []);

    return (
        <>
            <Modal isOpen backdrop='static' size='xl'>
                <Form onSubmit={handleSubmit}>
                    <ModalHeader>
                        Select Article
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
                                    ref={waitToLoadArticlesRef}
                                    callback={loadArticles}
                                    loading={<Loader display={{ type: 'over-element' }} />}
                                >
                                    {(response, err) => (
                                        <>
                                            {err && console.error(err)}
                                            {response && (
                                                <PaginatedTable ref={paginatedTableRef} initialResponse={response} pullData={loadArticles}>
                                                    {(data) => (
                                                        <Table hover>
                                                            <thead>
                                                                <tr>
                                                                    <th scope='col'>ID</th>
                                                                    <th scope='col'>Title</th>
                                                                    <th scope='col'>Summary</th>
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
                                                                            All Articles
                                                                        </td>
                                                                    </tr>
                                                                )}
                                                                {passArticlesThru(data).map((article, index) => (
                                                                    <ArticleRow
                                                                        key={index}
                                                                        article={article}
                                                                        selected={selected ? selected.article.id === article.article.id : false}
                                                                        onSelected={handleArticleSelected}
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

export default SelectArticleModal;
