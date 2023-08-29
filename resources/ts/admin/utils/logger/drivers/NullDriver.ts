import { ILogDriver } from '@admin/utils/logger';

export default class NullDriver implements ILogDriver {
    public warn(message: string, context?: object): void {
        return;
    }

    public info(message: string, context?: object): void {
        return;
    }

    public error(message: string, context?: object): void {
        return;
    }

    public log(message: string, context?: object): void {
        return;
    }

    public debug(message: string, context?: object): void {
        return;
    }
}
