import React from 'react';
import { FaCheck, FaClock, FaEdit, FaFlag, FaHourglassHalf, FaLock, FaTimes, FaToolbox } from 'react-icons/fa';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import S from 'string';
import { DateTime } from 'luxon';
import axios from 'axios';
import Swal from 'sweetalert2';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';

import Article from '@admin/utils/api/models/Article';
import User from '@admin/utils/api/models/User';
import Comment from '@admin/utils/api/models/Comment';
import { update } from '@admin/utils/api/endpoints/comments';

interface ICommentProps {
    comment: Comment;
    onUpdated: () => void;
    setArticle: (article?: Article) => void;
    setUser: (user?: User) => void;
}

interface ICommentActionsProps {
    comment: Comment;
    onStatusChangeClicked: (status: TCommentStatuses) => Promise<void>;
}

const SingleCommentActions: React.FC<ICommentActionsProps> = ({ comment, onStatusChangeClicked }) => {
    const [actionDropdownOpen, setActionDropdownOpen] = React.useState(false);

    const handleStatusActionClicked = React.useCallback((e: React.MouseEvent, status: TCommentStatuses) => {
        e.preventDefault();

        onStatusChangeClicked(status);
    }, [comment, onStatusChangeClicked]);

    return (
        <>
            <Dropdown toggle={() => setActionDropdownOpen((prev) => !prev)} isOpen={actionDropdownOpen}>
                <DropdownToggle caret color='primary'>
                    <FaToolbox />{' '}
                    Actions
                </DropdownToggle>
                <DropdownMenu>
                    <DropdownItem href={comment.generatePath()}><FaEdit />{' '}Edit</DropdownItem>
                    <DropdownItem divider />
                    <DropdownItem onClick={(e) => handleStatusActionClicked(e, 'approved')}><FaCheck />{' '}Set as Approved</DropdownItem>
                    <DropdownItem onClick={(e) => handleStatusActionClicked(e, 'denied')}><FaTimes />{' '}Set as Denied</DropdownItem>
                    <DropdownItem onClick={(e) => handleStatusActionClicked(e, 'locked')}><FaLock />{' '}Set as Locked</DropdownItem>
                    <DropdownItem onClick={(e) => handleStatusActionClicked(e, 'flagged')}><FaFlag />{' '}Set as Flagged</DropdownItem>
                    <DropdownItem onClick={(e) => handleStatusActionClicked(e, 'awaiting_verification')}><FaClock />{' '}Set as Awaiting Verification</DropdownItem>
                    <DropdownItem onClick={(e) => handleStatusActionClicked(e, 'awaiting_approval')}><FaHourglassHalf />{' '}Set as Awaiting Approval</DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </>
    );
}

const SingleComment: React.FC<ICommentProps> = ({ comment, onUpdated, setArticle }) => {
    const handleStatusChangeClicked = React.useCallback(async (status: TCommentStatuses) => {
        try {
            if (!comment.comment.id)
                throw new Error('Comment ID is missing.');

            await update(comment, { status })

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: `The comment status was changed.`,
                showConfirmButton: true,
                showCancelButton: false
            });

            onUpdated();
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred: ${message}\nPlease try again.`,
                showConfirmButton: false,
                showCancelButton: true
            });
        }
    }, [comment, onUpdated]);

    return (
        <>
            <tr>
                <th scope='row'>{comment.comment.id}</th>
                <td>
                    <a href='#' title={comment.comment.article.title} onClick={() => setArticle(comment.article)}>
                        {comment.comment.article_id}
                    </a>
                </td>
                <td>{comment.commenterInfo.display_name}</td>

                <td>{S(comment.comment.comment).truncate(100).s}</td>
                <td>{comment.createdAt.toLocaleString(DateTime.DATETIME_SHORT)}</td>
                <td>{S(comment.status).humanize().s}</td>
                <td>
                    <SingleCommentActions comment={comment} onStatusChangeClicked={handleStatusChangeClicked} />
                </td>
            </tr>
        </>
    );
}

export default SingleComment;
