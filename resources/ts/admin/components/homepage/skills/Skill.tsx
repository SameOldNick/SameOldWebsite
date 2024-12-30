import React from 'react';
import { Button, Col } from 'reactstrap';
import { FaEdit, FaTimesCircle } from 'react-icons/fa';

import classNames from 'classnames';

import Icon from '@admin/components/icon-selector/Icon';

import { IHasIconsFile, withIconsFile } from '@admin/components/icon-selector/withIconsFile';

interface ISkillProps extends IHasIconsFile {
    skill: ISkill;
    selected: boolean;

    onEditClicked: () => void;
    onDeleteClicked: () => void;
    onSelected: (selected: boolean) => void;
}

const Skill: React.FC<ISkillProps> = ({ lookupIcon, skill, selected, onSelected, onEditClicked, onDeleteClicked }) => {
    const icon = React.useMemo(() => lookupIcon(skill.icon), [skill]);

    const handleClick = React.useCallback((_e: React.MouseEvent<HTMLElement>) => {
        onSelected(!selected)
    }, [onSelected]);

    const handleEditClick = React.useCallback((e: React.MouseEvent<HTMLButtonElement>) => {
        e.stopPropagation();
        onEditClicked();
    }, [onEditClicked]);

    const handleDeleteClick = React.useCallback((e: React.MouseEvent<HTMLButtonElement>) => {
        e.stopPropagation();
        onDeleteClicked();
    }, [onDeleteClicked]);

    return (
        <Col
            md={3}
            className={classNames('border rounded p-3', { 'bg-body-secondary': selected })}
            onClick={handleClick}
            style={{ cursor: 'pointer' }}
        >
            <div className='d-flex justify-content-center'>
                <div
                    className={classNames('d-flex justify-content-center align-items-center rounded-circle')}
                    style={{ backgroundColor: '#8cbb45', width: 100, height: 100 }}
                >
                    {icon && <Icon icon={icon} size={45} />}
                </div>
            </div>
            <h2 className={classNames('text-center')}>
                {skill.skill}
            </h2>
            <div className='d-flex justify-content-center'>
                <Button color="primary" className='align-middle me-1' onClick={handleEditClick}>
                    <span className='me-1'>
                        <FaEdit />
                    </span>
                    Edit
                </Button>
                <Button color="danger" className='align-middle' onClick={handleDeleteClick}>
                    <span className='me-1'>
                        <FaTimesCircle />
                    </span>
                    Delete
                </Button>
            </div>
        </Col>
    );
}

export default withIconsFile(Skill);
