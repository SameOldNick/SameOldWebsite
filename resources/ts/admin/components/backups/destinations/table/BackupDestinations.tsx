import React from 'react';
import { Alert, Col, Input, Row, Table } from 'reactstrap';

import BackupDestination from './BackupDestination';

interface IProps {
    destinations: IBackupDestination[];
    selected: IBackupDestination[];
    onSelect: (destination: IBackupDestination) => void;
    onSelectAll: () => void;
    onTestClicked: (destination: IBackupDestination) => void;
    onEditClicked: (destination: IBackupDestination) => void;
    onDeleteClicked: (destination: IBackupDestination) => void;
}

const BackupDestinations: React.FC<IProps> = ({
    destinations,
    selected,
    onSelectAll,
    onSelect,
    onTestClicked,
    onEditClicked,
    onDeleteClicked
}) => {

    return (
        <>
            {destinations.length === 0 && (
                <Alert color='info'>
                    No backup destinations currently exist. Use the button above to add a destination.
                </Alert>
            )}
            {destinations.length > 0 && (
                <Row>
                    <Col xs={12}>
                        <Table responsive>
                            <thead>
                                <tr>
                                    <th style={{ width: '20px' }}>
                                        <Input
                                            type='checkbox'
                                            checked={selected.length === destinations.length}
                                            onChange={onSelectAll}
                                        />
                                    </th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Host</th>
                                    <th>Enabled</th>
                                    <th style={{ width: '15%' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {destinations.map((destination, i) => (
                                    <BackupDestination
                                        key={i}
                                        selected={selected.map((item) => item.id).includes(destination.id)}
                                        onSelect={() => onSelect(destination)}
                                        destination={destination}
                                        onTestClicked={() => onTestClicked(destination)}
                                        onEditClicked={() => onEditClicked(destination)}
                                        onDeleteClicked={() => onDeleteClicked(destination)}
                                    />
                                ))}
                            </tbody>
                        </Table>
                    </Col>

                </Row>
            )}
        </>
    );
}

export default BackupDestinations;
