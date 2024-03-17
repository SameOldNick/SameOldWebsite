import Authorized from "@admin/middleware/Authorized";

interface IOptions {
    loading?: React.ReactNode;
    unauthorized?: React.ReactNode;
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
export function requiresRoles<TProps extends object>(Component: React.ComponentType<TProps>, roles: string[], options: IOptions = {}) {
    const element: React.FC = (props: any) => (
        <Authorized hasAll={roles} {...options}>
            <Component {...props}  />
        </Authorized>
    );

    element.displayName = `requiresRoles(${Component.displayName || Component.name}, ${roles})`;

    return element;
}

