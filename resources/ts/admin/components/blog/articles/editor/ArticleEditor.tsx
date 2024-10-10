import React from 'react';
import { Col, Row } from 'reactstrap';

import ArticleContentForm from '@admin/components/blog/articles/editor/form/ArticleContentForm';
import MainImage from '@admin/components/blog/articles/editor/form/MainImage';
import Tags from '@admin/components/blog/articles/editor/form/Tags';

import ArticleEditorContext, { ArticleEditorInputs } from '@admin/components/blog/articles/editor/ArticleEditorContext';

type ArticleEditorErrors = Record<string, string[]>;

interface IProps {
    errors: ArticleEditorErrors;
    inputs: ArticleEditorInputs;
}

const ArticleEditor: React.FC<IProps> = ({ errors, inputs }) => {
    return (
        <>
            <ArticleEditorContext.Provider value={{ errors, inputs }}>
                <Row>
                    <Col md={8}>
                        <ArticleContentForm />
                    </Col>

                    <Col md={4}>
                        <MainImage className='mb-3' />

                        <Tags />
                    </Col>
                </Row>
            </ArticleEditorContext.Provider>

        </>
    );
}

export default ArticleEditor;
export { ArticleEditorErrors };
