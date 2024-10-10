import React from 'react';

import { TitleInputs } from '@admin/components/blog/articles/editor/form/controls/article-form/Title';
import { SlugInputs } from '@admin/components/blog/articles/editor/form/controls/article-form/Slug';
import { ContentInputs } from '@admin/components/blog/articles/editor/form/controls/article-form/Content';
import { SummaryInputs } from '@admin/components/blog/articles/editor/form/controls/article-form/Summary';
import { MainImageInputs } from '@admin/components/blog/articles/editor/form/MainImage';
import { TagsInputs } from '@admin/components/blog/articles/editor/form/Tags';

type ArticleEditorInputs = TitleInputs & SlugInputs & ContentInputs & SummaryInputs & MainImageInputs & TagsInputs;

interface IArticleEditorContext {
    errors: Record<string, string[]>;

    inputs: ArticleEditorInputs;
}

const fallbackFunction = () => {
    throw new Error('Function not implemented. You cannot use article controls outside the ArticleEditorContext provider.');
};

const ArticleEditorContext = React.createContext<IArticleEditorContext>({
    errors: {},
    inputs: {
        title: '',
        onTitleChanged: fallbackFunction,
        autoGenerateSlug: false,
        onAutoGenerateSlugChanged: fallbackFunction,
        slug: '',
        onSlugChanged: fallbackFunction,
        content: '',
        onContentChange: fallbackFunction,
        onUploadImage: fallbackFunction,
        autoGenerateSummary: false,
        summary: '',
        onAutoGenerateSummaryChanged: fallbackFunction,
        onSummaryChanged: fallbackFunction,
        mainImage: undefined,
        onMainImageSelected: fallbackFunction,
        onMainImageRemoved: fallbackFunction,
        tags: [],
        onTagsChanged: fallbackFunction,
    }
});

const hasErrors = (context: IArticleEditorContext, input: string) => Object.keys(context.errors).includes(input) && context.errors[input].length > 0;

export default ArticleEditorContext;
export { IArticleEditorContext, ArticleEditorInputs, hasErrors };
