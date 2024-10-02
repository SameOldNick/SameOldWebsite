import React from 'react';
import { ReactTags, TagSuggestion } from 'react-tag-autocomplete';

import { createAuthRequest } from '@admin/utils/api/factories';

type TReactTagsProps = React.ComponentProps<typeof ReactTags>;

interface IReactTagsWrapperProps extends Omit<TReactTagsProps, 'suggestions'> {

}

const ReactTagsWithSuggestions: React.FC<IReactTagsWrapperProps> = ({ ...props }) => {
    const [suggestions, setSuggestions] = React.useState<TagSuggestion[]>([]);

    const getTags = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().get<ITag[]>('tags');

            setSuggestions(response.data.map(({ tag, slug }, index) => ({ label: tag, value: slug ?? index })))
        } catch (e) {
            logger.error(`Unable to get tag suggestions: ${JSON.stringify(e)}`);
        }
    }, []);

    React.useEffect(() => {
        getTags();
    }, []);

    return (
        <ReactTags suggestions={suggestions} {...props} />
    );
};

export default ReactTagsWithSuggestions;
