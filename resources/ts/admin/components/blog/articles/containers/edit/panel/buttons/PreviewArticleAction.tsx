import React from 'react';
import { Button } from 'reactstrap';
import { FaExternalLinkAlt } from 'react-icons/fa';

import { useEditArticleActionsContext } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';

interface PreviewArticleActionProps {

}

const PreviewArticleAction: React.FC<PreviewArticleActionProps> = ({ }) => {
    const { onPreviewArticleClicked } = useEditArticleActionsContext();

    return (
        <>
            <Button
                color='primary'
                outline
                className='me-1'
                title='Preview Article'
                onClick={onPreviewArticleClicked}
            >
                <FaExternalLinkAlt />
            </Button>
        </>
    );
}

export default PreviewArticleAction;
