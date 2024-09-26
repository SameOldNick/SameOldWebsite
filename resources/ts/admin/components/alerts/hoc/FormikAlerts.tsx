import React from "react";
import { AlertProps } from "reactstrap";
import { ConnectedProps, connect } from "react-redux";

import { TComponent } from "@admin/store/slices/alerts";
import Alerts from "@admin/components/alerts/Alerts";
import { FormikErrors, FormikValues } from "formik";

interface IFormikAlerts<IFormikValues extends FormikValues> {
    errors: FormikErrors<IFormikValues>;
}

function FormikAlerts<IFormikValues extends FormikValues = FormikValues>({ errors }: IFormikAlerts<IFormikValues>) {
    const alerts = React.useMemo(() => 
        Object.entries(errors)
            .filter(([, value]) => value)
            .map<IAlert>(([, value]) => ({
                type: 'danger',
                message: value
            })), 
        [errors]
    );

    return (
        <Alerts alerts={alerts} />
    );
};

export default FormikAlerts;
