import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Row, Col, FormGroup, Label, Input, Form } from 'reactstrap';

import { DateTime } from 'luxon';
import { IPromptModalProps } from '@admin/utils/modals';

interface ISelectDateTimeModalProps extends IPromptModalProps<DateTime> {
    existing?: DateTime;
}

const SelectDateTimeModal: React.FC<ISelectDateTimeModalProps> = ({ existing, onSuccess, onCancelled }) => {
    const currentDateTime = React.useMemo(() => existing ?? DateTime.now(), [existing]);

    const [date, setDate] = React.useState<string>(existing?.toFormat('yyyy-MM-dd') ?? '');
    const [time, setTime] = React.useState<string>(existing?.toFormat('HH:mm:ss') ?? '');

    const handleChange = (setter: React.Dispatch<React.SetStateAction<string>>) => (e: React.ChangeEvent<HTMLInputElement>) => {
        setter(e.currentTarget.value);
    };

    const handleSubmit = React.useCallback((e: React.FormEvent) => {
        e.preventDefault();

        const selected = DateTime.fromISO(`${date}T${time}`);

        onSuccess(selected);
    }, [date, time, onSuccess]);

    return (
        <>
            <Modal isOpen scrollable backdrop='static'>
                <Form onSubmit={handleSubmit}>
                    <ModalHeader>
                        Select Date &amp; Time
                    </ModalHeader>
                    <ModalBody>
                        <Row>
                            <Col xs={12}>
                                <p className='fw-bold text-center'>Current Time Zone: {currentDateTime.toFormat('ZZZZZ (ZZ)')}</p>
                            </Col>
                            <Col xs={12}>
                                <FormGroup>
                                    <Label for="date">
                                        Date
                                    </Label>
                                    <Input
                                        id="date"
                                        name="date"
                                        type="date"
                                        value={date}
                                        onChange={handleChange(setDate)}
                                        onBlur={handleChange(setDate)}
                                    />
                                </FormGroup>
                            </Col>
                            <Col xs={12}>
                                <FormGroup>
                                    <Label for="time">
                                        Time
                                    </Label>
                                    <Input
                                        id="time"
                                        name="time"
                                        type="time"
                                        value={time}
                                        onChange={handleChange(setTime)}
                                        onBlur={handleChange(setTime)}
                                    />
                                </FormGroup>
                            </Col>

                        </Row>
                    </ModalBody>
                    <ModalFooter>
                        <Button type='submit' color="primary" disabled={!date || !time}>
                            Select
                        </Button>{' '}
                        <Button color="secondary" onClick={onCancelled}>
                            Cancel
                        </Button>
                    </ModalFooter>
                </Form>
            </Modal>
        </>
    );
}

export default SelectDateTimeModal;
