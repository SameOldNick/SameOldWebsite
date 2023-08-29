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
    link: string;
    selected: boolean;

    onEditClicked: () => void;
    onDeleteClicked: () => void;
    onSelected: (selected: boolean) => void;
}

interface ISocialMediaLinkItem {
    link: TSocialMediaLink;
    selected: boolean;
}

export default class SocialMediaLinks extends React.Component<ISocialMediaLinksProps, IState> {
    static SocialMediaLink: React.FC<ISocialMediaLinkProps> = ({ link, selected, onSelected, onEditClicked, onDeleteClicked }) => {
        return (
            <ListGroupItem className="d-flex justify-content-between">
                <span>
                    <Input type="checkbox" className="align-middle" checked={selected} onChange={(e) => onSelected(e.target.checked)} />
                    <Button tag='a' color='link' href={link} target='_blank'>{link}</Button>
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
        this.editLink = this.editLink.bind(this);
        this.deleteLinks = this.deleteLinks.bind(this);

    }

    componentDidMount() {
        this.load();
    }

    private async load() {
        try {
            const response = await createAuthRequest().get<TSocialMediaLink[]>('/pages/homepage/social-media');

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

    private async update(links: TSocialMediaLink[]) {
        try {
            const response = await createAuthRequest().post<TSocialMediaLink[]>('/pages/homepage/social-media', { links });

            this.setState({
                links: response.data.map<ISocialMediaLinkItem>((link) => ({ link, selected: false }))
            });

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Social media links have been updated.'
            });
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to update social media links: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await this.update(links);
        }

    }

    private onItemSelected(link: TSocialMediaLink, selected: boolean) {
        this.setState(({ links }) => ({ links: links.map((item) => item.link === link ? { link, selected } : item) }));
    }

    private displayEditLink(link: ISocialMediaLinkItem) {
        this.setState({ editLink: link });
    }

    private async addLink(newLink: TSocialMediaLink) {
        const { links } = this.state;

        await this.update([...links.map(({ link }) => link), newLink]);
    }

    private async editLink(item: ISocialMediaLinkItem, newLink: TSocialMediaLink) {
        const { links } = this.state;

        await this.update(links.map((value) => value.link === item.link ? newLink : value.link));
    }

    private async deleteLink(link: TSocialMediaLink) {
        const { links } = this.state;

        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${link}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await this.update(links.filter((value) => value.link !== link).map((value) => value.link));

    }

    private async deleteLinks() {
        const { links } = this.state;

        const toKeep = links.filter((value) => !value.selected);

        if (toKeep.length === links.length) {
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
            text: `Do you really want to remove ${links.length - toKeep.length} link(s)?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await this.update(toKeep.map((value) => value.link));
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
                        onSubmitted={this.addLink}
                        onClose={() => this.setState({ addLink: false })}
                    />
                )}
                {editLink && (
                    <SocialMediaLinkPrompt
                        link={editLink.link}
                        onSubmitted={(newLink) => this.editLink(editLink, newLink)}
                        onClose={() => this.setState({ editLink: undefined })}
                    />
                )}
                <Row className="mb-3">
                    <Col className="d-flex justify-content-between">
                        <div>
                            <Button onClick={() => this.setState({ addLink: true })}>Add Link</Button>
                        </div>

                        <div>
                            <Button className="me-1" onClick={() => this.load()}>
                                <span className='me-1'>
                                    <FaSync />
                                </span>
                                Update
                            </Button>

                            <Button color="danger" disabled={!hasSelected()} onClick={this.deleteLinks}>
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
                                        onDeleteClicked={() => this.deleteLink(link)}
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
