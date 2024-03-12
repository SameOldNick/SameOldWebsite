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

function WaitToLoad<TReturnValue>({ loading, children, callback, maxTime }: IProps<TReturnValue>, ref: React.ForwardedRef<IWaitToLoadHandle>) {
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

    const load = async () => {
        try {
            setState({ loading: true });

            const value = await callback();

            resolved(value);
        } catch (e) {
            console.error(e);

            error(e);
        }

		if (maxTime) {
			waitTimeout = setTimeout(() => state.loading && setState({ loading: false, error: 'Maximum wait time reached.' }), maxTime);
		}
    }

    const helpers: IWaitToLoadHelpers = {
        reload: () => load()
    }

	const resolved = (value: TReturnValue) => {
		setState({ loading: false, returnValue: value });
	}

	const error = (err: unknown) => {
		setState({ loading: false, error: err });
	}

	const isChildrenCallback = (value: any): value is TWaitToLoadCallback<any> => {
		return typeof value === 'function';
	}

	const isNotLoadingState = (state: TState<TReturnValue>): state is TIsNotLoadingStates<TReturnValue> => {
		return !state.loading;
	}

	const isErrorState = (state: any): state is IErrorState => {
		return !state.loading && state.error !== undefined && state.returnValue === undefined;
	}

	const isFinishedState = (state: any): state is IFinishedState<TReturnValue> => {
		return !state.loading && state.error === undefined && state.returnValue !== undefined;
	}

	const renderChildren = (state: TIsNotLoadingStates<TReturnValue>) => {
		if (isChildrenCallback(children) && isFinishedState(state)) {
			return children(state.returnValue, undefined, helpers);
		} else if (isErrorState(state) && isChildrenCallback(children)) {
			return children(undefined, state.error, helpers);
		} else {
			return children;
		}
	}

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

/**
 * This essentially hacks TSX so that a generic type is required when using the component.
 * React.forwardRef by itself doesn't work with generic types.
 */
interface GenericForwardedRefComponent<T> {
    <TReturnValue>(props: React.PropsWithoutRef<IProps<TReturnValue>> & React.RefAttributes<T>): React.ReactNode;
}

const ForwardedWaitToLoad: GenericForwardedRefComponent<IWaitToLoadHandle> = React.forwardRef<IWaitToLoadHandle, IProps<any>>(WaitToLoad);

export default ForwardedWaitToLoad;
