import React from 'react';
import { Button, Table as ReactstrapTable } from 'reactstrap';
import { FaTimes } from 'react-icons/fa';

import { DateTime } from 'luxon';

import CodeBlock from '@admin/components/CodeBlock';

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
                        <tr key={index}>
                            <td>
                                <input
                                    type="checkbox"
                                    checked={selected.some(({ id }) => id === entry.id)}
                                    onChange={() => onSelect(entry)}
                                />
                            </td>
                            <td>
                                {entry.input === 'email' ? 'E-mail address' : 'Name'}
                            </td>
                            <td>
                                {entry.type === 'static' && entry.value}
                                {entry.type === 'regex' && <CodeBlock options={{ lang: 'regex', theme: 'vitesse-light' }}>{entry.value}</CodeBlock>}
                            </td>
                            <td>
                                {DateTime.fromISO(entry.created_at).toLocaleString(DateTime.DATETIME_FULL)}
                            </td>
                            <td>
                                <Button color='primary' onClick={() => onRemove(entry)}>
                                    <FaTimes />{' '}
                                    Remove
                                </Button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </ReactstrapTable>

        </>
    )
}

export default Table;
