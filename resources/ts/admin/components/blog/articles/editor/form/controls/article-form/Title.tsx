import React from 'react';
import { FormGroup, Label } from 'reactstrap';

import ErrorMessage from '@admin/components/blog/articles/editor/form/controls/fields/ErrorMessage';
import DynamicInput from '@admin/components/blog/articles/editor/form/controls/fields/DynamicInput';
import ArticleEditorContext from '@admin/components/blog/articles/editor/ArticleEditorContext';

interface TitleInputs {
    title: string;
    onTitleChanged: (title: string) => void;
}

const Title: React.FC = () => {
    const { inputs: { title, onTitleChanged } } = React.useContext(ArticleEditorContext);

    return (
        <>
            <FormGroup className='has-validation'>
                <Label for='title'>Title:</Label>

                <DynamicInput
                    type='text'
                    name='title'
                    id='title'
                    value={title}
                    onChange={(title) => onTitleChanged(title)}
                />
                <ErrorMessage input='title' />

            </FormGroup>
        </>
    );
}

export default Title;
export { TitleInputs };
