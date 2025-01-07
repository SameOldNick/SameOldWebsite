import { Button } from 'reactstrap';
import { FaTimes } from 'react-icons/fa';

import { DateTime } from 'luxon';

import CodeBlock from '@admin/components/CodeBlock';

interface TableProps {
    entry: IBlacklistEntry;
    selected: boolean;
    onSelect: () => void;
    onRemove: () => void;
}

const Row = ({
    entry,
    selected,
    onSelect,
    onRemove
}: TableProps) => {

    return (
        <tr>
            <td>
                <input
                    type="checkbox"
                    checked={selected}
                    onChange={onSelect}
                />
            </td>
            <td>
                {entry.input === 'email' ? 'E-mail address' : 'Name'}
            </td>
            <td>
                {entry.type === 'static' && entry.value}
                {entry.type === 'regex' && <CodeBlock options={{ lang: 'regex', theme: 'vitesse-light' }}>
                    {entry.value}
                </CodeBlock>}
            </td>
            <td>
                {DateTime.fromISO(entry.created_at).toLocaleString(DateTime.DATETIME_FULL)}
            </td>
            <td>
                <Button color='primary' onClick={onRemove}>
                    <FaTimes />{' '}
                    Remove
                </Button>
            </td>
        </tr>
    )
}

export default Row;
