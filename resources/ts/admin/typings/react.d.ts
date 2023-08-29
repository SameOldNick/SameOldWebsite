/// <reference types="react" />

declare namespace React {
    export interface Component<P = {}, S = {}, SS = any> {
        public setStateAndResolve<K extends keyof S>(
            state: ((prevState: Readonly<S>, props: Readonly<P>) => (Pick<S, K> | S | null)) | (Pick<S, K> | S | null)
        ): Promise<void>;

        public setStateAndResolveWithValue<K extends keyof S, V = any>(
            state: ((prevState: Readonly<S>, props: Readonly<P>) => (Pick<S, K> | S | null)) | (Pick<S, K> | S | null),
            value: V
        ): Promise<V>;
    }
}

