import React from 'react';

interface IProps {
    enabled: boolean;
}

const UnsavedChangesWarning: React.FC<IProps> = ({ enabled }) => {
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
