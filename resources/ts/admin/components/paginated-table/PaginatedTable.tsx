import React from 'react';

import PaginateResponse from '@admin/utils/api/models/PaginateResponse';
import Pages from './Pages';

type TChildrenCallback = (data: unknown[], key: number) => React.ReactNode;

interface PaginatedTableProps {
    initialResponse: IPaginateResponse | IPaginateResponseCollection;
    pullData: (link?: string) => Promise<IPaginateResponse | IPaginateResponseCollection>;
    onUpdate?: (data: unknown[]) => void;
    onError?: (e: unknown) => void;
    loader?: React.ReactNode;
    children: React.ReactNode | TChildrenCallback;
}

interface PaginatedTableHandle {
    currentPage: number;
    reset: () => Promise<void>;
    reload: () => Promise<void>;
}

function PaginatedTable({
    initialResponse,
    children,
    loader,
    ...props
}: PaginatedTableProps, ref: React.ForwardedRef<PaginatedTableHandle>) {
    const [paginateResponse, setPaginateResponse] = React.useState<PaginateResponse>(new PaginateResponse(initialResponse));
    const [data, setData] = React.useState<unknown[]>(initialResponse.data ?? []);
    const [lastLink, setLastLink] = React.useState<string>();
    const [loading, setLoading] = React.useState(false);
    const [renderCount, setRenderCount] = React.useState(1);
    const [currentPage, setCurrentPage] = React.useState(1);

    React.useImperativeHandle(ref, () => ({
        currentPage,
        reset: async () => {
            setPaginateResponse(new PaginateResponse(initialResponse));
            setData(initialResponse.data ?? []);
            setCurrentPage(1);
            setLastLink(undefined);
            setRenderCount(1);
        },
        reload: async () => {
            // TODO: Reload doesn't work when on first page
            setRenderCount((prev) => prev + 1);
        }
    }), [renderCount, currentPage, initialResponse]);

    const tryPullData = React.useCallback(async (link: string) => {
        const { pullData, onError, onUpdate } = props;

        setLoading(true);

        try {
            const response = await pullData(link);
            const paginateResponse = new PaginateResponse(response);

            setPaginateResponse(paginateResponse);
            setData(response.data ?? []);
            setCurrentPage(paginateResponse.meta.current_page);

            if (onUpdate)
                onUpdate(response.data);
        } catch (err) {
            if (onError)
                onError(err);
        } finally {
            setLoading(false);
        }
    }, [props]);

    const handlePageSelect = React.useCallback((link: string) => {
        setLastLink(link);
    }, []);

    React.useEffect(() => {
        if (lastLink)
            tryPullData(lastLink);
    }, [lastLink, renderCount]);

    return (
        <>
            {loading && loader}
            {typeof children === 'function' ? children(data, renderCount) : children}
            {paginateResponse.hasPages && <Pages response={paginateResponse} onPageSelect={handlePageSelect} />}
        </>
    );
}

const ForwardedPaginatedTable = React.forwardRef(PaginatedTable);

export default ForwardedPaginatedTable;
export { PaginatedTableProps, PaginatedTableHandle };
