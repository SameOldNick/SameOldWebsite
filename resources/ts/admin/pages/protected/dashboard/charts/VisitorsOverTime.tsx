import React from "react";
import { Line } from "react-chartjs-2";

import { DateTime } from "luxon";

import ChartWrapper from "@admin/components/wrappers/ChartWrapper";

interface IProps {
    data: IChartVisitors;
}

const VisitorsOverTime: React.FC<IProps> = ({ data }) => {
    const lineChart = React.useMemo(() => ({
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top' as const,
                },
                title: {
                    display: false
                },
            },
        },
        data: {
            labels: Object.keys(data).map((date) => DateTime.fromISO(date).toLocaleString(DateTime.DATE_MED)),
            datasets: [
                {
                    label: 'New Users',
                    data: Object.values(data).map(({ newUsers }) => newUsers),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                },
                {
                    label: 'Total Users',
                    data: Object.values(data).map(({ totalUsers }) => totalUsers),
                    borderColor: 'rgb(53, 162, 235)',
                    backgroundColor: 'rgba(53, 162, 235, 0.5)',
                }
            ],
        }
    }), [data]);

    return (
        <ChartWrapper>
            <Line options={lineChart.options} data={lineChart.data} />
        </ChartWrapper>
    );
}

export default VisitorsOverTime;
