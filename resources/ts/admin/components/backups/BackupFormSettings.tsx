import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikProps } from 'formik';
import { Button, Col, FormGroup, FormText, Input, InputGroup, Label, Row } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';

import * as Yup from 'yup';
import axios from 'axios';
import Swal from 'sweetalert2';
import classNames from 'classnames';
import S from 'string';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import { IHasRouter } from '@admin/components/hoc/WithRouter';
import FormikAlerts from '@admin/components/alerts/hoc/FormikAlerts';
import CronExpressionBuilderModal from '@admin/components/modals/CronExpressionBuilderModal';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';
import { transformCronExpression } from '@admin/utils';
import { fetchSettings, updateSettings } from '@admin/utils/api/endpoints/backup';

type TChannels = 'mail' | 'discord' | 'slack';

type TFrequencies = 'never' | 'daily' | 'weekly' | 'monthly' | 'custom';

export interface IFormikValues {
    'notification_channels': TChannels[];
    'notification_to_email': string;
    'notification_from_email': string;
    'notification_from_name': string;
    'notification_discord_webhook': string;
    'notification_discord_username': string;
    'notification_discord_avatar_url': string;
    'notification_slack_webhook': string;
    'notification_slack_username': string;
    'notification_slack_icon': string;
    'notification_slack_channel': string;

    'backup_frequency': TFrequencies;
    'custom_backup_cron': string;

    'cleanup_frequency': TFrequencies;
    'custom_clean_cron': string;
}

interface IProps extends IHasRouter {
}


