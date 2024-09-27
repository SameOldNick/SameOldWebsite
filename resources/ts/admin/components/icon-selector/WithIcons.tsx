import React from 'react';
import { getAllIcons as getAllIconsFromFile, IIconsFile, IIconType, loadIconsFromFile, lookupIcon as lookupIconFromFile } from './utils';

import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

export interface IHasIconsFile {
    iconsFile: IIconsFile;
    lookupIcon: (icon: string) => IIconType | undefined;
    getAllIcons: () => IIconType[];
}

type TFallback = React.ReactNode | ((err: unknown) => React.ReactNode);

export function withIconsFile<TProps extends IHasIconsFile = IHasIconsFile>(
    Component: React.ComponentType<TProps>, 
    fallback: TFallback = null,
    loading: React.ReactNode = <Loader display={{ type: 'over-element' }} />
) {
    const element: React.FC<Omit<TProps, keyof IHasIconsFile>> = (props) => {
        const loadIcons = React.useCallback(async () => {
            return loadIconsFromFile();
        }, []);

        const lookupIcon = React.useCallback((file: IIconsFile, icon: string) => lookupIconFromFile(file, icon), []);
        const getAllIcons = React.useCallback((file: IIconsFile) => getAllIconsFromFile(file), []);

        const fallbackFunc = React.useCallback(typeof fallback !== 'function' ? () => fallback : fallback, [fallback]);

        return (
            <>
                <WaitToLoad loading={loading} callback={loadIcons}>
                    {(file, err) => (
                        <>
                            {file && <Component 
                                {...(props as TProps)} 
                                iconsFile={file} 
                                lookupIcon={(icon) => lookupIcon(file, icon)} 
                                getAllIcons={() => getAllIcons(file)}  
                            />}
                            {err && fallbackFunc(err)}
                        </>
                    )}
                </WaitToLoad>

            </>
        );
    }

    element.displayName = `withIconsFile(${Component.displayName || Component.name})`

    return element;
}