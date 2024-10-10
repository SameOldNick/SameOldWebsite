import React from 'react';
import { FaInfoCircle } from 'react-icons/fa';
import { FormGroup, Input, InputGroup, InputGroupText, Label, Tooltip } from 'reactstrap';

import ErrorMessage from '@admin/components/blog/articles/editor/form/controls/fields/ErrorMessage';
import DynamicInput from '@admin/components/blog/articles/editor/form/controls/fields/DynamicInput';
import ArticleEditorContext from '@admin/components/blog/articles/editor/ArticleEditorContext';

interface SlugInputs {
    autoGenerateSlug: boolean;
    onAutoGenerateSlugChanged: (checked: boolean) => void;
    slug: string;
    onSlugChanged: (slug: string) => void;
}

type SlugProps = {};

const Slug: React.FC<SlugProps> = ({ }) => {
    const {
        inputs: {
            autoGenerateSlug,
            slug,
            onAutoGenerateSlugChanged,
            onSlugChanged
        }
    } = React.useContext(ArticleEditorContext);

    const [slugTooltipOpen, setSlugTooltipOpen] = React.useState(false);

    const handleAutoGenerateClick = React.useCallback(() => {
        onAutoGenerateSlugChanged(!autoGenerateSlug);
    }, [autoGenerateSlug, onAutoGenerateSlugChanged]);

    const handleSlugChange = React.useCallback((value: string) => {
        onSlugChanged(value);
    }, [onSlugChanged]);

    return (
        <>
            <FormGroup className='has-validation'>
                <Label for='slug'>
                    <a href='#' id='slugTooltip' className='text-decoration-none'>
                        Slug:{' '}
                        <FaInfoCircle />
                    </a>
                </Label>

                <InputGroup>
                    <InputGroupText>
                        <Input
                            type="checkbox"
                            addon
                            name='autoGenerateSlug'
                            id='autoGenerateSlug'
                            aria-label="Enable slug auto generation"
                            checked={autoGenerateSlug}
                            onClick={handleAutoGenerateClick}
                        />
                    </InputGroupText>
                    <DynamicInput
                        type='text'
                        name='slug'
                        id='slug'
                        value={slug}
                        onChange={handleSlugChange}
                        disabled={autoGenerateSlug}
                    />
                    <ErrorMessage input='slug' />

                </InputGroup>

            </FormGroup>

            <Tooltip
                isOpen={slugTooltipOpen}
                target="slugTooltip"
                toggle={() => setSlugTooltipOpen(!slugTooltipOpen)}
            >
                Select the checkbox to auto generate the slug from the title.
            </Tooltip>
        </>
    );
}

export default Slug;
export { SlugProps, SlugInputs };
