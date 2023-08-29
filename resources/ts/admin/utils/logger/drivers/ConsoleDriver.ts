import { ILogDriver, TLogLevels } from '@admin/utils/logger';
const { console: browserConsole } = window;

export default class ConsoleDriver implements ILogDriver {
    public info(message: string, context?: object | undefined) {
        this.log('info', message, context);
    }

    public warn(message: string, context?: object | undefined) {
        this.log('warn', message, context);
    }

    public error(message: string, context?: object | undefined) {
        this.log('error', message, context);
    }

    public debug(message: string, context?: object): void {
        this.log('debug', message, context);
    }

    private log(level: TLogLevels, message: string, context?: object) {
        const params: any[] = [message];

        if (context !== undefined)
            params.push(context);

        browserConsole[level].apply(browserConsole, params);
    }
}
