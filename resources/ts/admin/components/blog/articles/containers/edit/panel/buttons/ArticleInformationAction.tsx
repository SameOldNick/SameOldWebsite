import React from 'react';
import { Button } from 'reactstrap';
import { FaInfoCircle } from 'react-icons/fa';

import { useEditArticleActionsContext } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';

interface ArticleInformationActionProps {
}

const ArticleInformationAction: React.FC<ArticleInformationActionProps> = ({ }) => {
    const { onArticleInformationClicked } = useEditArticleActionsContext();

    return (
        <>
            <Button
                color="primary"
                outline
                className='me-1'
                title='Article Information'
                onClick={onArticleInformationClicked}
            >
                <FaInfoCircle />
            </Button>
        </>
    );
}

export default ArticleInformationAction;
