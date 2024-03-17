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

interface IPropsHasAll {
    hasAll: TRole[];
}

interface IPropsOneOf {
    oneOf: TRole[];
}

type TAuthorizedChildren = React.ReactNode | ((authorized: boolean) => React.ReactNode);

interface ISharedProps {
    loading?: React.ReactNode;
    unauthorized?: React.ReactNode;
    children: TAuthorizedChildren;
}

type TProps = ConnectedProps<typeof connector> & (IPropsHasAll | IPropsOneOf) & ISharedProps;

const Authorized: React.FC<TProps> = ({ account: { user }, children, loading = <LoaderOverlay display={{ type: 'over-element' }} />, unauthorized = <></>, fetchUser, ...props }) => {
    const [status, setStatus] = React.useState<'loading' | 'authorized' | 'unauthorized'>('loading');

    const isOneOf = (props: object): props is IPropsOneOf => 'oneOf' in (props as TProps);
    const roles = React.useMemo<TRole[]>(() => isOneOf(props) ? props.oneOf : props.hasAll, [props]);

    const hasRoles = React.useCallback(async (user: User, roles: TRole[]) => isOneOf(props) ? user.hasAnyRoles(...roles) : user.hasAllRoles(...roles), [user, roles]);

    const checkForRoles = async () => {
        setStatus('loading');

        if (user === undefined) {
            await fetchUser();

            return;
        }

        const found = await hasRoles(new User(user), roles);

        setStatus(found ? 'authorized' : 'unauthorized');
    }

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
    }, [status]);

    return (
        <>
            {element}
        </>
    );
}

export default connector(Authorized);
