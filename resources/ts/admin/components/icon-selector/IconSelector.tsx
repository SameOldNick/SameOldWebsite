import React from "react";
import { FaSearch } from 'react-icons/fa';
import { Button, Col, Form, Input, InputGroup, Modal, ModalBody, ModalFooter, ModalHeader, Row } from "reactstrap";

import { IIconType, getAllIcons } from "./utils";
import IconItem from "./IconItem";
import { IHasIconsFile, withIconsFile } from "./WithIcons";

interface IIconSelectorProps extends IHasIconsFile {
    open: boolean;
    onSave: (icon: IIconType) => void;
    onCancel: () => void;
}

const IconSelector: React.FC<IIconSelectorProps> = ({ getAllIcons, open, onSave, onCancel }) => {
    const inputRef = React.createRef<HTMLInputElement>();
    const [icons, setIcons] = React.useState<IIconType[]>([]);
    const [selected, setSelected] = React.useState<IIconType>();

    const allIcons = React.useMemo(() => getAllIcons(), [getAllIcons]);

    const refreshIcons = React.useCallback(() => {
        if (!allIcons) {
            logger.error('Icons are not loaded.');

            return;
        }

        const input = inputRef.current?.value.toLowerCase() || '';
        const found: IIconType[] = [];

        if (input === '') {
            found.push(...allIcons);
        } else {
            for (const icon of allIcons) {
                if (icon.name.toLowerCase().indexOf(input) >= 0) {
                    found.push(icon);
                }
            }
        }

        setIcons(found);
    }, [allIcons]);

    const handleSelect = React.useCallback((icon: IIconType) => {
        setSelected(icon !== selected ? icon : undefined);
    }, [selected]);

    const handleSubmit = React.useCallback((e: React.FormEvent) => {
        e.preventDefault();

        refreshIcons();
    }, [refreshIcons]);

    const handleSave = React.useCallback(() => {
        if (!selected)
            return;

        onSave(selected);
    }, [onSave]);

    return (
        <>
            <Modal isOpen={open} backdrop='static' size="lg" scrollable>
                <ModalHeader>Select Icon</ModalHeader>
                <ModalBody>
                    <Row className="justify-content-center mb-3">
                        <Col xs={8}>
                            <Form onSubmit={handleSubmit}>
                                <InputGroup>
                                    <Input innerRef={inputRef} onChange={refreshIcons} />
                                    <Button type='submit'>
                                        <FaSearch />
                                    </Button>
                                </InputGroup>
                            </Form>
                        </Col>
                    </Row>
                    <Row>
                        {icons.map((icon, index) => (
                            <IconItem
                                key={index}
                                icon={icon}
                                selected={selected && selected === icon ? true : false}
                                onSelect={() => handleSelect(icon)}
                            />
                        ))}
                    </Row>

                </ModalBody>
                <ModalFooter>
                    <Button color="primary" disabled={selected === undefined} onClick={handleSave}>
                        Save
                    </Button>
                    {' '}
                    <Button color="secondary" onClick={onCancel}>
                        Cancel
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default withIconsFile(IconSelector);
