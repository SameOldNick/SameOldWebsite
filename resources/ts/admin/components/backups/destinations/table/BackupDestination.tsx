import React from 'react';
import { Button, Input } from 'reactstrap';

interface IProps {
    destination: IBackupDestination;
    selected: boolean;
    onSelect: () => void;
    onEditClicked: () => void;
    onDeleteClicked: () => void;
}

const BackupDestination: React.FC<IProps> = ({ destination, selected, onSelect, onEditClicked, onDeleteClicked }) => {
    return (
        <>
            <tr>
                <td>
                    <Input
                        type='checkbox'
                        checked={selected}
                        onChange={onSelect}
                    />
                </td>
                <td>{destination.name}</td>
                <td>{destination.type.toUpperCase()}</td>
                <td>{destination.host}</td>
                <td>{destination.enable ? 'Enabled' : 'Disabled'}</td>
                <td>
                    <Button color='warning' size='sm' className="me-2" onClick={onEditClicked}>Edit</Button>
                    <Button color='danger' size='sm' className="btn-sm" onClick={onDeleteClicked}>Delete</Button>
                </td>
            </tr>
        </>
    );
}

export default BackupDestination;
