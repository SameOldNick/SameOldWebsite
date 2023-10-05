import React from 'react';
import { Card, CardBody, CardProps, Col, FormGroup, Label, Row } from 'reactstrap';
import { ErrorMessage } from 'formik';
import { Tag } from 'react-tag-autocomplete';

import ReactTagsWithSuggestions from '@admin/components/ReactTagsWithSuggestions';

interface ITagsProps extends Omit<CardProps, 'children'> {
    tags: Tag[];
    onTagsChanged: (tags: Tag[]) => void;
}

const Tags: React.FC<ITagsProps> = ({ tags, onTagsChanged, ...props }) => {
    return (
        <Card {...props}>
            <CardBody>
                <Row>

                    <Col xs={12}>
                        <FormGroup className='has-validation'>
                            <Label for='tags'>Tags:</Label>
                            <ReactTagsWithSuggestions
                                allowNew
                                selected={tags}
                                onAdd={(tag) => onTagsChanged([...tags, tag])}
                                onDelete={(i) => onTagsChanged(tags.filter((_, index) => i !== index))}
                            />
                            <ErrorMessage name='tags' component='div' className='invalid-feedback' />
                        </FormGroup>
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
}

export default Tags;
