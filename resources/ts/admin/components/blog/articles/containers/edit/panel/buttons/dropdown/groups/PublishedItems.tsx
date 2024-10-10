import React from 'react';
import { DropdownItem } from 'reactstrap';

import { useEditArticleActionsContext } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';

interface PublishedItemsProps {
}

const PublishedItems: React.FC<PublishedItemsProps> = ({

}) => {
    const {
        onSaveAsRevisionClicked,
        onUpdateClicked,
        onUnpublishClicked,
        onScheduleClicked,
        onDeleteClicked
    } = useEditArticleActionsContext();

    return (
        <>
            <DropdownItem onClick={onSaveAsRevisionClicked}>Save as Revision</DropdownItem>
            <DropdownItem onClick={onUpdateClicked}>Update</DropdownItem>
            <DropdownItem divider />
            <DropdownItem onClick={onUnpublishClicked}>Unpublish</DropdownItem>
            <DropdownItem onClick={onScheduleClicked}>Schedule</DropdownItem>
            <DropdownItem onClick={onDeleteClicked}>Delete</DropdownItem>
        </>
    );
}

export default PublishedItems;
