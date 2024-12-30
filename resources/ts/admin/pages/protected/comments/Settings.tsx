import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import CommentSettings, { ICommentSettings } from '@admin/components/blog/comments/settings/Settings';
import { requiresRolesForPage } from '@admin/components/hoc/requiresRoles';
import { createAuthRequest } from '@admin/utils/api/factories';
import Loader from '@admin/components/Loader';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import axios from 'axios';
import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';

const Settings: React.FC = () => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);

    const loadSettings = React.useCallback(async () => {
        const response = await createAuthRequest().get<IPageMetaData[]>('blog/settings');

        return response.data.reduce((settings: any, { key, value }) => {
            settings[key] = value;

            return settings;
        }, {}) as ICommentSettings;
    }, []);

    const handleResponse = React.useCallback(async (response: ICommentSettings) => {
        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Comment settings have been updated.`,
        });
    }, []);

    const handleError = React.useCallback(async (err: unknown) => {
        logger.error(err);

        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred: ${message}`,
        });
    }, []);

    const handleSaveSettings = React.useCallback(async (settings: ICommentSettings) => {
        try {
            const response = await createAuthRequest().post<ICommentSettings>('blog/settings', settings);

            await handleResponse(response.data);
        } catch (err) {
            await handleError(err);
        } finally {
            waitToLoadRef.current?.load();
        }
    }, [handleResponse, handleError, waitToLoadRef.current]);

    return (
        <>
            <Helmet>
                <title>Comment Settings</title>
            </Helmet>

            <Heading title='Comment Settings' />

            <WaitToLoad
                ref={waitToLoadRef}
                callback={loadSettings}
                loading={<Loader display={{ type: 'over-element' }} />}
            >
                {(response, err) => (
                    <>
                        {err && logger.error(err)}
                        {response && (
                            <Card>
                                <CardBody>
                                    <CommentSettings settings={response} onSave={handleSaveSettings} />
                                </CardBody>
                            </Card>
                        )}
                    </>

                )}
            </WaitToLoad>
        </>
    );
}

export default requiresRolesForPage(Settings, ['manage_comments']);
