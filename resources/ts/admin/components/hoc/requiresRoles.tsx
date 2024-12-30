import Authorized from "@admin/middleware/Authorized";
import Loader from "@admin/components/Loader";
import FourZeroThree from "@admin/pages/errors/FourZeroThree";

interface IOptions {
    loading?: React.ReactNode;
    unauthorized?: React.ReactNode;
    any?: boolean;
}

/**
 * Creates a component that will only be rendered if user has all specified roles
 *
 * @export
 * @template TProps
 * @param {React.ComponentType<TProps>} Component
 * @param {string[]} roles
 * @param {IOptions} [options={}]
 * @returns High ordered component
 */
export function requiresRoles<TProps extends object>(Component: React.ComponentType<TProps>, roles: string[], { any = false, loading, unauthorized }: IOptions = {}) {
    const element: React.FC<TProps> = (props) => {
        return (
            <Authorized roles={roles} hasAll={any !== false} oneOf={any === false} loading={loading} unauthorized={unauthorized}>
                <Component {...props} />
            </Authorized>
        );
    }


    element.displayName = `requiresRoles(${Component.displayName || Component.name}, ${roles})`;

    return element;
}

/**
 * Shortcut for requiring roles to access pages.
 * This loads the appropriate options for accessing pages.
 *
 * @export
 * @template TProps
 * @param {React.ComponentType<TProps>} Component
 * @param {string[]} roles
 * @param {IOptions} [options={}]
 * @returns High ordered component
 */
export function requiresRolesForPage<TProps extends object>(Component: React.ComponentType<TProps>, roles: string[], options: IOptions = {}) {
    return requiresRoles(Component, roles, {
        loading: <Loader display={{ type: 'page', show: true }} />,
        // TODO: Change to 401 (Unauthorized)
        unauthorized: <FourZeroThree />,
        ...options
    });
}
