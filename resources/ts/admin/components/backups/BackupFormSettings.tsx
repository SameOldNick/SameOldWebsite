import React from 'react';
import { ErrorMessage, Field, Form, Formik, FormikHelpers } from 'formik';
import { Button, Col, Collapse, FormGroup, FormText, Input, Label, List, ListInlineItem, Row, Tooltip, Badge } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';
import { FaInfoCircle } from 'react-icons/fa';

import * as Yup from 'yup';
import axios, { AxiosResponse } from 'axios';
import Swal from 'sweetalert2';
import S from 'string';

import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';
import { IHasRouter } from '@admin/components/hoc/WithRouter';
import FormikAlerts from '@admin/components/alerts/hoc/FormikAlerts';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import classNames from 'classnames';

type TChannels = 'mail' | 'discord' | 'slack';

export interface IFormikValues {
    'notification_channels': TChannels[],
    'notification_to_email': string,
    'notification_from_email': string,
    'notification_from_name': string,
    'notification_discord_webhook': string,
    'notification_discord_username': string,
    'notification_discord_avatar_url': string,
    'notification_slack_webhook': string,
    'notification_slack_username': string,
    'notification_slack_icon': string,
    'notification_slack_channel': string,
}

interface IProps extends IHasRouter {
}


const BackupFormSettings: React.FC<IProps> = ({ router: { navigate } }) => {
    const waitToLoadRef = React.createRef<IWaitToLoadHandle>();

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
    }), []);

    const getBackupSettings = React.useCallback(async () => {
        const response = await createAuthRequest().get<IBackupSetting[]>('/backup/settings');

        return response.data;
    }, []);

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
        }

        const values: IFormikValues = { ...initialValues };

        for (const { key, value } of settings) {
            if (key === 'notification_to_email') {
                values[key] = value.replace(';', ', ');
            } else if (key === 'notification_channel') {
                const channels = value.split(';') as TChannels[];

                values.notification_channels = channels;
            } else if (key in initialValues) {
                const typedKey = key as keyof Omit<IFormikValues, 'notification_channels'>;

                values[typedKey] = value;
            }
        }

        return values;
    }, []);

    const onUpdated = React.useCallback(async (response: AxiosResponse<IBackupSetting[]>) => {
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

    const handleFormSubmit = React.useCallback(async (values: IFormikValues, helpers: FormikHelpers<IFormikValues>) => {
        try {
            const data: Record<string, string | string[]> = {
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
                'notification_slack_channel': values.notification_slack_channel
            };

            const response = await createAuthRequest().post<IBackupSetting[]>('/backup/settings', data);

            await onUpdated(response);
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

    return (
        <>
            <WaitToLoad ref={waitToLoadRef} loading={<Loader display={{ type: 'over-element' }} />} callback={getBackupSettings}>
                {(settings, err) => (
                    <>
                        {err !== undefined && handleError(err)}
                        {settings !== undefined && (
                            <>
                                <Formik<IFormikValues>
                                    validationSchema={schema}
                                    initialValues={getInitialFormValues(settings)}
                                    onSubmit={handleFormSubmit}
                                >
                                    {({ values, errors, touched, isSubmitting }) => (
                                        <>
                                            <Form>
                                                <Row>
                                                    <Col md={12}>
                                                        <FormikAlerts errors={errors} />
                                                    </Col>
                                                </Row>

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
                                                    <small className="form-text text-muted">Separate multiple emails with commas.</small>
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
                                                </FormGroup>

                                                <Row>
                                                    <Col className='text-end'>
                                                        <Button color='primary' type='submit' disabled={isSubmitting}>
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
