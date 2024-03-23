import React from 'react';
import { FaCalendarAlt, FaEdit, FaExternalLinkAlt, FaFileAlt, FaSave, FaToolbox, FaTrash, FaUndo } from 'react-icons/fa';
import { Button, Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import S from 'string';
import axios from 'axios';
import Swal from 'sweetalert2';
import { DateTime } from 'luxon';

import SelectDateTimeModal from '@admin/components/modals/SelectDateTimeModal';

import Article from '@admin/utils/api/models/Article';

import { updateArticle, restoreArticle as restoreArticleApi, deleteArticle as deleteArticleApi } from '@admin/utils/api/endpoints/articles';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';

interface IArticleProps {
    article: Article;
    onUpdated: () => void;
}

const ArticleRow: React.FC<IArticleProps> = ({ article, onUpdated }) => {
    const [actionDropdown, setActionDropdown] = React.useState(false);

    const handlePublishClicked = React.useCallback(async () => {
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
    }, [onUpdated]);

    const publishArticle = React.useCallback(async () => {
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
    }, []);

    const handleScheduleClicked = React.useCallback(async () => {
        try {
            const dateTime = await awaitModalPrompt(SelectDateTimeModal);

            scheduleArticle(dateTime);
        } catch (err) {
            // User cancelled modal.
        }
    }, []);

    const scheduleArticle = React.useCallback(async (dateTime: DateTime) => {
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
    }, [onUpdated]);

    const handleUnpublishClicked = React.useCallback(async () => {
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
    }, [onUpdated]);

    const unpublishArticle = React.useCallback(async () => {
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
    }, []);

    const handleDeleteClicked = React.useCallback(async () => {
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
    }, [onUpdated]);

    const deleteArticle = React.useCallback(async () => {
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
    }, [onUpdated]);

    const handleRestoreClicked = React.useCallback(async () => {
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
    }, [onUpdated]);

    const restoreArticle = React.useCallback(async () => {
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
    }, [onUpdated]);

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

export default ArticleRow;
