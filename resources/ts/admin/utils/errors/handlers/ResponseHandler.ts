import axios from "axios";
import { IErrorHandler } from "../HandlerManager";
import { defaultFormatter } from "@admin/utils/response-formatter/factories";

class ResponseHandler implements IErrorHandler {
    canHandle(error: unknown) {
        return axios.isAxiosError(error);
    }

    handle(error: unknown) {
        const axiosError = axios.isAxiosError(error);

        if (!axiosError) {
            throw new Error('Error cannot be handled.');
        }

        return defaultFormatter().parse(axios.isAxiosError(error) ? error.response : undefined);
    }

}

export default ResponseHandler;
