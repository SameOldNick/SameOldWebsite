import React from 'react';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
} from 'chart.js';

interface IChartWrapperProps extends React.PropsWithChildren {

}

let hasInitialized = false;

if (!hasInitialized) {
    ChartJS.register(
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        Title,
        Tooltip,
        Legend,
        ArcElement
    );

    hasInitialized = true;
}

const ChartWrapper: React.FC<IChartWrapperProps> = ({ children }) => {
    return (
        <>
            {children}
        </>
    );
}

export default ChartWrapper;
