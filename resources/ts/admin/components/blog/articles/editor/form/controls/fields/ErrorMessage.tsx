import React from 'react';
import ArticleEditorContext, { hasErrors } from '@admin/components/blog/articles/editor/ArticleEditorContext';

interface IErrorMessageProps {
    input: string;
}

const ErrorMessage: React.FC<IErrorMessageProps> = ({ input }) => {
    const context = React.useContext(ArticleEditorContext);

    return (
        <>
            {hasErrors(context, input) && (
                <div className="invalid-feedback">
                    {context.errors[input][0]}
                </div>

            )}
        </>
    );
}

export default ErrorMessage;
