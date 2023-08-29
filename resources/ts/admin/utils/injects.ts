import React from 'react';

export default () => {
    React.Component.prototype.setStateAndResolve = function (this: React.Component, state: object | (() => void)) {
        return new Promise<void>((resolve) => this.setState(state, () => resolve(undefined)));
    };

    React.Component.prototype.setStateAndResolveWithValue = function <V = any>(this: React.Component, state: object | (() => void), value: V) {
        return new Promise<V>((resolve) => this.setState(state, () => resolve(value)));
    };
};

