import React from 'react';

import PaginateResponse from '@admin/utils/api/models/PaginateResponse';
import Pages from './Pages';

type TChildrenCallback<TData = unknown> = (data: TData[], key: number) => React.ReactNode;

interface PaginatedTableProps<TData = unknown> {
    initialResponse: IPaginateResponse<TData> | IPaginateResponseCollection<TData>;
    pullData: (link?: string) => Promise<IPaginateResponse<TData> | IPaginateResponseCollection<TData>>;
    onUpdate?: (data: TData[]) => void;
    onError?: (e: unknown) => void;
    loader?: React.ReactNode;
    children: React.ReactNode | TChildrenCallback<TData>;
}

interface PaginatedTableHandle {
    currentPage: number;
    reset: () => Promise<void>;
    reload: () => Promise<void>;
}

function PaginatedTable<TData>({ initialResponse, children, loader, ...props }: PaginatedTableProps<TData>, ref: React.ForwardedRef<PaginatedTableHandle>) {
    const [lastResponse, setLastResponse] = React.useState<PaginateResponse<TData>>(new PaginateResponse(initialResponse));
    const [lastLink, setLastLink] = React.useState<string>();
    const [loading, setLoading] = React.useState(false);
    const [renderCount, setRenderCount] = React.useState(1);
    const [currentPage, setCurrentPage] = React.useState(1);

    React.useImperativeHandle(ref, () => ({
        currentPage,
        reset: async () => {
            setLastResponse(new PaginateResponse(initialResponse));
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

            setLastResponse(paginateResponse);
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
            {typeof children === 'function' ? children(lastResponse.response.data, renderCount) : children}
            {lastResponse.hasPages && <Pages response={lastResponse} onPageSelect={handlePageSelect} />}
        </>
    );
}

const ForwardedPaginatedTable = React.forwardRef(PaginatedTable) as <TData>(
    props: PaginatedTableProps<TData> & React.RefAttributes<PaginatedTableHandle>
) => ReturnType<typeof PaginatedTable>;

export default ForwardedPaginatedTable;
export { PaginatedTableProps, PaginatedTableHandle };
