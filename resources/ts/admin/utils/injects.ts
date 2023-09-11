import React from 'react';

export default () => {
    React.Component.prototype.setStateAndResolve = function (this: React.Component, state: object | (() => void)) {
        return new Promise<void>((resolve) => this.setState(state, () => resolve(undefined)));
    };

    React.Component.prototype.setStateAndResolveWithValue = function <V = any>(this: React.Component, state: object | (() => void), value: V) {
        return new Promise<V>((resolve) => this.setState(state, () => resolve(value)));
    };

    React.assignRef = function <T>(ref: React.ForwardedRef<T>, instance: T | null): T | null {
        if (typeof ref === 'function')
            ref(instance);
        else if (ref)
            ref.current = instance;

        return instance;
    }
};

