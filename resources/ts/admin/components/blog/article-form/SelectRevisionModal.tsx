import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Table } from 'reactstrap';

import { DateTime } from 'luxon';
import classNames from 'classnames';
import Revision from '@admin/utils/api/models/Revision';

interface ISelectRevisionModalProps {
    existing?: Revision;
    revisions: IRevision[];
    onSelected: (revision: IRevision) => void;
    onCancelled: () => void;
}

const SelectRevisionModal: React.FC<ISelectRevisionModalProps> = ({ existing, revisions, onSelected, onCancelled }) => {
    const [selected, setSelected] = React.useState<IRevision>();

    const isSelected = (revision: IRevision) => {
        if (selected !== undefined)
            return selected.uuid === revision.uuid;
        else if (existing !== undefined)
            return existing.revision.uuid === revision.uuid;
        else
            return false;
    }

    return (
        <>
            <Modal isOpen scrollable backdrop='static'>
                <ModalHeader>
                    Restore Revision
                </ModalHeader>
                <ModalBody>
                    <Table hover>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            {(revisions).map((revision, index) => (
                                <tr
                                    key={index}
                                    title={`Created: ${revision.created_at}`}
                                    className={classNames({ 'table-active': isSelected(revision) })}
                                    style={{ cursor: 'pointer' }}
                                    onClick={() => setSelected(revision)}
                                >
                                    <td>{revision.uuid}</td>
                                    <td>{DateTime.fromISO(revision.created_at).toRelative()}</td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </ModalBody>
                <ModalFooter>
                    <Button color="primary" disabled={selected === undefined} onClick={() => selected && onSelected(selected)}>
                        Select
                    </Button>{' '}
                    <Button color="secondary" onClick={onCancelled}>
                        Cancel
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default SelectRevisionModal;
