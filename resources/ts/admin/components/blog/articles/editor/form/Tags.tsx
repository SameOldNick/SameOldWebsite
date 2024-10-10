import React from 'react';
import { Card, CardBody, CardProps, Col, FormGroup, Label, Row } from 'reactstrap';
import { Tag } from 'react-tag-autocomplete';

import ReactTagsWithSuggestions from '@admin/components/ReactTagsWithSuggestions';

import ErrorMessage from '@admin/components/blog/articles/editor/form/controls/fields/ErrorMessage';
import ArticleEditorContext from '@admin/components/blog/articles/editor/ArticleEditorContext';

interface TagsInputs {
    tags: Tag[];
    onTagsChanged: (tags: Tag[]) => void;
}

type TTagsProps = Omit<CardProps, 'children'>;

const Tags: React.FC<TTagsProps> = ({ ...props }) => {
    const { inputs: { tags, onTagsChanged } } = React.useContext(ArticleEditorContext);

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
                            <ErrorMessage input='tags' />
                        </FormGroup>
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
}

export default Tags;
export { TTagsProps, TagsInputs };
