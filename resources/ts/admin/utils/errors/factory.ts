import HandlerManager from "./HandlerManager";

import ErrorHandler from "./handlers/ErrorHandler";
import FallbackHandler from "./handlers/FallbackHandler";
import ResponseHandler from "./handlers/ResponseHandler";

/**
 * Creates error handler manager
 * @returns {HandlerManager}
 * @exports
 */
const createErrorHandler = (): HandlerManager => {
    return new HandlerManager([
        new ResponseHandler(),
        new ErrorHandler(),
        new FallbackHandler()
    ]);
}

export default createErrorHandler;
