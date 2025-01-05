import { useCallback, useEffect, useState } from "react"
import { codeToHtml } from 'shiki'

interface Props {
    children: string
    options: Parameters<typeof codeToHtml>[1];
}

const CodeBlock = ({ children, options }: Props) => {
    const [html, setHtml] = useState<string>();

    const convertToHtml = useCallback(async (code: string) => {
        return codeToHtml(code, options);
    }, []);

    useEffect(() => {
        convertToHtml(children).then((html) => setHtml(html));
    }, []);

    return (
        <>
            {html && <div dangerouslySetInnerHTML={{ __html: html }} />}
        </>
    );
}

export default CodeBlock;