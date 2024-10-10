import React from 'react';

import classNames from 'classnames';
import ArticleEditorContext, { hasErrors } from '@admin/components/blog/articles/editor/ArticleEditorContext';

// Define a type that accepts both intrinsic HTML elements and custom React components
type AsProp<T extends React.ElementType> = {
    as: T;
    name: string;
} & Omit<React.ComponentPropsWithoutRef<T>, 'as' | 'name'>; // Omit 'as' to avoid conflicts

function DynamicField<T extends React.ElementType>({ as: Tag, name, ...extraProps }: AsProp<T>) {
    const context = React.useContext(ArticleEditorContext);

    const props = React.useMemo(() => ({
        ...extraProps,
        name,
        className: classNames(extraProps.className, { 'is-invalid': hasErrors(context, name) })
    }), [extraProps]);

    return React.createElement(Tag, props);
}

export default DynamicField;
