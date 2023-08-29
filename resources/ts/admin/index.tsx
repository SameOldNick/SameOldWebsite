import React from 'react';
import ReactDOM from 'react-dom';

import injects from '@admin/utils/injects';

import logger from '@admin/utils/logger';
import ConsoleDriver from '@admin/utils/logger/drivers/ConsoleDriver';


import App from '@admin/App';
import reportWebVitals from '@admin/reportWebVitals';

// Setup logger
logger.setDriver(new ConsoleDriver());

if (import.meta.env.VITE_APP_DEBUG) {
    logger.info('Build Information:');
    logger.info(`Environment: ${import.meta.env.VITE_APP_ENV}`);
    logger.info(`Debug mode: ${import.meta.env.VITE_APP_DEBUG ? 'Enabled' : 'Disabled'}`);
    logger.info(`Build date: ${import.meta.env.BUILD_DATE}`);
    logger.info(`React version: ${React.version}`);
    logger.info(`ReactDOM version: ${ReactDOM.version}`);

}

injects();

ReactDOM.render(
    <App />,
    document.getElementById('root')
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
