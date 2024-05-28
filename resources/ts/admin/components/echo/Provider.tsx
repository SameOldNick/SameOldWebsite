import React from 'react';
import Echo from 'laravel-echo';

import { EchoContext } from '@admin/utils/echo/context';

interface IProviderEchoProps extends React.PropsWithChildren {
    echo: Echo;
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
