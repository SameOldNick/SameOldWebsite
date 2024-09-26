import { AlertProps } from 'reactstrap';

declare global {
    export type TAlertTypes = 'success' | 'warning' | 'danger' | 'info';

    export interface IAlert extends Omit<AlertProps, 'children'> {
        type: TAlertTypes;
        message: string | JSX.Element;
        dismissable?: boolean;
    }
}
