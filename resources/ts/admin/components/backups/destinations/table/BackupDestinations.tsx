import React from 'react';
import { Alert, Col, Input, Row, Table } from 'reactstrap';

import BackupDestination from './BackupDestination';

interface IProps {
    destinations: IBackupDestination[];
    selected: IBackupDestination[];
    onSelect: (destination: IBackupDestination) => void;
    onSelectAll: () => void;
    onEditClicked: (destination: IBackupDestination) => void;
    onDeleteClicked: (destination: IBackupDestination) => void;
}

const BackupDestinations: React.FC<IProps> = ({
    destinations,
    selected,
    onSelectAll,
    onSelect,
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
                        <Table>
                            <thead>
                                <tr>
                                    <th>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {destinations.map((destination, i) => (
                                    <BackupDestination
                                        key={i}
                                        selected={selected.map((item) => item.id).includes(destination.id)}
                                        onSelect={() => onSelect(destination)}
                                        destination={destination}
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