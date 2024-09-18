import React from 'react';
import { NavLink } from 'react-router-dom';
import { FaEdit, FaTrash, FaUndo } from 'react-icons/fa';
import { Button } from 'reactstrap';

import S from 'string';

import DeleteProjectModal from '@admin/components/projects/DeleteProjectModal';
import RestoreProjectModal from '@admin/components/projects/RestoreProjectModal';

import awaitModalPrompt from '@admin/utils/modals';

interface IProjectListRowProps {
    project: IProject;
    onDeleted: () => void;
    onRestored: () => void;
}

const ProjectListRow: React.FC<IProjectListRowProps> = ({ project, onDeleted, onRestored }) => {
    const handleDeleteClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(DeleteProjectModal, { project });

            onDeleted();
        } catch (err) {
            // Modal was cancelled.
        }
    }, [project, onDeleted]);

    const handleRestoreClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(RestoreProjectModal, { project });

            onRestored();
        } catch (err) {
            // Modal was cancelled.
        }
    }, [project, onRestored]);

    return (
        <>
            <tr>
                <td>{project.id}</td>
                <td>{project.project}</td>
                <td>{S(project.description).truncate(40).s}</td>
                <td>
                    <a href={project.url} target='_blank' rel='noreferrer'>
                        {project.url}
                    </a>
                </td>
                <td>
                    {
                        project.deleted_at === null ?
                            (
                                <>
                                    <Button color='primary' tag={NavLink} to={`edit/${project.id}`} className='me-1'>
                                        <FaEdit />
                                    </Button>
                                    <Button color='danger' onClick={handleDeleteClicked}>
                                        <FaTrash />
                                    </Button>
                                </>
                            ) :
                            (
                                <>
                                    <Button color='primary' onClick={handleRestoreClicked}>
                                        <FaUndo />
                                    </Button>
                                </>
                            )
                    }

                </td>
            </tr>
        </>
    );
}

export default ProjectListRow;
