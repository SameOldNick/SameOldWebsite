import React from 'react';
import { DropdownItem } from 'reactstrap';

import { useEditArticleActionsContext } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';

interface UnpublishedItemsProps {
}

const UnpublishedItems: React.FC<UnpublishedItemsProps> = ({

}) => {
    const {
        onSaveAsRevisionClicked,
        onScheduleClicked,
        onPublishClicked,
        onDeleteClicked
    } = useEditArticleActionsContext();

    return (
        <>
            <DropdownItem onClick={onSaveAsRevisionClicked}>Save as Revision</DropdownItem>
            <DropdownItem onClick={onPublishClicked}>Save &amp; Publish</DropdownItem>
            <DropdownItem onClick={onScheduleClicked}>Schedule</DropdownItem>
            <DropdownItem divider />
            <DropdownItem onClick={onDeleteClicked}>Delete</DropdownItem>
        </>
    );
}

export default UnpublishedItems;
