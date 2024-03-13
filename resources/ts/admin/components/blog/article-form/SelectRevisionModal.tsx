import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Table } from 'reactstrap';

import axios from 'axios';
import { DateTime } from 'luxon';
import classNames from 'classnames';
import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';

import { IPromptModalProps } from '@admin/utils/modals';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import Revision from '@admin/utils/api/models/Revision';

export interface ISelectRevisionModalProps extends IPromptModalProps<IRevision> {
    articleId: number;
    existing?: Revision;
}

const SelectRevisionModal: React.FC<ISelectRevisionModalProps> = ({ articleId, existing, onSuccess, onCancelled }) => {
    const waitToLoadRevisionsRef = React.createRef<IWaitToLoadHandle>();
    const [selected, setSelected] = React.useState<IRevision>();

    const loadRevisions = React.useCallback(async () => {
        const response = await createAuthRequest().get<IRevision[]>(`blog/articles/${articleId}/revisions`);

        return response.data;
    }, []);

    const handleLoadRevisionsError = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred loading revisions: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            waitToLoadRevisionsRef.current?.load();
        } else {
            onCancelled();
        }
    }, [onCancelled]);

    const isSelected = React.useCallback((revision: IRevision) => {
        if (selected !== undefined)
            return selected.uuid === revision.uuid;
        else if (existing !== undefined)
            return existing.revision.uuid === revision.uuid;
        else
            return false;
    }, [existing]);

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
                            <WaitToLoad<IRevision[]>
                                ref={waitToLoadRevisionsRef}
                                loading={<Loader display={{ type: 'over-element' }} />}
                                callback={loadRevisions}
                            >
                                {(revisions, err) => (
                                    <>
                                        {err && handleLoadRevisionsError(err)}
                                        {revisions && (revisions).map((revision, index) => (
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

                                    </>
                                )}
                            </WaitToLoad>
                        </tbody>
                    </Table>
                </ModalBody>
                <ModalFooter>
                    <Button color="primary" disabled={selected === undefined} onClick={() => selected && onSuccess(selected)}>
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
