import React from "react";
import { FaSearch } from 'react-icons/fa';
import { Button, Col, Form, Input, InputGroup, Modal, ModalBody, ModalFooter, ModalHeader, Row } from "reactstrap";

import { IIconType } from "./utils";
import IconItem from "./IconItem";
import { IHasIconsFile, withIconsFile } from "./withIconsFile";
import { IPromptModalProps } from "@admin/utils/modals";

type IconSelectorModalProps = IHasIconsFile & IPromptModalProps<IIconType>;

const IconSelectorModal: React.FC<IconSelectorModalProps> = ({ getAllIcons, onSuccess, onCancelled }) => {
    const inputRef = React.useRef<HTMLInputElement>(null);
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
    }, [inputRef.current, allIcons]);

    const handleSelect = React.useCallback((icon: IIconType) => {
        setSelected(icon !== selected ? icon : undefined);
    }, [selected]);

    const handleSubmit = React.useCallback((e: React.FormEvent) => {
        e.preventDefault();

        refreshIcons();
    }, [refreshIcons]);

    const handleSaveClicked = React.useCallback(() => {
        if (!selected) {
            logger.error('The "Save" button was clicked when no icon is selected.');
            return;
        }

        onSuccess(selected);
    }, [selected, onSuccess]);

    const handleCancelClicked = React.useCallback(() => {
        onCancelled();
    }, [onCancelled]);

    return (
        <>
            <Modal isOpen={true} backdrop='static' size="lg" scrollable>
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
                    <Button color="primary" disabled={selected === undefined} onClick={handleSaveClicked}>
                        Save
                    </Button>
                    {' '}
                    <Button color="secondary" onClick={handleCancelClicked}>
                        Cancel
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default withIconsFile(IconSelectorModal);
