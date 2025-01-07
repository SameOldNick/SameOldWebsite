import React from 'react';
import { Table as ReactstrapTable } from 'reactstrap';

import Row from './Row';

interface TableProps {
    entries: IBlacklistEntry[];
    selected: IBlacklistEntry[];
    onSelectAll: (e: React.ChangeEvent<HTMLInputElement>) => void;
    onSelect: (entry: IBlacklistEntry) => void;
    onRemove: (entry: IBlacklistEntry) => void;
}

const Table = ({
    entries,
    selected,
    onSelectAll,
    onSelect,
    onRemove
}: TableProps) => {

    return (
        <>
            <ReactstrapTable responsive>
                <thead>
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                                checked={entries.every((entry) => selected.some(({ id }) => id === entry.id))}
                                onChange={onSelectAll}
                            />
                        </th>
                        <th>Field</th>
                        <th>Value</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {entries.map((entry, index) => (
                        <Row
                            key={index}
                            entry={entry}
                            selected={selected.some(({ id }) => id === entry.id)}
                            onSelect={() => onSelect(entry)}
                            onRemove={() => onRemove(entry)}
                        />
                    ))}
                </tbody>
            </ReactstrapTable>

        </>
    )
}

export default Table;
