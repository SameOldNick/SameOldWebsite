import React from "react";
import { Button, Col, ListGroup, ListGroupItem, Row } from "reactstrap";
import withReactContent from "sweetalert2-react-content";
import { FaSync, FaTrash } from "react-icons/fa";

import axios from "axios";
import Swal from "sweetalert2";

import SocialMediaLink from "./SocialMediaLink";
import SocialMediaLinkPrompt from "./SocialMediaLinkPrompt";
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import LoadError from "@admin/components/LoadError";
import Loader from "@admin/components/Loader";

import { defaultFormatter } from "@admin/utils/response-formatter/factories";
import awaitModalPrompt from "@admin/utils/modals";
import { addSocialMediaLink, deleteSocialMediaLink, loadSocialMediaLinks, updateSocialMediaLink } from "@admin/utils/api/endpoints/social-media-links";

const SocialMediaLinks: React.FC = () => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const [selected, setSelected] = React.useState<ISocialMediaLink[]>([]);

    const load = React.useCallback(async () => loadSocialMediaLinks(), []);

    const reload = React.useCallback(() => {
        waitToLoadRef.current?.load();

        setSelected([]);
    }, [waitToLoadRef.current]);

    const handleAddLink = React.useCallback(async (link: string) => {
        try {
            await addSocialMediaLink(link);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been added.'
            });
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to add social media link: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await handleAddLink(link);
        }
    }, []);

    const handleUpdateLink = React.useCallback(async (item: ISocialMediaLink, updatedLink: string) => {
        if (!item.id) {
            logger.error('Social media link is missing ID.');
            return;
        }

        try {
            await updateSocialMediaLink(item.id, updatedLink);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been updated.'
            });
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to update social media link: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await handleUpdateLink(item, updatedLink);
        }
    }, []);

    const tryDelete = React.useCallback(async (item: ISocialMediaLink) => {
        if (!item.id) {
            logger.error('Social media link is missing ID.');
            return;
        }

        try {
            await deleteSocialMediaLink(item.id);
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to delete social media link ID "${item.id}": ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await tryDelete(item);
        }
    }, []);

    const handleItemSelected = React.useCallback((link: ISocialMediaLink, selected: boolean) => {
        setSelected((prev) => selected ? prev.concat(link) : prev.filter((item) => item !== link));
    }, []);

    const handleAddLinkClicked = React.useCallback(async () => {
        try {
            const link = await awaitModalPrompt(SocialMediaLinkPrompt);

            await handleAddLink(link);
        } catch (err) {
            // Modal was cancelled.
        } finally {
            reload();
        }
    }, [reload, handleAddLink]);

    const handleEditLinkClicked = React.useCallback(async (item: ISocialMediaLink) => {
        try {
            const updated = await awaitModalPrompt(SocialMediaLinkPrompt, { link: item });

            await handleUpdateLink(item, updated);
        } catch (err) {
            // Modal was cancelled.
        } finally {
            reload();
        }
    }, [reload, handleUpdateLink]);

    const handleDeleteSelectedClicked = React.useCallback(async () => {
        if (selected.length === 0) {
            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `No links are selected.`
            });

            return;
        }

        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove ${selected.length} link(s)?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        for (const item of selected) {
            tryDelete(item);
        }

        reload();
    }, [reload, tryDelete, selected]);

    const handleDeleteItemClicked = React.useCallback(async (link: ISocialMediaLink) => {
        if (!link.id) {
            logger.error('Social media link is missing ID.');
            return;
        }

        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${link.link}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        try {
            await deleteSocialMediaLink(link.id);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been deleted.'
            });
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to delete social media link: ${message}`,
                showConfirmButton: false,
                showCancelButton: true
            });
        } finally {
            reload();

        }
    }, [reload]);

    return (
        <>
            <Row className="mb-3">
                <Col className="d-flex justify-content-between">
                    <div>
                        <Button color='primary' onClick={handleAddLinkClicked}>Add Link</Button>
                    </div>

                    <div>
                        <Button color='primary' className="me-1" onClick={() => reload()}>
                            <span className='me-1'>
                                <FaSync />
                            </span>
                            Refresh
                        </Button>

                        <Button color="danger" disabled={selected.length === 0} onClick={handleDeleteSelectedClicked}>
                            <span className='me-1'>
                                <FaTrash />
                            </span>
                            Delete Selected
                        </Button>
                    </div>
                </Col>
            </Row>

            <WaitToLoad
                ref={waitToLoadRef}
                callback={load}
                loading={<Loader display={{ type: 'over-element' }} />}
            >
                {(response, err, { reload }) => (
                    <>
                        {response && (
                            <Row className="mb-3">
                                <Col>
                                    <ListGroup>
                                        {response.length > 0 ?
                                            response.map((link, index) => (
                                                <SocialMediaLink
                                                    key={index}
                                                    link={link}
                                                    selected={selected.includes(link)}
                                                    onSelected={(selected) => handleItemSelected(link, selected)}
                                                    onEditClicked={() => handleEditLinkClicked(link)}
                                                    onDeleteClicked={() => handleDeleteItemClicked(link)}
                                                />
                                            ))
                                            : (
                                                <ListGroupItem disabled>No links found.</ListGroupItem>
                                            )
                                        }
                                    </ListGroup>
                                </Col>
                            </Row>
                        )}
                        {err && (
                            <LoadError
                                error={err}
                                onTryAgainClicked={() => reload()}
                                onGoBackClicked={() => window.history.back()}
                            />
                        )}
                    </>
                )}
            </WaitToLoad>

        </>
    );
}

export default SocialMediaLinks;
