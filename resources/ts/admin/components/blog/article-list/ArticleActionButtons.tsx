import React from 'react';
import { Button, Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap';
import { FaCalendarAlt, FaEdit, FaExternalLinkAlt, FaFileAlt, FaSave, FaToolbox, FaTrash, FaUndo } from 'react-icons/fa';

import Article from '@admin/utils/api/models/Article';

interface IArticleActionButtonsProps {
    article: Article;

    onPreviewClicked: (event: React.MouseEvent<HTMLElement>) => void;
    onEditClicked: (event: React.MouseEvent<HTMLElement>) => void;

    onPublishNowClicked: (event: React.MouseEvent<HTMLElement>) => void;
    onScheduleClicked: (event: React.MouseEvent<HTMLElement>) => void;

    onUnpublishClicked: (event: React.MouseEvent<HTMLElement>) => void;

    onRestoreClicked: (event: React.MouseEvent<HTMLElement>) => void;
    onDeleteClicked: (event: React.MouseEvent<HTMLElement>) => void;
}

const ArticleActionButtons: React.FC<IArticleActionButtonsProps> = (props) => {
    const {
        article: { status },
        onPreviewClicked,
        onEditClicked,
        onPublishNowClicked,
        onScheduleClicked,
        onRestoreClicked,
        onDeleteClicked,
        onUnpublishClicked,
    } = props;

    const [actionDropdown, setActionDropdown] = React.useState(false);

    return React.useMemo(() => {
        switch (status) {
            case Article.ARTICLE_STATUS_UNPUBLISHED: {
                return (
                    <>
                        <Dropdown group toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                            <DropdownToggle caret color='primary'>
                                <FaToolbox />{' '}
                                Actions
                            </DropdownToggle>
                            <DropdownMenu>
                                <DropdownItem onClick={onPreviewClicked}><FaExternalLinkAlt />{' '}Preview</DropdownItem>
                                <DropdownItem onClick={onEditClicked}><FaEdit />{' '}Edit</DropdownItem>
                                <DropdownItem onClick={onPublishNowClicked}><FaSave />{' '}Publish Now</DropdownItem>
                                <DropdownItem onClick={onScheduleClicked}><FaCalendarAlt />{' '}Schedule</DropdownItem>
                                <DropdownItem onClick={onDeleteClicked}><FaTrash />{' '}Delete</DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                    </>
                )
            }

            case Article.ARTICLE_STATUS_PUBLISHED:
            case Article.ARTICLE_STATUS_SCHEDULED: {
                return (
                    <>
                        <Dropdown group toggle={() => setActionDropdown((prev) => !prev)} isOpen={actionDropdown}>
                            <DropdownToggle caret color='primary'>
                                <FaToolbox />{' '}
                                Actions
                            </DropdownToggle>
                            <DropdownMenu>
                                <DropdownItem onClick={onPreviewClicked}><FaExternalLinkAlt />{' '}Preview</DropdownItem>
                                <DropdownItem onClick={onEditClicked}><FaEdit />{' '}Edit</DropdownItem>
                                <DropdownItem onClick={onUnpublishClicked}><FaFileAlt />{' '}Unpublish</DropdownItem>
                                <DropdownItem onClick={onDeleteClicked}><FaTrash />{' '}Delete</DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                    </>
                );
            }

            case Article.ARTICLE_STATUS_DELETED: {
                return (
                    <>
                        <Button color='primary' onClick={onRestoreClicked} title='Undelete' className='me-1'>
                            <FaUndo />{' '}
                            Restore
                        </Button>
                    </>
                );
            }
        }
    }, [status, actionDropdown]);
}

export default ArticleActionButtons;
