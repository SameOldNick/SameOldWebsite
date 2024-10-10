import React from 'react';
import { FaRegImage } from 'react-icons/fa';

interface ICurrentImage {
    src: string;
    description: string;
}

interface IProps {
    current?: ICurrentImage;
}

const ImageDisplay: React.FC<IProps> = ({ current }) => {
    return (
        <>
            {current === undefined && (
                <div className='text-center'>
                    <FaRegImage size='200' />
                    <p className='mb-0 fw-bold'>No Image Selected</p>
                </div>
            )}

            {current !== undefined && (
                <div className='text-center'>
                    <img src={current.src} className='img-fluid mb-3' style={{ maxHeight: '250px' }} />
                    <p className='mb-0 fw-bold'>{current.description}</p>
                </div>
            )}
        </>
    );
}

export default ImageDisplay;
export { ICurrentImage };
