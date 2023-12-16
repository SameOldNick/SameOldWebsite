import React from "react";
import { Table } from "reactstrap";

interface IProps {
    data: IChartLinks;
    total?: number;
}

const PopularLinks: React.FC<IProps> = ({ data, total = 5 }) => {
    const entries = React.useMemo(() => Object.entries(data).sort(([,a], [,b]) => b - a).slice(0, total), [data]);

    return (
        <>
            <Table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Link</th>
                        <th>Users</th>
                    </tr>
                </thead>
                <tbody>
                    {entries.map(([url, users], index) => (
                        <tr key={index}>
                            <td>{index + 1}</td>
                            <td>{url}</td>
                            <td>{users}</td>
                        </tr>
                    ))}
                </tbody>
            </Table>
        </>
    );
}

export default PopularLinks;
