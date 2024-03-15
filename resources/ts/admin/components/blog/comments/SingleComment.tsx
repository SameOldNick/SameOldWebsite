import React from 'react';
import { FaCheckCircle, FaEdit, FaTimesCircle, FaToolbox } from 'react-icons/fa';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import S from 'string';
import { DateTime } from 'luxon';
import axios from 'axios';
import Swal from 'sweetalert2';

import { approve, deny } from '@admin/utils/api/endpoints/comments';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

import Article from '@admin/utils/api/models/Article';
import User from '@admin/utils/api/models/User';
import Comment from '@admin/utils/api/models/Comment';

interface ICommentProps {
    comment: Comment;
    onUpdated: () => void;
    setArticle: (article?: Article) => void;
    setUser: (user?: User) => void;
}

const SingleComment: React.FC<ICommentProps> = ({ comment, onUpdated, setArticle, setUser }) => {
    const [actionDropdown, setActionDropdown] = React.useState(false);

    const handleApproveClicked = React.useCallback(async (e: React.MouseEvent) => {
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
    }, [onUpdated]);

    const handleDenyClicked = React.useCallback(async (e: React.MouseEvent) => {
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
    }, [onUpdated]);

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

export default SingleComment;
