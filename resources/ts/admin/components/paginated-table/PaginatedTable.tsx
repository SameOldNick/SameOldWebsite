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
    reload: () => Promise<void>;
}

function PaginatedTable<TData>({ initialResponse, children, loader, ...props }: PaginatedTableProps<TData>, ref: React.ForwardedRef<PaginatedTableHandle>) {
    const [lastResponse, setLastResponse] = React.useState<PaginateResponse<TData>>(new PaginateResponse(initialResponse));
    const [lastLink, setLastLink] = React.useState<string>();
    const [loading, setLoading] = React.useState(false);
    const [renderCount, setRenderCount] = React.useState(1);

    React.useImperativeHandle(ref, () => ({
        reload: async () => {
            setRenderCount((prev) => prev + 1);
        }
    }), [renderCount]);

    const tryPullData = React.useCallback(async (link: string) => {
        const { pullData, onError, onUpdate } = props;

        setLoading(true);

        try {
            const response = await pullData(link);
            const paginateResponse = new PaginateResponse(response);

            setLastResponse(paginateResponse);

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
