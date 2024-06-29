import React from 'react';
import { Button, ListGroup, ListGroupItem, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';

import { IPromptModalProps } from '@admin/utils/modals';
import { TFilters } from './Settings';

export interface ISelectFiltersModalProps extends IPromptModalProps<TFilters> {
    filters: TFilters;
}

const SelectFiltersModal: React.FC<ISelectFiltersModalProps> = ({ filters, onSuccess, onCancelled }) => {
    const [selected, setSelected] = React.useState<TFilters>(filters);

    const availableFilters = React.useMemo(() => ({
        'profanity': 'Profanity Filter',
        'email': 'E-mail Filter',
        'language': 'Language Filter',
        'link': 'Link Filter'
    }), []);

    const handleFilterClick = React.useCallback((e: React.MouseEvent, filter: string) => {
        e.preventDefault();

        setSelected((value) => value.includes(filter) ? value.filter((el) => el !== filter) : value.concat(filter));
    }, []);

    return (
        <>
            <Modal isOpen scrollable backdrop='static'>
                <ModalHeader>
                    Select Comment Filters
                </ModalHeader>
                <ModalBody>
                    <ListGroup>
                        {Object.entries(availableFilters).map(([key, value], index) => (
                            <ListGroupItem
                                key={index}
                                action
                                active={selected.includes(key)}
                                tag='a'
                                href='#'
                                onClick={(e) => handleFilterClick(e, key)}
                            >
                                {value}
                            </ListGroupItem>
                        ))}
                    </ListGroup>
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

export default SelectFiltersModal;
