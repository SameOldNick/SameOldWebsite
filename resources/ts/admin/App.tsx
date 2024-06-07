import React from 'react';
import { Provider } from 'react-redux';
import { Helmet } from 'react-helmet';
import { BrowserRouter as Router } from "react-router-dom";
import { IconContext } from "react-icons";

import store from '@admin/store/index';
import Pages from '@admin/pages';
import EchoProvider from '@admin/components/echo/Provider';
import createEcho, { attachEchoToWindow, wrapEcho, attachEchoWrapperToWindow } from '@admin/utils/echo/echo';

interface IProps {

}

const App: React.FC<IProps> = ({ }) => {
    const echo = React.useMemo(() => {
        const echo = attachEchoToWindow(createEcho());

        return attachEchoWrapperToWindow(wrapEcho(echo));
    }, []);

    return (
        <>
            <Helmet titleTemplate='%s | Same Old Nick' />

            <Provider store={store}>
                <EchoProvider echo={echo}>
                    <IconContext.Provider value={{ className: 'react-icons' }}>
                        <Router>
                            <Pages />
                        </Router>
                    </IconContext.Provider>
                </EchoProvider>
            </Provider>
        </>
    );
}

export default App;
