
import { Middleware } from 'redux';
import { configureStore, Tuple } from '@reduxjs/toolkit'
import { thunk } from 'redux-thunk';
import logger from 'redux-logger';

import reducers from './reducers';

const createRootMiddlewares = () => {
    const middleware: Middleware[] = [thunk];

    if (import.meta.env.VITE_APP_DEBUG)
        middleware.push(logger);

    return new Tuple(...middleware);
}

const factory = () => configureStore({
    middleware: createRootMiddlewares,
    reducer: reducers,
});

export default factory;
