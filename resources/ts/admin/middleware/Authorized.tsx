import React from 'react';
import { connect, ConnectedProps } from 'react-redux';

import LoaderOverlay from '@admin/components/Loader';

import { fetchUser } from '@admin/store/slices/account';
import User from '@admin/utils/api/models/User';

const connector = connect(
    ({ account }: RootState) => ({ account }),
    { fetchUser }
);

type TRole = string;

type TAuthorizedChildren = React.ReactNode | ((authorized: boolean) => React.ReactNode);

/**
 * Prop types for Authorized component
 *
 * @interface IAuthorizedProps
 */
interface IAuthorizedProps {
    /**
     * The roles to check for.
     *
     * @type {TRole[]}
     * @memberof IAuthorizedProps
     */
    roles: TRole[];
    /**
     * Checks user has all roles.
     * If not specified, default is true.
     * If both hasAll and oneOf is the same value (true/false), hasAll is used.
     *
     * @type {boolean}
     * @memberof IAuthorizedProps
     */
    hasAll?: boolean;
    /**
     * Checks user has one of roles.
     * If not specified, default is false.
     * If both hasAll and oneOf is the same value (true/false), hasAll is used.
     *
     * @type {boolean}
     * @memberof IAuthorizedProps
     */
    oneOf?: boolean;
    /**
     * Element to display when checking for authorization.
     * Default is <LoaderOverlay />
     *
     * @type {React.ReactNode}
     * @memberof IAuthorizedProps
     */
    loading?: React.ReactNode;
    /**
     * Element to display if unauthorized.
     * Default is React fragment.
     *
     * @type {React.ReactNode}
     * @memberof IAuthorizedProps
     */
    unauthorized?: React.ReactNode;
    /**
     * Elements to display when authorized, or, callback that is called when authorized or unauthorized.
     *
     * @type {TAuthorizedChildren}
     * @memberof IAuthorizedProps
     */
    children: TAuthorizedChildren;
}

type TAuthorizedProps = ConnectedProps<typeof connector> & IAuthorizedProps;

const Authorized: React.FC<TAuthorizedProps> = ({
    account: { user },
    roles,
    hasAll = true,
    oneOf = false,
    children,
    loading = <LoaderOverlay display={{ type: 'over-element' }} />,
    unauthorized = <></>,
    fetchUser
}) => {
    const [status, setStatus] = React.useState<'loading' | 'authorized' | 'unauthorized'>('loading');

    const hasRoles = React.useCallback(async (user: User) => oneOf && !hasAll ? user.hasAnyRoles(...roles) : user.hasAllRoles(...roles), [user, roles, oneOf, hasAll]);

    const checkForRoles = React.useCallback(async () => {
        setStatus('loading');

        if (user === undefined) {
            await fetchUser();

            return;
        }

        const found = await hasRoles(new User(user));

        setStatus(found ? 'authorized' : 'unauthorized');
    }, [fetchUser, hasRoles]);

    React.useEffect(() => {
        checkForRoles();
    }, [user, roles]);

    const element = React.useMemo(() => {
        if (status === 'loading') {
            return loading;
        }

        if (typeof children === 'function') {
            return children(status === 'authorized');
        } else {
            return status === 'authorized' ? children : unauthorized;
        }
    }, [status, children, unauthorized]);

    return (
        <>
            {element}
        </>
    );
}

export default connector(Authorized);
