import React from 'react';
import { Button } from 'reactstrap';
import { FaUndo } from 'react-icons/fa';

import { useEditArticleActionsContext } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';

interface RestoreRevisionActionProps {

}

const RestoreRevisionAction: React.FC<RestoreRevisionActionProps> = ({ }) => {
    const { onRestoreRevisionClicked } = useEditArticleActionsContext();

    return (
        <>
            <Button
                color="primary"
                outline
                className='me-1'
                title='Restore Revision'
                onClick={onRestoreRevisionClicked}
            >
                <FaUndo />
            </Button>
        </>
    );
}

export default RestoreRevisionAction;
