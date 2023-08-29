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

export default class WaitToLoad<TReturnValue> extends React.Component<IProps<TReturnValue>, TState<TReturnValue>> {
	private _waitTimeout?: NodeJS.Timeout;

	constructor(props: Readonly<IProps<TReturnValue>>) {
		super(props);

		this.state = {
			loading: true
		};
	}

	componentDidMount() {
		this.load();
	}

	componentWillUnmount() {
		const { maxTime } = this.props;

		if (maxTime || this._waitTimeout) {
			clearTimeout(this._waitTimeout);
			this._waitTimeout = undefined;
		}
	}

    public async load() {
        const { callback, maxTime } = this.props;

        try {
            await this.setStateAndResolve({ loading: true });

            const value = await callback();

            this.resolved(value);
        } catch (e) {
            console.error(e);

            this.error(e);
        }

		if (maxTime) {
			this._waitTimeout = setTimeout(() => this.state.loading && this.setState({ loading: false }), maxTime);
		}
    }

    private get helpers(): IWaitToLoadHelpers {
        return {
            reload: () => this.load()
        };
    }

	private resolved(value: TReturnValue) {
		this.setState({ loading: false, returnValue: value });
	}

	private error(err: unknown) {
		this.setState({ loading: false, error: err });
	}

	private isChildrenCallback(value: any): value is TWaitToLoadCallback<any> {
		return typeof value === 'function';
	}

	private isNotLoadingState(state: TState<TReturnValue>): state is TIsNotLoadingStates<TReturnValue> {
		return !state.loading;
	}

	private isErrorState(state: any): state is IErrorState {
		return !state.loading && state.error !== undefined && state.returnValue === undefined;
	}

	private isFinishedState(state: any): state is IFinishedState<TReturnValue> {
		return !state.loading && state.error === undefined && state.returnValue !== undefined;
	}

	private renderChildren(state: TIsNotLoadingStates<TReturnValue>) {
		const { children } = this.props;

		if (this.isChildrenCallback(children) && this.isFinishedState(state)) {
			return children(state.returnValue, undefined, this.helpers);
		} else if (this.isErrorState(state) && this.isChildrenCallback(children)) {
			return children(undefined, state.error, this.helpers);
		} else {
			return children;
		}
	}

	render() {
		const { loading } = this.props;

		return (
			<>
				{!this.isNotLoadingState(this.state) ? loading : this.renderChildren(this.state)}
			</>
		);
	}
}
