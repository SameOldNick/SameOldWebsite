import React from 'react';

interface IProps {
    enabled: boolean;
}

/**
 * Causes the browser to warn the user before changing pages.
 */
const UnsavedChangesWarning: React.FC<IProps> = ({ enabled }) => {
    // TODO: Hook on React Router
    React.useEffect(() => {
        const callback = (e: BeforeUnloadEvent) => {
            if (enabled) {
                e.preventDefault();
                e.returnValue = '';
            }
        }

        window.addEventListener('beforeunload', callback);

        return () => window.removeEventListener('beforeunload', callback);

    }, [enabled]);

    return (
        <></>
    );
}

export default UnsavedChangesWarning;
