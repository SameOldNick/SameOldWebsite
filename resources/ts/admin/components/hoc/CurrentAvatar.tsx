import React from "react";

import Avatar from "../Avatar";

import { createAuthRequest, createUrl } from "@admin/utils/api/factories";

interface ICurrentAvatarProps {
    size?: number;
}

const CurrentAvatar = React.forwardRef<Avatar, ICurrentAvatarProps>(({ size }, ref) => {
    const [attrs, setAttrs] = React.useState<Omit<React.HTMLProps<HTMLImageElement>, 'ref'>>();

    const fetchAvatar = async () => {
        try {
            const response = await createAuthRequest().get<IFileUrl>(createUrl('user/avatar'), { size });

            const extraAttrs: React.HTMLProps<HTMLImageElement> = size !== undefined ? { width: size, height: size } : { };

            setAttrs({ src: response.data.url, ...extraAttrs });
        } catch (e) {
            setAttrs({ src: '', alt: 'An error occurred retrieving avatar.' });
        }
    };

    React.useEffect(() => {
        fetchAvatar();

        return () => setAttrs(undefined);
    }, []);

    return <Avatar ref={ref} {...attrs} />
});

CurrentAvatar.displayName = 'CurrentAvatar';

export default CurrentAvatar;
