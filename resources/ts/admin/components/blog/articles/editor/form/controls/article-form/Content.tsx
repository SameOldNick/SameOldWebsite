import React from 'react';
import { FormGroup, Label } from 'reactstrap';

import classNames from 'classnames';

import ErrorMessage from '@admin/components/blog/articles/editor/form/controls/fields/ErrorMessage';
import ArticleEditorContext from '@admin/components/blog/articles/editor/ArticleEditorContext';
import MarkdownEditor, { UploadImagesCallback } from '@admin/components/MarkdownEditor';

type TMarkdownImage = ArrayElement<Awaited<ReturnType<UploadImagesCallback>>>;

interface ContentInputs {
    content: string;
    onContentChange: (content: string) => void;
    onUploadImage: (files: File[]) => Promise<TMarkdownImage[]>;
}

const Content: React.FC = () => {
    const {
        inputs: {
            content,
            onContentChange,
            onUploadImage
        },
        errors
    } = React.useContext(ArticleEditorContext);

    const hasError = React.useMemo(() => 'content' in errors && errors.content.length > 0, [errors]);

    return (
        <>
            <FormGroup className='has-validation'>
                <Label for='description'>Content:</Label>

                <div className={classNames(hasError ? 'is-invalid form-control' : '')}>
                    <MarkdownEditor
                        mode='split'
                        value={content}
                        onChange={(v) => onContentChange(v)}
                        uploadImages={onUploadImage}
                    />
                </div>

                <ErrorMessage input='content' />
            </FormGroup>
        </>
    );
}

export default Content;
export { TMarkdownImage, ContentInputs };
