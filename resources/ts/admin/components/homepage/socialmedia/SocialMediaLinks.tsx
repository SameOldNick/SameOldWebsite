import React from "react";
import { Button, Col, ListGroup, ListGroupItem, Row } from "reactstrap";
import withReactContent from "sweetalert2-react-content";
import { FaSync, FaTrash } from "react-icons/fa";

import axios from "axios";
import Swal from "sweetalert2";

import SocialMediaLink from "./SocialMediaLink";
import SocialMediaLinkPrompt from "./SocialMediaLinkPrompt";

import { createAuthRequest } from "@admin/utils/api/factories";
import { defaultFormatter } from "@admin/utils/response-formatter/factories";
import awaitModalPrompt from "@admin/utils/modals";

interface ISocialMediaLinksProps {
}

interface ISocialMediaLinkItem {
    link: ISocialMediaLink;
    selected: boolean;
}

const SocialMediaLinks: React.FC<ISocialMediaLinksProps> = ({ }) => {
    const [links, setLinks] = React.useState<ISocialMediaLinkItem[]>([]);

    const load = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().get<ISocialMediaLink[]>('social-media');

            setLinks(response.data.map<ISocialMediaLinkItem>((link) => ({ link, selected: false })));
        } catch (err) {
            console.error(err);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred trying to load social media links.`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await load();
        }

    }, []);

    const addLink = React.useCallback(async (link: string) => {
        try {
            const response = await createAuthRequest().post<ISocialMediaLink[]>('social-media', { link });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been added.'
            });

            load();
        } catch (err) {
            console.error(err);

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
                await addLink(link);
        }
    }, []);

    const updateLink = React.useCallback(async (item: ISocialMediaLinkItem, updatedLink: string) => {
        try {
            const response = await createAuthRequest().put<ISocialMediaLink[]>(`social-media/${item.link.id}`, { link: updatedLink });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been updated.'
            });

            load();
        } catch (err) {
            console.error(err);

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
                await updateLink(item, updatedLink);
        }
    }, []);

    const promptDeleteLink = React.useCallback(async (link: ISocialMediaLink) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${link.link}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await deleteLink(link);

    }, []);

    const deleteLink = React.useCallback(async (link: ISocialMediaLink) => {
         try {
            const response = await createAuthRequest().delete<ISocialMediaLink[]>(`social-media/${link.id}`);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been deleted.'
            });

            load();
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to delete social media link: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await deleteLink(link);
        }

    }, []);

    const promptDeleteLinks = React.useCallback(async () => {
        const toDelete = links.filter((value) => value.selected);

        if (toDelete.length === 0) {
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
            text: `Do you really want to remove ${toDelete.length} link(s)?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        for (const item of toDelete) {
            deleteLinksOne(item);
        }

        await load();
    }, []);

    const deleteLinksOne = React.useCallback(async (item: ISocialMediaLinkItem) => {
        try {
            const response = await createAuthRequest().delete<ISocialMediaLink[]>(`social-media/${item.link.id}`);

        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to delete social media link ID "${item.link.id}": ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await deleteLinksOne(item);
        }
    }, []);

    React.useEffect(() => {
        load();
    }, []);

    const hasSelected = React.useMemo(() => {
        for (const { selected } of links) {
            if (selected)
                return true;
        }

        return false;
    }, [links]);

    const onItemSelected = React.useCallback((link: ISocialMediaLink, selected: boolean) => {
        setLinks((links) => links.map((item) => item.link === link ? { link, selected } : item));
    }, []);

    const handleAddLinkClicked = React.useCallback(async () => {
        try {
            const link = await awaitModalPrompt(SocialMediaLinkPrompt);

            await addLink(link);
        } catch (err) {
            // Modal was cancelled.
        }
    }, []);

    const handleEditLinkClicked = React.useCallback(async (item: ISocialMediaLinkItem) => {
        try {
            const updated = await awaitModalPrompt(SocialMediaLinkPrompt, { link: item.link });

            await updateLink(item, updated);
        } catch (err) {
            // Modal was cancelled.
        }
    }, []);

    return (
        <>
            <Row className="mb-3">
                <Col className="d-flex justify-content-between">
                    <div>
                        <Button color='primary' onClick={handleAddLinkClicked}>Add Link</Button>
                    </div>

                    <div>
                        <Button color='primary' className="me-1" onClick={() => load()}>
                            <span className='me-1'>
                                <FaSync />
                            </span>
                            Refresh
                        </Button>

                        <Button color="danger" disabled={!hasSelected} onClick={promptDeleteLinks}>
                            <span className='me-1'>
                                <FaTrash />
                            </span>
                            Delete Selected
                        </Button>
                    </div>
                </Col>
            </Row>

            <Row className="mb-3">
                <Col>
                    <ListGroup>
                        {links.length > 0 ?
                            links.map((item, index) => (
                                <SocialMediaLink
                                    key={index}
                                    link={item.link}
                                    selected={item.selected}
                                    onSelected={(selected) => onItemSelected(item.link, selected)}
                                    onEditClicked={() => handleEditLinkClicked(item)}
                                    onDeleteClicked={() => promptDeleteLink(item.link)}
                                />
                            ))
                            : (
                                <ListGroupItem disabled>No links found.</ListGroupItem>
                            )
                        }
                    </ListGroup>
                </Col>
            </Row>
        </>
    );
}

export default SocialMediaLinks;
