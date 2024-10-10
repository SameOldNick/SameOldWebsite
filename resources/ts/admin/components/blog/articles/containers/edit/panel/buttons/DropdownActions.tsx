import React from 'react';
import { Dropdown, DropdownMenu, DropdownToggle } from 'reactstrap';

import S from 'string';

import PublishedItems from './dropdown/groups/PublishedItems';
import UnpublishedItems from './dropdown/groups/UnpublishedItems';
import ScheduledItems from './dropdown/groups/ScheduledItems';
import { useEditArticleActionsContext } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';

import Article from '@admin/utils/api/models/Article';

interface DropdownActionsProps {
}

const DropdownActions: React.FC<DropdownActionsProps> = ({ }) => {
    const { article } = useEditArticleActionsContext();

    const [dropdownOpen, setDropdownOpen] = React.useState(false);

    const items = React.useMemo(() => {
        switch (article.status) {
            case Article.ARTICLE_STATUS_PUBLISHED:
                return <PublishedItems />;
            case Article.ARTICLE_STATUS_UNPUBLISHED:
                return <UnpublishedItems />;
            case Article.ARTICLE_STATUS_SCHEDULED:
                return <ScheduledItems />;
            default:
                return null;
        }
    }, [article]);

    return (
        <>
            <Dropdown toggle={() => setDropdownOpen((prev) => !prev)} isOpen={dropdownOpen}>
                <DropdownToggle caret color='primary'>
                    {`Status: ${S(article.status).capitalize().s}`}
                </DropdownToggle>
                <DropdownMenu>
                    {items}
                </DropdownMenu>
            </Dropdown>
        </>
    );
}

export default DropdownActions;
