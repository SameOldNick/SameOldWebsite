import { IErrorHandler } from "../HandlerManager";

class ErrorHandler implements IErrorHandler {
    public canHandle(error: unknown) {
        return ErrorHandler.isError(error);
    }

    public handle(error: unknown) {
        if (!ErrorHandler.isError(error)) {
            throw new Error('Error cannot be handled.');
        }

        return error.message;
    }

    public static isError(error: unknown): error is Error {
        if (!error || typeof error !== 'object')
            return false;

        if (!('name' in error && 'message' in error))
            return false;

        return typeof error.name === 'string' && typeof error.name === 'string';

    }
}

export default ErrorHandler;
