import NullDriver from './drivers/NullDriver';

export type TLogLevels = 'warn' | 'info' | 'error' | 'debug';

export interface ILogDriver {
    warn(message: string, context?: object): void;
    info(message: string, context?: object): void;
    error(message: string, context?: object): void;
    debug(message: string, context?: object): void;
}

export class Logger implements ILogDriver {
    private driver: ILogDriver;

    constructor() {
        this.driver = new NullDriver();
    }

    public setDriver(driver: ILogDriver) {
        this.driver = driver;
    }

    public info(message: any, context?: object) {
        this.log('info', message, context);
    }

    public warn(message: any, context?: object) {
        this.log('warn', message, context);
    }

    public error(message: any, context?: object) {
        this.log('error', message, context);
    }

    public debug(message: any, context?: object) {
        this.log('debug', message, context);
    }

    public log(level: TLogLevels, message: any, context?: object) {
        const formatted = this.formatMessage(message);
        this.driver[level].call(this.driver, formatted, context);
    }

    private formatMessage(message: any) {
        if (typeof message === 'object' && !Object.prototype.hasOwnProperty.call(message, 'toString'))
            return JSON.stringify(message);
        else
            return String(message);
    }
}

const logger = new Logger();

//global.logger = logger;
window.logger = logger;

export default logger;
