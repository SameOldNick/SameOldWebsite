import HandlerManager from "./HandlerManager";

import ErrorHandler from "./handlers/ErrorHandler";
import FallbackHandler from "./handlers/FallbackHandler";
import ResponseHandler from "./handlers/ResponseHandler";
import StringHandler from "./handlers/StringHandler";

/**
 * Creates error handler manager
 * @returns {HandlerManager}
 * @exports
 */
const createErrorHandler = (): HandlerManager => {
    return new HandlerManager([
        new ResponseHandler(),
        new ErrorHandler(),
        new StringHandler(),
        new FallbackHandler()
    ]);
}

export default createErrorHandler;
