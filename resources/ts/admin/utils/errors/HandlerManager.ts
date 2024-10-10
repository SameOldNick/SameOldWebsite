

interface IErrorHandler {
    canHandle: (error: unknown) => boolean;
    handle: (error: unknown) => string;
}

/**
 * Managers error handlers
 *
 * @class HandlerManager
 */
class HandlerManager {
    constructor(
        protected readonly stack: IErrorHandler[]
    ) {

    }

    handle(error: unknown): string {
        for (const handler of this.stack) {
            try {
                if (handler.canHandle(error)) {
                    return handler.handle(error);
                }
            } catch (err) {
                logger.error(err);
            }

        }

        return 'An unknown error occurred.';
    }
}

export default HandlerManager;
export { IErrorHandler };
