import { forwardRef, HTMLAttributes, RefObject, useCallback, useEffect, useImperativeHandle, useMemo, useRef, useState } from "react";

import factory from "codemirror-ssr";

import CodeMirror, { EditorConfiguration } from "codemirror";

interface CodeEditorHandle {
    codemirror: typeof CodeMirror;
    editor?: CodeMirror.EditorFromTextArea;
    textArea: RefObject<HTMLTextAreaElement>;
}

interface CodeEditorProps extends Omit<HTMLAttributes<HTMLTextAreaElement>, 'ref'> {
    onCodeMirrorLoaded?: (cm: typeof CodeMirror) => void;
    onCodeMirrorEditorCreated?: (editor: CodeMirror.EditorFromTextArea) => void;
    value?: string;
    options?: EditorConfiguration;
}

const CodeEditor = forwardRef<CodeEditorHandle, CodeEditorProps>(({
    onCodeMirrorLoaded,
    onCodeMirrorEditorCreated,
    value,
    options = {},
    ...props
}: CodeEditorProps, ref) => {
    const editorRef = useRef<HTMLTextAreaElement>(null);
    const [cmEditor, setCmEditor] = useState<CodeMirror.EditorFromTextArea>();

    const codemirror = useMemo(() => {
        const codemirror = factory();

        if (onCodeMirrorLoaded) {
            onCodeMirrorLoaded(codemirror);
        }

        return codemirror;
    }, [onCodeMirrorLoaded]);

    const createEditor = useCallback((textareaRef: HTMLTextAreaElement) => {
        return codemirror.fromTextArea(textareaRef, options);
    }, [codemirror]);

    useEffect(() => {
        if (editorRef.current) {
            const editor = createEditor(editorRef.current);

            setCmEditor(editor);

            if (onCodeMirrorEditorCreated) {
                onCodeMirrorEditorCreated(editor);
            }

            return () => {
                editor.toTextArea();
            }
        }
    }, [codemirror, editorRef, onCodeMirrorEditorCreated]);

    useEffect(() => {
        if (cmEditor && value !== undefined) {
            cmEditor.setValue(value);
        }
    }, [value]);

    useImperativeHandle(ref, () => ({
        codemirror,
        editor: cmEditor,
        textArea: editorRef
    }), [codemirror, cmEditor, editorRef]);

    return (
        <textarea
            ref={editorRef}
            {...props}
        />
    );
});

CodeEditor.displayName = 'CodeEditor';

export default CodeEditor;
export type { CodeEditorProps, CodeEditorHandle };