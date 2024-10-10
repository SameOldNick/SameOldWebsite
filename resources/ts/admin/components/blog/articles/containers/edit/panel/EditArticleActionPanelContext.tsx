import React from 'react';

import Article from '@admin/utils/api/models/Article';

interface EditArticleActionsContextValues {
    article: Article;

    onArticleInformationClicked: () => void;
    onPreviewArticleClicked: () => void;
    onRestoreRevisionClicked: () => void;

    onSaveAsRevisionClicked: () => void;
    onUpdateClicked: () => void;
    onUnpublishClicked: () => void;
    onScheduleClicked: () => void;
    onPublishClicked: () => void;
    onUnscheduleClicked: () => void;
    onDeleteClicked: () => void;
}

const EditArticleActionsContext = React.createContext<EditArticleActionsContextValues | undefined>(undefined);

const useEditArticleActionsContext = () => {
    const context = React.useContext(EditArticleActionsContext);

    if (!context) {
        throw new Error('useEditArticleActionsContext must be used within an EditArticleActionPanel');
    }

    return context;
};

export default EditArticleActionsContext;
export { EditArticleActionsContextValues, useEditArticleActionsContext };
