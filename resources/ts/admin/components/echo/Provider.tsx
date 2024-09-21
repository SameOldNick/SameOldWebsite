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

    return (
        <>
            <EchoContext.Provider value={{ echo }}>
                {children}
            </EchoContext.Provider>
        </>
    );
}

export default Provider;
