import React from "react";
import { Col } from "reactstrap";

import classNames from "classnames";

import Icon from "./Icon";
import { IIconType } from "./utils";

interface IIconItemProps {
    icon: IIconType;
    selected: boolean;
    onSelect: () => void;
}

const IconItem: React.FC<IIconItemProps> = ({ icon, selected, onSelect }) => {
    const [highlight, setHighlight] = React.useState(false);

    return (
        <Col>
            <button
                type="button"
                className={classNames('btn', { active: highlight || selected })}
                onMouseOver={() => setHighlight(true)}
                onMouseOut={() => setHighlight(false)}
                onClick={() => onSelect()}
            >
                <Icon icon={icon} size={24} />
            </button>

        </Col>
    );
}

export default IconItem;
