import React from "react";
import { Button, Col, Input, ListGroup, ListGroupItem, Row } from "reactstrap";
import withReactContent from "sweetalert2-react-content";
import { FaEdit, FaSync, FaTimesCircle, FaTrash } from "react-icons/fa";

import axios from "axios";
import Swal from "sweetalert2";

import SocialMediaLinkPrompt from "./SocialMediaLinkPrompt";

import { createAuthRequest } from "@admin/utils/api/factories";
import { defaultFormatter } from "@admin/utils/response-formatter/factories";

interface ISocialMediaLinksProps {
}

interface IState {
    links: ISocialMediaLinkItem[];
    addLink: boolean;
    editLink?: ISocialMediaLinkItem;
}

interface ISocialMediaLinkProps {
    link: ISocialMediaLink;
    selected: boolean;

    onEditClicked: () => void;
    onDeleteClicked: () => void;
    onSelected: (selected: boolean) => void;
}

interface ISocialMediaLinkItem {
    link: ISocialMediaLink;
    selected: boolean;
}

export default class SocialMediaLinks extends React.Component<ISocialMediaLinksProps, IState> {
    static SocialMediaLink: React.FC<ISocialMediaLinkProps> = ({ link, selected, onSelected, onEditClicked, onDeleteClicked }) => {
        return (
            <ListGroupItem className="d-flex justify-content-between">
                <span>
                    <Input type="checkbox" className="align-middle" checked={selected} onChange={(e) => onSelected(e.target.checked)} />
                    <Button tag='a' color='link' href={link.link} target='_blank'>{link.link}</Button>
                </span>

                <span>
                    <Button color="link" onClick={() => onEditClicked()}>
                        <FaEdit />
                    </Button>
                    <Button color="link" className="text-danger" onClick={() => onDeleteClicked()}>
                        <FaTimesCircle />
                    </Button>
                </span>
            </ListGroupItem>
        );
    }

    constructor(props: Readonly<ISocialMediaLinksProps>) {
        super(props);

        this.state = {
            links: [],
            addLink: false
        };

        this.addLink = this.addLink.bind(this);
        this.promptDeleteLink = this.promptDeleteLink.bind(this);
        this.promptDeleteLinks = this.promptDeleteLinks.bind(this);

    }

    componentDidMount() {
        this.load();
    }

    private async load() {
        try {
            const response = await createAuthRequest().get<ISocialMediaLink[]>('social-media');

            this.setState({
                links: response.data.map<ISocialMediaLinkItem>((link) => ({ link, selected: false }))
            });
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
                await this.load();
        }

    }

    private onItemSelected(link: ISocialMediaLink, selected: boolean) {
        this.setState(({ links }) => ({ links: links.map((item) => item.link === link ? { link, selected } : item) }));
    }

    private displayEditLink(link: ISocialMediaLinkItem) {
        this.setState({ editLink: link });
    }

    private async addLink(link: string) {
        try {
            const response = await createAuthRequest().post<ISocialMediaLink[]>('social-media', { link });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been added.'
            });

            this.load();
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
                await this.addLink(link);
        }
    }

    private async updateLink(item: ISocialMediaLinkItem, updatedLink: string) {
        try {
            const response = await createAuthRequest().put<ISocialMediaLink[]>(`social-media/${item.link.id}`, { link: updatedLink });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been updated.'
            });

            this.load();
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
                await this.updateLink(item, updatedLink);
        }
    }

    private async promptDeleteLink(link: ISocialMediaLink) {
        const { links } = this.state;

        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${link.link}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await this.deleteLink(link);

    }

    private async deleteLink(link: ISocialMediaLink) {
         try {
            const response = await createAuthRequest().delete<ISocialMediaLink[]>(`social-media/${link.id}`);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media link has been deleted.'
            });

            this.load();
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
                await this.deleteLink(link);
        }

    }

    private async promptDeleteLinks() {
        const { links } = this.state;

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
            this.deleteLinksOne(item);
        }

        await this.load();
    }

    private async deleteLinksOne(item: ISocialMediaLinkItem) {
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
                await this.deleteLinksOne(item);
        }
    }

    render() {
        const { links, addLink, editLink } = this.state;

        const hasSelected = () => {
            for (const { selected } of links) {
                if (selected)
                    return true;
            }

            return false;
        }

        return (
            <>
                {addLink && (
                    <SocialMediaLinkPrompt
                        link={false}
                        onSubmitted={this.addLink}
                        onClose={() => this.setState({ addLink: false })}
                    />
                )}
                {editLink && (
                    <SocialMediaLinkPrompt
                        link={editLink.link}
                        onSubmitted={(newLink) => this.updateLink(editLink, newLink)}
                        onClose={() => this.setState({ editLink: undefined })}
                    />
                )}
                <Row className="mb-3">
                    <Col className="d-flex justify-content-between">
                        <div>
                            <Button color='primary' onClick={() => this.setState({ addLink: true })}>Add Link</Button>
                        </div>

                        <div>
                            <Button color='primary' className="me-1" onClick={() => this.load()}>
                                <span className='me-1'>
                                    <FaSync />
                                </span>
                                Update
                            </Button>

                            <Button color="danger" disabled={!hasSelected()} onClick={this.promptDeleteLinks}>
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
                                links.map(({ link, selected }, index) => (
                                    <SocialMediaLinks.SocialMediaLink
                                        key={index}
                                        link={link}
                                        selected={selected}
                                        onSelected={(selected) => this.onItemSelected(link, selected)}
                                        onEditClicked={() => this.displayEditLink({ link, selected })}
                                        onDeleteClicked={() => this.promptDeleteLink(link)}
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
        )
    }
}
