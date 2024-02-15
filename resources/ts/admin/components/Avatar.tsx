import React from 'react';
import { ClipLoader } from 'react-spinners';

import LazyLoadImage from './hoc/LazyLoadImage';

interface IAvatarProps extends Omit<React.HTMLProps<HTMLImageElement>, 'ref' | 'placeholder' | 'onError'> {
    src: string;
}

const Avatar: React.FC<IAvatarProps> = ({ src, ...props }) => {
    return (
        <LazyLoadImage
            placeholder={<ClipLoader color='#858796' className='img-profile' />}
            className="img-profile rounded-circle"
            src={src}
            {...props}
        />
    );
}

export default Avatar;
