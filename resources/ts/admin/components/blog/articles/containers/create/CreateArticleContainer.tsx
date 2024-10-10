import React from 'react';
import { Row } from 'reactstrap';
import { Navigate } from 'react-router-dom';

import UnsavedChangesWarning from '@admin/components/UnsavedChangesWarning';
import Heading, { HeadingTitle } from '@admin/layouts/admin/Heading';

import ArticleFormikProvider, { ArticleFormValues } from '@admin/components/blog/articles/containers/formik/ArticleFormikProvider';
import ArticleEditor from '@admin/components/blog/articles/editor/ArticleEditor';
import { ArticleEditorInputs } from '@admin/components/blog/articles/editor/ArticleEditorContext';
import CreateArticleActionPanel from '@admin/components/blog/articles/containers/create/CreateArticleActionPanel';

import Article from '@admin/utils/api/models/Article';

const CreateArticleContainer: React.FC = ({ }) => {
    const [created, setCreated] = React.useState<Article>();
    const [autoGenerateSummary, setAutoGenerateSummary] = React.useState(true);
    const [summary, setSummary] = React.useState('');

    const initialValues = React.useMemo<ArticleFormValues>(() => ({
        title: '',
        autoGenerateSlug: true,
        slug: '',
        content: '',
        autoGenerateSummary: true,
        summary: '',
        uploadedImages: [],
        tags: []
    }), []);

    const articleEditorInputs = React.useMemo<Partial<ArticleEditorInputs>>(() => ({
        autoGenerateSummary: autoGenerateSummary,
        onAutoGenerateSummaryChanged: (autoGenerate) => setAutoGenerateSummary(autoGenerate),
        onSummaryChanged: (value) => setSummary(value),
        summary: !autoGenerateSummary ?
            summary :
            '(The summary will be automatically generated after you save the article)',
    }), [autoGenerateSummary, summary]);

    const handleArticleCreated = React.useCallback((article: Article) => {
        setCreated(article);
    }, []);

    return (
        <>
            {created !== undefined && <Navigate to={created.generatePath()} />}

            <ArticleFormikProvider
                initialValues={initialValues}
                inputs={articleEditorInputs}
            >
                {({ formik, errors, inputs }) => (
                    <>
                        <UnsavedChangesWarning enabled={Object.values(formik.touched).filter((value) => value).length > 0} />

                        <Row>
                            <Heading>
                                <HeadingTitle>
                                    Create Post
                                </HeadingTitle>

                                <div className='d-flex'>
                                    <CreateArticleActionPanel onArticleCreated={handleArticleCreated} />
                                </div>
                            </Heading>
                        </Row>

                        <ArticleEditor errors={errors} inputs={inputs} />
                    </>
                )}
            </ArticleFormikProvider>
        </>
    );
}

export default CreateArticleContainer;