const BackupFormSettings: React.FC<IProps> = ({ router: { navigate } }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const formikRef = React.useRef<FormikProps<IFormikValues>>(null);

    const schema = React.useMemo(() => Yup.object().shape({
        notification_channels: Yup.array().of(Yup.string().oneOf(['mail', 'discord', 'slack'])).notRequired(),

        notification_to_email: Yup.string()
            .test('is-valid-emails', 'One or more emails are invalid', (value) => {
                if (!value) return true; // Allow empty field if not required

                const emails = value.split(',').map(email => email.trim());
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                return emails.every(email => emailRegex.test(email));
            })
            .when('notification_channels', {
                is: (notificationChannels: TChannels) => notificationChannels.includes('mail'), // Condition: 'mail' is in the array
                then: (schema) => schema.required('Emails are required when the mail channel is selected'),
                otherwise: (schema) => schema.nullable() // Field is not required otherwise
            }),
        notification_from_email: Yup.string()
            .email().max(255)
            .when('notification_channels', {
                is: (notificationChannels: TChannels) => notificationChannels.includes('mail'), // Condition: 'mail' is in the array
                then: (schema) => schema.required('From email is required when the mail channel is selected'),
                otherwise: (schema) => schema.nullable() // Field is not required otherwise
            }),
        notification_from_name: Yup.string().max(255).nullable(),

        notification_discord_webhook: Yup.string()
            .url().max(255)
            .when('notification_channels', {
                is: (notificationChannels: TChannels) => notificationChannels.includes('discord'), // Condition: 'discord' is in the array
                then: (schema) => schema.required('Webhook URL is required when the Discord channel is selected'),
                otherwise: (schema) => schema.nullable() // Field is not required otherwise
            }),
        notification_discord_username: Yup.string()
            .max(255)
            .when('notification_channels', {
                is: (notificationChannels: TChannels) => notificationChannels.includes('discord'), // Condition: 'discord' is in the array
                then: (schema) => schema.required('Username is required when the Discord channel is selected'),
                otherwise: (schema) => schema.nullable() // Field is not required otherwise
            }),
        notification_discord_avatar_url: Yup.string().nullable().url().max(255),

        notification_slack_webhook: Yup.string()
            .url().max(255)
            .when('notification_channels', {
                is: (notificationChannels: TChannels) => notificationChannels.includes('slack'), // Condition: 'slack' is in the array
                then: (schema) => schema.required('Webhook URL is required when the Slack channel is selected'),
                otherwise: (schema) => schema.nullable() // Field is not required otherwise
            }),
        notification_slack_username: Yup.string()
            .max(255)
            .when('notification_channels', {
                is: (notificationChannels: TChannels) => notificationChannels.includes('slack'), // Condition: 'slack' is in the array
                then: (schema) => schema.required('Username is required when the Slack channel is selected'),
                otherwise: (schema) => schema.nullable() // Field is not required otherwise
            }),
        notification_slack_icon: Yup.string().nullable().max(255),
        notification_slack_channel: Yup.string()
            .max(255)
            .when('notification_channels', {
                is: (notificationChannels: TChannels) => notificationChannels.includes('slack'), // Condition: 'slack' is in the array
                then: (schema) => schema.required('Channel is required when the Slack channel is selected'),
                otherwise: (schema) => schema.nullable() // Field is not required otherwise
            }),

        backup_frequency: Yup.string().oneOf(['never', 'daily', 'weekly', 'monthly', 'custom']),
        custom_backup_cron: Yup.string()
            .when('backup_frequency', {
                is: (frequency: string) => frequency === 'custom',
                then: (schema) => schema.required('Backup Cron expression is required when "custom" is selected.'),
                otherwise: (schema) => schema.nullable()
            })
            .matches(/(((\d+,)+\d+|(\d+(\/|-)\d+)|\d+|\*) ?){5,7}/, 'Backup Cron expression is invalid.'),

        cleanup_frequency: Yup.string().oneOf(['never', 'daily', 'weekly', 'monthly', 'custom']),
        custom_clean_cron: Yup.string()
            .when('cleanup_frequency', {
                is: (frequency: string) => frequency === 'custom',
                then: (schema) => schema.required('Cleanup Cron expression is required when "custom" is selected.'),
                otherwise: (schema) => schema.nullable()
            })
            .matches(/(((\d+,)+\d+|(\d+(\/|-)\d+)|\d+|\*) ?){5,7}/, 'Cleanup Cron expression is invalid.'),
    }), []);

    const getInitialFormValues = React.useCallback((settings: IBackupSetting[]) => {
        const initialValues: IFormikValues = {
            'notification_channels': [],
            'notification_to_email': '',
            'notification_from_email': '',
            'notification_from_name': '',
            'notification_discord_webhook': '',
            'notification_discord_username': '',
            'notification_discord_avatar_url': '',
            'notification_slack_webhook': '',
            'notification_slack_username': '',
            'notification_slack_icon': '',
            'notification_slack_channel': '',

            'backup_frequency': 'never',
            'custom_backup_cron': '',

            'cleanup_frequency': 'never',
            'custom_clean_cron': '',
        }

        const values: IFormikValues = { ...initialValues };

        for (const { key, value } of settings) {
            if (key === 'notification_to_email') {
                values[key] = value.replace(';', ', ');
            } else if (key === 'notification_channel') {
                const channels = value.split(';').filter((channel) => channel) as TChannels[];

                values.notification_channels = channels;
            } else if (key === 'backup_cron' || key === 'cleanup_cron') {
                let expression = value.startsWith('@') ? transformCronExpression(value) : value;

                const mappings: Record<string, TFrequencies> = {
                    '0 0 * * *': 'daily',
                    '0 0 * * 0': 'weekly',
                    '0 0 1 * *': 'monthly',
                };

                if (expression in mappings) {
                    values[key === 'backup_cron' ? 'backup_frequency' : 'cleanup_frequency'] = mappings[expression];
                } else {
                    values[key === 'backup_cron' ? 'backup_frequency' : 'cleanup_frequency'] = 'custom';
                    values[key === 'backup_cron' ? 'custom_backup_cron' : 'custom_clean_cron'] = expression;
                }
            } else if (key in initialValues) {
                const typedKey = key as keyof Omit<IFormikValues, 'notification_channels' | 'backup_frequency' | 'cleanup_frequency' | 'backup_disks'>;

                values[typedKey] = value;
            }
        }

        return values;
    }, []);

    const onUpdated = React.useCallback(async (response: IBackupSetting[]) => {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Updated',
            text: 'Backup settings were successfully updated.',
        });

        waitToLoadRef.current?.load();
    }, [waitToLoadRef.current]);

    const onError = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred: ${message}`,
        });
    }, []);

    const handleFormSubmit = React.useCallback(async (values: IFormikValues) => {
        try {
            const data: Record<string, string | string[] | null> = {
                'notification_channel': values.notification_channels,
                'notification_to_email': values.notification_to_email.split(',').map((email) => email.trim()),
                'notification_from_email': values.notification_from_email,
                'notification_from_name': values.notification_from_name,

                'notification_discord_webhook': values.notification_discord_webhook,
                'notification_discord_username': values.notification_discord_username,
                'notification_discord_avatar_url': values.notification_discord_avatar_url,

                'notification_slack_webhook': values.notification_slack_webhook,
                'notification_slack_username': values.notification_slack_username,
                'notification_slack_icon': values.notification_slack_icon,
                'notification_slack_channel': values.notification_slack_channel,
            };

            switch (values.backup_frequency) {
                case 'daily': {
                    data.backup_cron = '@daily';
                    break;
                }
                case 'weekly': {
                    data.backup_cron = '@weekly';
                    break;
                }
                case 'monthly': {
                    data.backup_cron = '@monthly';
                    break;
                }
                case 'custom': {
                    data.backup_cron = values.custom_backup_cron;
                    break;
                }
                default: {
                    data.backup_cron = null;
                    break;
                }
            }

            switch (values.cleanup_frequency) {
                case 'daily': {
                    data.cleanup_cron = '@daily';
                    break;
                }
                case 'weekly': {
                    data.cleanup_cron = '@weekly';
                    break;
                }
                case 'monthly': {
                    data.cleanup_cron = '@monthly';
                    break;
                }
                case 'custom': {
                    data.cleanup_cron = values.custom_clean_cron;
                    break;
                }
                default: {
                    data.cleanup_cron = null;
                    break;
                }
            }

            const updated = await updateSettings(data);

            await onUpdated(updated);
        } catch (e) {
            await onError(e);
        }
    }, [onUpdated, onError]);

    const handleError = React.useCallback(async (err: unknown) => {
        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `Unable to retrieve meta data: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            waitToLoadRef.current?.load();
        } else {
            navigate(-1);
        }
    }, [waitToLoadRef.current]);

    const handleCustomizeCronClicked = React.useCallback(async (fieldName: string) => {
        try {
            const expression = await awaitModalPrompt(CronExpressionBuilderModal, {});

            formikRef.current?.setFieldValue(fieldName, expression);
        } catch (err) {
            // User cancelled modal.
        }
    }, [formikRef]);

    return (
        <>
            <WaitToLoad ref={waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={fetchSettings}>
                {(settings, err) => (
                    <>
                        {err !== undefined && handleError(err)}
                        {settings !== undefined && (
                            <>
                                <Formik<IFormikValues>
                                    innerRef={formikRef}
                                    validationSchema={schema}
                                    initialValues={getInitialFormValues(settings.current_values)}
                                    onSubmit={handleFormSubmit}
                                >
                                    {({ values, errors, touched, isSubmitting, isValid, ...helpers }) => (
                                        <>
                                            <Form>
                                                <Row>
                                                    <Col md={12}>
                                                        <FormikAlerts errors={errors} />
                                                    </Col>
                                                </Row>

                                                <h5>Backup Settings</h5>

                                                <FormGroup row>
                                                    <Col xs={12}>
                                                        <Label for="disks">Disks</Label>
                                                    </Col>
                                                    <Col>
                                                        {settings.possible_values.backup_disks.map((value, i) => (
                                                            <FormGroup key={i} check inline>
                                                                <Field
                                                                    as={Input}
                                                                    type='checkbox'
                                                                    id={`backup_disks_${value}`}
                                                                    name={`backup_disks`}
                                                                    value={value}
                                                                />
                                                                <Label htmlFor={`backup_disks_${value}`} check>{S(value).humanize().s}</Label>
                                                            </FormGroup>
                                                        ))}
                                                        <ErrorMessage name='backup_disks' component='div' className='invalid-feedback' />
                                                    </Col>
                                                </FormGroup>

                                                <h5>Mail Notification Settings</h5>

                                                <FormGroup switch>
                                                    <Field
                                                        as={Input}
                                                        type='switch'
                                                        role='switch'
                                                        name='notification_channels'
                                                        id='channelMail'
                                                        value='mail'
                                                        checked={values.notification_channels.includes('mail')}
                                                        className={classNames({ 'is-invalid': errors.notification_channels && touched.notification_channels })}
                                                    />
                                                    {' '}
                                                    <Label check for='channelMail'>
                                                        Enable Mail Notifications
                                                    </Label>
                                                </FormGroup>

                                                {/* Mail Settings */}
                                                <FormGroup>
                                                    <Label for="notifyEmails">Emails to Notify</Label>
                                                    <Field
                                                        as={Input}
                                                        type='email'
                                                        name='notification_to_email'
                                                        id='notifyEmails'
                                                        placeholder="Enter emails"
                                                        multiple
                                                        className={classNames({ 'is-invalid': errors.notification_to_email && touched.notification_to_email })}
                                                        disabled={!values.notification_channels.includes('mail')}
                                                    />
                                                    <ErrorMessage name='notification_to_email' component='div' className='invalid-feedback' />
                                                    <FormText>Separate multiple emails with commas.</FormText>
                                                </FormGroup>
                                                <FormGroup>
                                                    <Label for="fromEmail">From Email Address</Label>
                                                    <Field
                                                        as={Input}
                                                        type='email'
                                                        name='notification_from_email'
                                                        id='fromEmail'
                                                        placeholder="from@example.com"
                                                        className={classNames({ 'is-invalid': errors.notification_from_email && touched.notification_from_email })}
                                                        disabled={!values.notification_channels.includes('mail')}
                                                    />
                                                    <ErrorMessage name='notification_from_email' component='div' className='invalid-feedback' />
                                                </FormGroup>
                                                <FormGroup>
                                                    <Label for="fromName">From Name</Label>
                                                    <Field
                                                        as={Input}
                                                        type='text'
                                                        name='notification_from_name'
                                                        id='fromName'
                                                        placeholder="Backup System"
                                                        className={classNames({ 'is-invalid': errors.notification_from_name && touched.notification_from_name })}
                                                        disabled={!values.notification_channels.includes('mail')}
                                                    />
                                                    <ErrorMessage name='notification_from_name' component='div' className='invalid-feedback' />
                                                </FormGroup>

                                                {/* Discord Settings (Shown if Discord is selected) */}
                                                <h5>Discord Notification Settings</h5>

                                                <FormGroup switch>
                                                    <Field
                                                        as={Input}
                                                        type='switch'
                                                        role='switch'
                                                        name='notification_channels'
                                                        id='channelDiscord'
                                                        value='discord'
                                                        checked={values.notification_channels.includes('discord')}
                                                        className={classNames({ 'is-invalid': errors.notification_channels && touched.notification_channels })}
                                                    />
                                                    {' '}
                                                    <Label check for='channelDiscord'>
                                                        Enable Discord Notifications
                                                    </Label>
                                                </FormGroup>

                                                <FormGroup>
                                                    <Label for="discordWebhook">Webhook URL</Label>
                                                    <Field
                                                        as={Input}
                                                        type='url'
                                                        name='notification_discord_webhook'
                                                        id='discordWebhook'
                                                        placeholder="https://discord.com/api/webhooks/..."
                                                        className={classNames({ 'is-invalid': errors.notification_discord_webhook && touched.notification_discord_webhook })}
                                                        disabled={!values.notification_channels.includes('discord')}
                                                    />
                                                    <ErrorMessage name='notification_discord_webhook' component='div' className='invalid-feedback' />
                                                </FormGroup>
                                                <FormGroup>
                                                    <Label for="discordUsername">Username</Label>
                                                    <Field
                                                        as={Input}
                                                        type='text'
                                                        name='notification_discord_username'
                                                        id='discordUsername'
                                                        placeholder="Backup Bot"
                                                        className={classNames({ 'is-invalid': errors.notification_discord_username && touched.notification_discord_username })}
                                                        disabled={!values.notification_channels.includes('discord')}
                                                    />
                                                    <ErrorMessage name='notification_discord_username' component='div' className='invalid-feedback' />
                                                </FormGroup>
                                                <FormGroup>
                                                    <Label for="discordAvatar">Avatar URL</Label>
                                                    <Field
                                                        as={Input}
                                                        type='url'
                                                        name='notification_discord_avatar_url'
                                                        id='discordAvatar'
                                                        placeholder="https://example.com/avatar.png"
                                                        className={classNames({ 'is-invalid': errors.notification_discord_avatar_url && touched.notification_discord_avatar_url })}
                                                        disabled={!values.notification_channels.includes('discord')}
                                                    />
                                                    <ErrorMessage name='notification_discord_avatar_url' component='div' className='invalid-feedback' />
                                                </FormGroup>

                                                {/* Slack Settings (Shown if Slack is selected) */}
                                                <h5>Slack Notification Settings</h5>

                                                <FormGroup switch>
                                                    <Field
                                                        as={Input}
                                                        type='switch'
                                                        role='switch'
                                                        name='notification_channels'
                                                        id='channelSlack'
                                                        value='slack'
                                                        checked={values.notification_channels.includes('slack')}
                                                        className={classNames({ 'is-invalid': errors.notification_channels && touched.notification_channels })}
                                                    />
                                                    {' '}
                                                    <Label check for='channelSlack'>
                                                        Enable Slack Notifications
                                                    </Label>
                                                </FormGroup>

                                                <FormGroup>
                                                    <Label for="slackWebhook">Webhook URL</Label>
                                                    <Field
                                                        as={Input}
                                                        type='url'
                                                        name='notification_slack_webhook'
                                                        id='slackWebhook'
                                                        placeholder="https://hooks.slack.com/services/..."
                                                        className={classNames({ 'is-invalid': errors.notification_slack_webhook && touched.notification_slack_webhook })}
                                                        disabled={!values.notification_channels.includes('slack')}
                                                    />
                                                    <ErrorMessage name='notification_slack_webhook' component='div' className='invalid-feedback' />
                                                </FormGroup>
                                                <FormGroup>
                                                    <Label for="slackUsername">Username</Label>
                                                    <Field
                                                        as={Input}
                                                        type='text'
                                                        name='notification_slack_username'
                                                        id='slackUsername'
                                                        placeholder="Backup Bot"
                                                        className={classNames({ 'is-invalid': errors.notification_slack_username && touched.notification_slack_username })}
                                                        disabled={!values.notification_channels.includes('slack')}
                                                    />
                                                    <ErrorMessage name='notification_slack_username' component='div' className='invalid-feedback' />
                                                </FormGroup>
                                                <FormGroup>
                                                    <Label for="slackIcon">Icon URL</Label>
                                                    <Field
                                                        as={Input}
                                                        type='url'
                                                        name='notification_slack_icon'
                                                        id='slackIcon'
                                                        placeholder="https://example.com/icon.png"
                                                        className={classNames({ 'is-invalid': errors.notification_slack_icon && touched.notification_slack_icon })}
                                                        disabled={!values.notification_channels.includes('slack')}
                                                    />
                                                    <ErrorMessage name='notification_slack_icon' component='div' className='invalid-feedback' />
                                                </FormGroup>
                                                <FormGroup>
                                                    <Label for="slackChannel">Channel</Label>
                                                    <Field
                                                        as={Input}
                                                        type='text'
                                                        name='notification_slack_channel'
                                                        id='slackChannel'
                                                        placeholder="#backups"
                                                        className={classNames({ 'is-invalid': errors.notification_slack_channel && touched.notification_slack_channel })}
                                                        disabled={!values.notification_channels.includes('slack')}
                                                    />
                                                    <ErrorMessage name='notification_slack_channel' component='div' className='invalid-feedback' />
                                                </FormGroup>

                                                <h5>Schedule Settings</h5>

                                                <FormGroup className='has-validation'>
                                                    <Label for="backup_frequency">Backup Frequency</Label>
                                                    <Field
                                                        as={Input}
                                                        type='select'
                                                        name='backup_frequency'
                                                        className={classNames({ 'is-invalid': errors.backup_frequency && touched.backup_frequency })}
                                                    >
                                                        <option value="never">Never</option>
                                                        <option value="daily">Daily</option>
                                                        <option value="weekly">Weekly</option>
                                                        <option value="monthly">Monthly</option>
                                                        <option value="custom">Custom Cron Expression</option>
                                                    </Field>
                                                    <ErrorMessage name='backup_frequency' component='div' className='invalid-feedback' />
                                                </FormGroup>

                                                <FormGroup className={classNames('has-validation', { 'd-none': values.backup_frequency !== 'custom' })}>
                                                    <Label for="custom_backup_cron">Custom Cron Expression</Label>
                                                    <InputGroup>
                                                        <Field
                                                            as={Input}
                                                            type='text'
                                                            name='custom_backup_cron'
                                                            placeholder="e.g. 0 0 * * *"
                                                            className={classNames({ 'is-invalid': errors.custom_backup_cron && touched.custom_backup_cron })}
                                                        />
                                                        <Button color='primary' onClick={() => handleCustomizeCronClicked('custom_backup_cron')}>Customize...</Button>
                                                    </InputGroup>
                                                    <ErrorMessage name='custom_backup_cron' component='div' className='invalid-feedback' />

                                                    <FormText>
                                                        Use Cron syntax. Example: "0 0 * * *" for daily at midnight.
                                                        See <a href='https://crontab.guru/' target='_blank'>crontab.guru</a> for more examples.
                                                    </FormText>
                                                </FormGroup>

                                                <FormGroup className='has-validation'>
                                                    <Label for="cleanup_frequency">Cleanup Frequency</Label>
                                                    <Field
                                                        as={Input}
                                                        type='select'
                                                        name='cleanup_frequency'
                                                        className={classNames({ 'is-invalid': errors.cleanup_frequency && touched.cleanup_frequency })}
                                                    >
                                                        <option value="never">Never</option>
                                                        <option value="daily">Daily</option>
                                                        <option value="weekly">Weekly</option>
                                                        <option value="monthly">Monthly</option>
                                                        <option value="custom">Custom Cron Expression</option>
                                                    </Field>
                                                    <ErrorMessage name='cleanup_frequency' component='div' className='invalid-feedback' />
                                                </FormGroup>

                                                <FormGroup className={classNames('has-validation', { 'd-none': values.cleanup_frequency !== 'custom' })}>
                                                    <Label for="custom_clean_cron">Custom Cleanup Cron Expression</Label>
                                                    <InputGroup>
                                                        <Field
                                                            as={Input}
                                                            type='text'
                                                            name='custom_clean_cron'
                                                            placeholder="e.g. 0 0 * * 0"
                                                            className={classNames({ 'is-invalid': errors.custom_clean_cron && touched.custom_clean_cron })}
                                                        />
                                                        <Button color='primary' onClick={() => handleCustomizeCronClicked('custom_clean_cron')}>Customize...</Button>
                                                    </InputGroup>
                                                    <ErrorMessage name='custom_clean_cron' component='div' className='invalid-feedback' />
                                                    <FormText>
                                                        Use Cron syntax. Example: "0 0 * * 0" for weekly on Sunday.
                                                        See <a href='https://crontab.guru/' target='_blank'>crontab.guru</a> for more examples.
                                                    </FormText>
                                                </FormGroup>

                                                <Row>
                                                    <Col className='text-end'>
                                                        <Button color='primary' type='submit' disabled={!isValid || isSubmitting}>
                                                            Save Settings
                                                        </Button>
                                                    </Col>
                                                </Row>
                                            </Form>
                                        </>
                                    )}
                                </Formik>
                            </>
                        )}
                    </>
                )}
            </WaitToLoad>

        </>
    );
}

export default BackupFormSettings;
