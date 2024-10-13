import React from 'react';

import { EchoContext } from '@admin/utils/echo/context';
import EchoWrapper from '@admin/utils/echo/wrappers/EchoWrapper';

interface IProviderEchoProps extends React.PropsWithChildren {
    factory: () => EchoWrapper;
}

const Provider: React.FC<IProviderEchoProps> = ({ factory, children }) => {
    const echo = React.useMemo(() => {
        try {
            return factory();
        } catch (err) {
            logger.error(`Unable to create Echo instance: ${err}`);

            return null;
        }
    }, [factory]);

    if (!echo) {
        logger.warn('The Echo functionality is disabled.');

        return (
            <>{children}</>
        );
    }

    React.useEffect(() => {
        echo.onConnectionStatesUpdated((state) => {
            logger.info(`Echo connection state updated: ${state}`);
        });
    }, [echo]);

    return (
        <>
            <EchoContext.Provider value={{ echo }}>
                {children}
            </EchoContext.Provider>
        </>
    );
}

export default Provider;
