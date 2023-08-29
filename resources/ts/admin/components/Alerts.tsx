import React from 'react';
import { Alert as ReactstrapAlert, AlertProps } from 'reactstrap';

export type TAlertTypes = 'success' | 'warning' | 'danger' | 'info';

export interface IAlert extends Omit<AlertProps, 'children'> {
    type: TAlertTypes;
    message: string | JSX.Element;
    dismissable?: boolean;
}


type TAlertExtraProps = Omit<AlertProps, 'children' | 'color' | 'key' | 'isOpen' | 'toggle'>;

interface IAlertsProps extends TAlertExtraProps {
    alerts: IAlert[];
    limit?: number;
}

interface IAlertProps extends TAlertExtraProps {
    alert: IAlert;
}

interface IState {
}

/**
 * Displays Bootstrap alerts from either the redux store or properties.
 *
 * @class Alerts
 * @extends {React.Component<IAlertsProps, IState>}
 */
export default class Alerts extends React.Component<IAlertsProps, IState> {
    static Alert: React.FC<IAlertProps> = ({ alert, ...props }) => {
        const [hidden, setHidden] = React.useState(false);

        if (alert.dismissable) {
            const toggleHidden = () => setHidden(!hidden);

            return <ReactstrapAlert color={alert.type} isOpen={!hidden} toggle={toggleHidden} {...props}>{alert.message}</ReactstrapAlert>;
        } else {
            return <ReactstrapAlert color={alert.type}>{alert.message}</ReactstrapAlert>;
        }
    }

    constructor(props: Readonly<IAlertsProps>) {
        super(props);

        this.state = {
        };
    }

    public render() {
        const { alerts, limit, ...alertProps } = this.props;

        return alerts
            .slice(0, limit)
            .map((alert, key) => <Alerts.Alert key={key} alert={alert} {...alertProps} />);
    }
}
