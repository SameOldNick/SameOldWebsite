import React from "react";
import { Button, Input, ListGroupItem } from "reactstrap";
import { FaEdit, FaTimesCircle } from "react-icons/fa";

interface ISocialMediaLinkProps {
    link: ISocialMediaLink;
    selected: boolean;

    onEditClicked: () => void;
    onDeleteClicked: () => void;
    onSelected: (selected: boolean) => void;
}

const SocialMediaLink: React.FC<ISocialMediaLinkProps> = ({ link, selected, onSelected, onEditClicked, onDeleteClicked }) => (
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

export default SocialMediaLink;
