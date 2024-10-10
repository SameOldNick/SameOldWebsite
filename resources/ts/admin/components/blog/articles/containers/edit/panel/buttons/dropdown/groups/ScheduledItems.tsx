import React from 'react';
import { DropdownItem } from 'reactstrap';

import { useEditArticleActionsContext } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';

interface ScheduledItemsProps {
}

const ScheduledItems: React.FC<ScheduledItemsProps> = ({

}) => {
    const {
        onSaveAsRevisionClicked,
        onUpdateClicked,
        onScheduleClicked,
        onUnscheduleClicked,
        onPublishClicked,
        onDeleteClicked
    } = useEditArticleActionsContext();

    return (
        <>
            <DropdownItem onClick={onSaveAsRevisionClicked}>Save as Revision</DropdownItem>
            <DropdownItem onClick={onUpdateClicked}>Update</DropdownItem>
            <DropdownItem onClick={onPublishClicked}>Publish Immediately</DropdownItem>
            <DropdownItem onClick={onScheduleClicked}>Reschedule</DropdownItem>
            <DropdownItem divider />
            <DropdownItem onClick={onUnscheduleClicked}>Unschedule</DropdownItem>
            <DropdownItem onClick={onDeleteClicked}>Delete</DropdownItem>
        </>
    );
}

export default ScheduledItems;
