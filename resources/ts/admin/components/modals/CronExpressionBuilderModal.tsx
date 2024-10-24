import React from 'react';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, FormGroup, Label, Input, Form, FormText } from 'reactstrap';

import { IPromptModalProps } from '@admin/utils/modals';
import { parseCronExpression } from '@admin/utils';

interface CronExpressionBuilderModalProps extends IPromptModalProps<string> {
    existing?: string;
}

const CronExpressionBuilderModal: React.FC<CronExpressionBuilderModalProps> = ({ existing, onSuccess, onCancelled }) => {
    const [minute, setMinute] = React.useState('*');
    const [hour, setHour] = React.useState('*');
    const [dayOfMonth, setDayOfMonth] = React.useState('*');
    const [month, setMonth] = React.useState('*');
    const [dayOfWeek, setDayOfWeek] = React.useState('*');

    const cronExpression = React.useMemo(() =>
        `${minute} ${hour} ${dayOfMonth} ${month} ${dayOfWeek}`,
        [minute, hour, dayOfMonth, month, dayOfWeek]
    );

    const initializeFromExpression = React.useCallback((expression: string) => {
        const parsed = parseCronExpression(expression);

        setMinute(parsed.minute);
        setHour(parsed.hour);
        setDayOfMonth(parsed.dayOfMonth);
        setMonth(parsed.month);
        setDayOfWeek(parsed.dayOfWeek);
    }, []);

    const renderNumberOptions = React.useCallback((min: number, max: number, everyLabel = 'Every') => {
        const options = [
            <option key="*" value="*">{everyLabel}</option>
        ];

        for (let num = min; num <= max; num++) {
            options.push(<option key={num} value={num}>{num}</option>);
        }

        return options;
    }, []);

    const handleSubmit = React.useCallback((e: React.FormEvent) => {
        e.preventDefault();

        onSuccess(cronExpression);
    }, [cronExpression]);

    React.useEffect(() => {
        try {
            if (existing) {
                initializeFromExpression(existing);
            }
        } catch (err) {
            logger.error(err);
        }
    }, [existing, initializeFromExpression]);

    return (
        <>
            <Modal isOpen backdrop='static'>
                <Form onSubmit={handleSubmit}>
                    <ModalHeader>
                        CRON Expression Builder
                    </ModalHeader>
                    <ModalBody>
                        <FormGroup>
                            <Label>CRON Expression</Label>
                            <Input
                                bsSize='lg'
                                type='text'
                                readOnly
                                className='text-center'
                                value={cronExpression}
                            />
                            <FormText>See <a href="https://crontab.guru/" target='_blank'>crontab.guru</a> for information on what this translates to.</FormText>
                        </FormGroup>

                        <FormGroup>
                            <Label>Minute</Label>
                            <Input
                                type='select'
                                value={minute}
                                onChange={(e) => setMinute(e.target.value)}
                            >
                                {renderNumberOptions(0, 59, 'Every Minute')}
                            </Input>
                        </FormGroup>

                        <FormGroup>
                            <Label>Hour</Label>
                            <Input
                                type='select'
                                value={hour}
                                onChange={(e) => setHour(e.target.value)}
                            >
                                {renderNumberOptions(0, 23, 'Every Hour')}
                            </Input>
                        </FormGroup>

                        <FormGroup>
                            <Label>Day of Month</Label>
                            <Input
                                type='select'
                                value={dayOfMonth}
                                onChange={(e) => setDayOfMonth(e.target.value)}
                            >
                                {renderNumberOptions(1, 31, 'Every Day')}
                            </Input>
                        </FormGroup>

                        <FormGroup>
                            <Label>Month</Label>
                            <Input
                                type='select'
                                value={month}
                                onChange={(e) => setMonth(e.target.value)}
                            >
                                <option value="*">Every Month</option>
                                {Array.from({ length: 12 }, (_, i) => (
                                    <option key={i + 1} value={i + 1}>{new Date(0, i).toLocaleString('default', { month: 'long' })}</option>
                                ))}
                            </Input>
                        </FormGroup>

                        <FormGroup>
                            <Label>Day of Week</Label>
                            <Input
                                type='select'
                                value={dayOfWeek}
                                onChange={(e) => setDayOfWeek(e.target.value)}
                            >
                                <option value="*">Every Day of the Week</option>
                                {Array.from({ length: 7 }, (_, i) => (
                                    <option key={i} value={i}>{["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"][i]}</option>
                                ))}
                            </Input>
                        </FormGroup>


                    </ModalBody>
                    <ModalFooter>
                        <Button type='submit' color="primary">
                            Save
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

export default CronExpressionBuilderModal;
