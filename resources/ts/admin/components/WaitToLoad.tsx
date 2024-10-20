import React from 'react';

export interface IWaitToLoadHelpers {
    reload: () => Promise<void>;
}

export type TWaitToLoadCallback<T> = (param: T | undefined, err: unknown | undefined, helpers: IWaitToLoadHelpers) => React.ReactNode;

interface IProps<T> {
    callback: () => Promise<T>;
    loading: React.ReactNode;
    maxTime?: number;
    children?: React.ReactNode | TWaitToLoadCallback<T>;
    log?: boolean;
}

interface IIsLoadingState {
    loading: true;
}

interface IFinishedState<T> {
    loading: false;
    returnValue: T;
}

interface IErrorState {
    loading: false;
    error: unknown;
}

type TIsNotLoadingStates<T> = IFinishedState<T> | IErrorState;
type TState<T> = IIsLoadingState | TIsNotLoadingStates<T>;

export interface IWaitToLoadHandle {
    load: () => void;
}

function WaitToLoad<TReturnValue>({ loading, children, callback, maxTime, log = true }: IProps<TReturnValue>, ref: React.ForwardedRef<IWaitToLoadHandle>) {
    let waitTimeout: Timeout | undefined;

    const [state, setState] = React.useState<TState<TReturnValue>>({ loading: true });

    React.useImperativeHandle(ref, () => ({
        /**
         * Calls the load function.
         * @deprecated Use helpers passed to children function instead.
         */
        load() {
            load();
        }
    }));

    const resolved = React.useCallback((value: TReturnValue) => {
        setState({ loading: false, returnValue: value });
    }, []);

    const error = React.useCallback((err: unknown) => {
        setState({ loading: false, error: err });
    }, []);

    const load = React.useCallback(async () => {
        try {
            setState({ loading: true });

            const value = await callback();

            resolved(value);
        } catch (e) {
            if (log)
                logger.error(e);

            error(e);
        }

        if (maxTime) {
            waitTimeout = setTimeout(() => state.loading && setState({ loading: false, error: 'Maximum wait time reached.' }), maxTime);
        }
    }, [callback, maxTime, resolved, error]);

    const isChildrenCallback = React.useCallback((value: any): value is TWaitToLoadCallback<any> => typeof value === 'function', []);

    const isNotLoadingState = React.useCallback((state: TState<TReturnValue>): state is TIsNotLoadingStates<TReturnValue> => !state.loading, []);

    const isErrorState = React.useCallback((state: any): state is IErrorState => !state.loading && state.error !== undefined && state.returnValue === undefined, []);

    const isFinishedState = React.useCallback((state: any): state is IFinishedState<TReturnValue> => !state.loading && state.error === undefined && state.returnValue !== undefined, []);

    const helpers: IWaitToLoadHelpers = React.useMemo(() => ({
        reload: () => load()
    }), [load]);

    const renderChildren = React.useCallback((state: TIsNotLoadingStates<TReturnValue>) => {
        if (isChildrenCallback(children) && isFinishedState(state)) {
            return children(state.returnValue, undefined, helpers);
        } else if (isErrorState(state) && isChildrenCallback(children)) {
            return children(undefined, state.error, helpers);
        } else {
            return children;
        }
    }, [isChildrenCallback, isErrorState, isFinishedState, children, helpers]);

    React.useEffect(() => {
        load();

        return () => {
            if (maxTime || waitTimeout) {
                clearTimeout(waitTimeout);
                waitTimeout = undefined;
            }
        }
    }, []);

    return (
        <>
            {!isNotLoadingState(state) ? loading : renderChildren(state)}
        </>
    );
}

const ForwardedWaitToLoad = React.forwardRef(WaitToLoad) as <TReturnValue>(
    props: IProps<TReturnValue> & React.RefAttributes<IWaitToLoadHandle>
) => ReturnType<typeof WaitToLoad>;

export default ForwardedWaitToLoad;
