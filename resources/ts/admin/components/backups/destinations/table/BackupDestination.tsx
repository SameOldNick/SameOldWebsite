import React from 'react';
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Input } from 'reactstrap';

interface IProps {
    destination: IBackupDestination;
    selected: boolean;
    onSelect: () => void;
    onEditClicked: () => void;
    onDeleteClicked: () => void;
}

const BackupDestination: React.FC<IProps> = ({ destination, selected, onSelect, onEditClicked, onDeleteClicked }) => {
    const [dropdownOpen, setDropdownOpen] = React.useState(false);

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
                    <Dropdown
                        isOpen={dropdownOpen}
                        toggle={() => setDropdownOpen((prev) => !prev)}
                    >
                        <DropdownToggle caret color='primary'>Actions</DropdownToggle>
                        <DropdownMenu>
                            <DropdownItem onClick={onEditClicked}>Edit</DropdownItem>
                            <DropdownItem onClick={onDeleteClicked}>Delete</DropdownItem>
                        </DropdownMenu>
                    </Dropdown>
                </td>
            </tr>
        </>
    );
}

export default BackupDestination;
