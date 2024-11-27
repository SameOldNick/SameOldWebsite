import axios from "axios";
import { IErrorHandler } from "../HandlerManager";
import { defaultFormatter } from "@admin/utils/response-formatter/factories";

class StringHandler implements IErrorHandler {
    canHandle(error: unknown) {
        return typeof error === 'string';
    }

    handle(error: unknown) {
        return error as string;
    }

}

export default StringHandler;
