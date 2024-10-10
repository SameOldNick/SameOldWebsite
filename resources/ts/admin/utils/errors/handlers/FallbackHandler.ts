import axios from "axios";
import { IErrorHandler } from "../HandlerManager";
import { defaultFormatter } from "@admin/utils/response-formatter/factories";

class FallbackHandler implements IErrorHandler {
    canHandle(error: unknown) {
        return true;
    }

    handle(error: unknown) {
        if (typeof error === 'string')
            return error;

        return 'An unknown error occurred.';
    }

}

export default FallbackHandler;
