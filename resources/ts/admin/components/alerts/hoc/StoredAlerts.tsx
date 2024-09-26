import React from "react";
import { AlertProps } from "reactstrap";
import { ConnectedProps, connect } from "react-redux";

import { TComponent } from "@admin/store/slices/alerts";
import Alerts from "@admin/components/alerts/Alerts";

const connector = connect(
    ({ alerts: { alerts } }: RootState) => ({ stored: alerts })
);

interface IPropsComponent {
    component: TComponent;
}

type TAlertExtraProps = Omit<AlertProps, 'children' | 'color' | 'key' | 'isOpen' | 'toggle'>;

interface IPropsOptions extends TAlertExtraProps {
    limit: number;
}

type TComponentProps = IPropsComponent & Partial<IPropsOptions>;

type TProps = ConnectedProps<typeof connector> & TComponentProps;

const StoredAlerts: React.FC<TProps> = ({ component, stored }) => {
    const alerts = React.useMemo(() => stored[component], [component, stored]);

    return (
        <Alerts alerts={alerts} />
    );
};

export default connector(StoredAlerts);
