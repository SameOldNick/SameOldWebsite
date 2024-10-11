import React from 'react';

import Authorized from '@admin/middleware/Authorized';

interface IProps extends React.PropsWithChildren {
    roles?: {
        roles: string[];
        oneOf?: boolean;
    } | string[];
}

const AuthorizedWrapper: React.FC<IProps> = ({ roles: rolesProp, children }) => {
    if (rolesProp === undefined)
        return children;

    const roles = React.useMemo(() => Array.isArray(rolesProp) ? rolesProp : rolesProp.roles, [rolesProp]);

    if (roles.length === 0)
        return children;

    const oneOf = React.useMemo(() => {
        if (Array.isArray(rolesProp) || rolesProp.oneOf === undefined)
            return false;

        return rolesProp.oneOf;
    }, [rolesProp]);

    return (
        <Authorized roles={roles} oneOf={oneOf}>
            {children}
        </Authorized>
    );

};

export default AuthorizedWrapper;
