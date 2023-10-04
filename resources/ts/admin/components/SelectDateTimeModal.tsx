import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, Row, Col, FormGroup, Label, Input, Form } from 'reactstrap';

import { DateTime } from 'luxon';

interface ISelectDateTimeModalProps {
    existing?: DateTime;
    onSelected: (dateTime: DateTime) => void;
    onCancelled: () => void;
}

const SelectDateTimeModal: React.FC<ISelectDateTimeModalProps> = ({ existing, onSelected, onCancelled }) => {
    const [currentDateTime, setCurrentDateTime] = React.useState<DateTime>(DateTime.now());
    const [date, setDate] = React.useState<string>('');
    const [time, setTime] = React.useState<string>('');

    const handleDateChanged = (newValue: string) => {
        setDate(newValue);
    }

    const handleTimeChanged = (newValue: string) => {
        setTime(newValue);
    }

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const selected = DateTime.fromISO(`${date}T${time}`);

        onSelected(selected);
    }

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
                                        onChange={(e) => handleDateChanged(e.currentTarget.value)}
                                        onBlur={(e) => handleDateChanged(e.currentTarget.value)}
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
                                        onChange={(e) => handleTimeChanged(e.currentTarget.value)}
                                        onBlur={(e) => handleTimeChanged(e.currentTarget.value)}
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
