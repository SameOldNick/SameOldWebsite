import React from 'react';
import { Col, FormGroup, Label, Row } from 'reactstrap';

import ErrorMessage from '@admin/components/blog/articles/editor/form/controls/fields/ErrorMessage';
import DynamicInput from '@admin/components/blog/articles/editor/form/controls/fields/DynamicInput';
import DynamicCheckbox from '@admin/components/blog/articles/editor/form/controls/fields/DynamicCheckbox';
import ArticleEditorContext from '@admin/components/blog/articles/editor/ArticleEditorContext';

interface SummaryInputs {
    autoGenerateSummary: boolean;
    summary: string;
    onAutoGenerateSummaryChanged: (autoGenerate: boolean) => void;
    onSummaryChanged: (summary: string) => void;
}

type SummaryProps = {};

const Summary: React.FC<SummaryProps> = ({ }) => {
    const { inputs: { autoGenerateSummary, summary, onAutoGenerateSummaryChanged, onSummaryChanged } } = React.useContext(ArticleEditorContext);

    return (
        <>
            <Row>
                <Col xs={12}>
                    <div className='has-validation mb-2'>
                        <Label for='summary'>Summary:</Label>
                        <DynamicInput
                            type='textarea'
                            name='summary'
                            id='summary'
                            value={summary}
                            rows={5}
                            disabled={autoGenerateSummary}
                            onChange={onSummaryChanged}
                        />
                        <ErrorMessage input='summary' />
                    </div>
                </Col>
                <Col xs={12}>
                    <FormGroup check className='mb-3'>
                        <DynamicCheckbox
                            name='summary_auto_generate'
                            id='summary_auto_generate'
                            checked={autoGenerateSummary}
                            onChange={onAutoGenerateSummaryChanged}
                        />
                        <Label check htmlFor='summary_auto_generate'>Auto generate summary</Label>
                    </FormGroup>
                </Col>

            </Row>
        </>
    );
}

export default Summary;
export { SummaryInputs, SummaryProps };
