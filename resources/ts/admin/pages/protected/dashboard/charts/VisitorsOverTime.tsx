import React from "react";
import { Line } from "react-chartjs-2";

import moment from "moment";

import ChartWrapper from "@admin/components/hoc/ChartWrapper";

interface IProps {
    data: IChartVisitors;
}

const VisitorsOverTime: React.FC<IProps> = ({ data }) => {
    const lineChart = React.useMemo(() => {
        return {
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
                labels: Object.keys(data).map((date) => moment(date).format('YYYY-MM-DD')),
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
        };
    }, [data]);

    return (
        <ChartWrapper>
            <Line options={lineChart.options} data={lineChart.data} />
        </ChartWrapper>
    );
}

export default VisitorsOverTime;