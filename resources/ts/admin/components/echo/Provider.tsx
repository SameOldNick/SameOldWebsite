import React from 'react';

import { EchoContext } from '@admin/utils/echo/context';
import EchoWrapper from '@admin/utils/echo/wrappers/EchoWrapper';

interface IProviderEchoProps extends React.PropsWithChildren {
    echo: EchoWrapper;
}

const Provider: React.FC<IProviderEchoProps> = ({ echo, children }) => {
    return (
        <>
            <EchoContext.Provider value={{ echo }}>
                {children}
            </EchoContext.Provider>
        </>
    );
}

export default Provider;
