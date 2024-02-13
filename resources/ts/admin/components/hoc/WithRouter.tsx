import React from "react";
import { NavigateFunction, Params, useLocation, useNavigate, useParams } from "react-router-dom";
import { Location } from "@remix-run/router";

export interface IHasRouter<ParamsOrKey extends string = string> {
    router: {
        location: Location;
        navigate: NavigateFunction;
        params: Readonly<Params<ParamsOrKey>>;
    }
}

/**
 * Generates React function component that adds router prop when rendered.
 *
 * @export
 * @param {React.ComponentType<IHasRouter>} Component
 * @returns Wrapper that sends router prop when called
 */
export function withRouter<TProps extends IHasRouter>(Component: React.ComponentType<TProps>) {
    const element: React.FC = (props: any) => {
        const location = useLocation();
        const navigate = useNavigate();
        const params = useParams();

        return (
            <Component {...props} router={{ location, navigate, params }} />
        );
    }

    element.displayName = `withRouter(${Component.displayName || Component.name})`

    return element;
}
