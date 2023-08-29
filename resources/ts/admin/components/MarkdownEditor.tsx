import React from "react";

import { Editor, EditorProps } from '@bytemd/react';

import gfm from '@bytemd/plugin-gfm';
import breaks from '@bytemd/plugin-breaks';
import frontmatter from '@bytemd/plugin-frontmatter';
import gemoji from '@bytemd/plugin-gemoji';
import highlight from '@bytemd/plugin-highlight';
import mediumZoom from '@bytemd/plugin-medium-zoom';

interface IMarkdownEditorProps extends EditorProps {

}

const MarkdownEditor: React.FC<IMarkdownEditorProps> = ({ plugins, ...props }) => {
    const activatedPlugins = React.useMemo(() => {
        if (plugins === undefined)
            return [
                breaks(),
                frontmatter(),
                gemoji(),
                gfm(),
                highlight(),
                mediumZoom()
            ];
        else
            return plugins;
    }, [plugins]);

    return (
        <Editor
            plugins={activatedPlugins}
            {...props}
        />
    );
}

export default MarkdownEditor;
