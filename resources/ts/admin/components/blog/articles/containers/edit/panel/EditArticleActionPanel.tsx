import React from 'react';
import { useNavigate } from 'react-router-dom';
import withReactContent from 'sweetalert2-react-content';

import { FormikProps } from 'formik';
import Swal, { SweetAlertOptions, SweetAlertResult } from 'sweetalert2';

import Loader from '@admin/components/Loader';

import EditArticleActionPanelContext, { EditArticleActionsContextValues } from '@admin/components/blog/articles/containers/edit/panel/EditArticleActionPanelContext';
import useArticleEditActions from '@admin/components/blog/articles/containers/edit/hooks/useArticleEditActions';
import { ArticleFormValues } from '@admin/components/blog/articles/containers/formik/ArticleFormikProvider';

import ArticleInformationAction from '@admin/components/blog/articles/containers/edit/panel/buttons/ArticleInformationAction';
import RestoreRevisionAction from '@admin/components/blog/articles/containers/edit/panel/buttons/RestoreRevisionAction';
import PreviewArticleAction from '@admin/components/blog/articles/containers/edit/panel/buttons/PreviewArticleAction';
import DropdownActions from '@admin/components/blog/articles/containers/edit/panel/buttons/DropdownActions';

import Article from '@admin/utils/api/models/Article';
import Revision from '@admin/utils/api/models/Revision';

type ArticleEditActionsHandlerHelpers = {
    onLoading: (loading: boolean) => void;
    onSuccess: (message: string, extra?: SweetAlertOptions) => Promise<SweetAlertResult>;
    onError: (message: string, extra?: SweetAlertOptions) => Promise<SweetAlertResult>;
    onNavigate: (url: string) => void;
    onReload: () => void;
    onUpdate: () => void;
}

type ArticleEditActionsHandlerParams = {
    formik: FormikProps<ArticleFormValues>;
    article: Article;
    revision: Revision;
    helpers: ArticleEditActionsHandlerHelpers;
}

type ArticleEditActionHandler = (params: ArticleEditActionsHandlerParams) => void;

type ArticleEditActionsHook = (params: ArticleEditActionsHandlerParams) => IEditActionHandlers;

interface IEditActionHandlers {
    handleArticleInformationClicked: () => void;
    handlePreviewArticleClicked: () => void;
    handleRestoreRevisionClicked: () => void;

    handleSaveAsRevisionClicked: () => void;
    handleUpdateClicked: () => void;
    handleUnpublishClicked: () => void;
    handleScheduleClicked: () => void;
    handlePublishClicked: () => void;
    handleUnscheduleClicked: () => void;
    handleDeleteClicked: () => void;
}

interface EditArticleActionPanelProps {
    article: Article;
    revision: Revision;
    formik: FormikProps<ArticleFormValues>;
}

const EditArticleActionPanel: React.FC<EditArticleActionPanelProps> = ({ article, revision, formik }) => {
    const navigate = useNavigate();

    const [loading, setLoading] = React.useState(false);

    const helpers = React.useMemo<ArticleEditActionsHandlerHelpers>(() => ({
        onLoading: (loading: boolean) => {
            setLoading(loading);
        },
        onUpdate: () => {
            formik.resetForm();
        },
        onSuccess: (message: string, extra: SweetAlertOptions = {}) => withReactContent(Swal).fire({
            title: 'Success',
            text: message,
            icon: 'success',
            ...extra
        }),
        onError: (message: string, extra: SweetAlertOptions = {}) => withReactContent(Swal).fire({
            title: 'Oops...',
            text: message,
            icon: 'error',
            ...extra
        }),
        onNavigate: (url: string) => {
            navigate(url);
        },
        onReload: () => {
            window.location.reload();
        }
    }), [navigate]);

    const contextValue = React.useMemo<EditArticleActionsContextValues>(() => {
        const {
            handleArticleInformationClicked,
            handlePreviewArticleClicked,
            handleRestoreRevisionClicked,
            handleSaveAsRevisionClicked,
            handleUpdateClicked,
            handleUnpublishClicked,
            handleScheduleClicked,
            handlePublishClicked,
            handleUnscheduleClicked,
            handleDeleteClicked,
        } = useArticleEditActions({
            formik,
            article,
            revision,
            helpers
        });

        return {
            article,
            onArticleInformationClicked: handleArticleInformationClicked,
            onPreviewArticleClicked: handlePreviewArticleClicked,
            onRestoreRevisionClicked: handleRestoreRevisionClicked,
            onSaveAsRevisionClicked: handleSaveAsRevisionClicked,
            onUpdateClicked: handleUpdateClicked,
            onUnpublishClicked: handleUnpublishClicked,
            onScheduleClicked: handleScheduleClicked,
            onPublishClicked: handlePublishClicked,
            onUnscheduleClicked: handleUnscheduleClicked,
            onDeleteClicked: handleDeleteClicked
        };
    }, [formik, article, revision, helpers]);

    return (
        <>
            <EditArticleActionPanelContext.Provider value={contextValue}>
                {loading && <Loader display={{ type: 'page', show: true }} />}

                <ArticleInformationAction />

                <RestoreRevisionAction />

                <PreviewArticleAction />

                <DropdownActions />
            </EditArticleActionPanelContext.Provider>
        </>
    );
}

export default EditArticleActionPanel;
export {
    EditArticleActionPanelProps,
    ArticleEditActionsHandlerParams,
    ArticleEditActionHandler,
    ArticleEditActionsHook,
    IEditActionHandlers
};
