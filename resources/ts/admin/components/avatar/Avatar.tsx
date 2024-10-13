import React from 'react';
import { ClipLoader } from 'react-spinners';

import classNames from 'classnames';

import LazyLoadImage from '@admin/components/hoc/LazyLoadImage';

interface IAvatarProps extends Omit<React.HTMLProps<HTMLImageElement>, 'ref' | 'placeholder' | 'onError'> {
    src: string;
}

const Avatar: React.FC<IAvatarProps> = ({ src, className, ...props }) => {
    return (
        <LazyLoadImage
            placeholder={<ClipLoader color='#858796' className='img-profile' />}
            className={classNames('img-profile rounded-circle', className)}
            src={src}
            {...props}
        />
    );
}

export default Avatar;
