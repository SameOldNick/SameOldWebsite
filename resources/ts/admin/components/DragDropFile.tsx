import React from 'react';
import { Button } from 'reactstrap';
import { FaUpload } from 'react-icons/fa';

import { humanReadableFileSize } from '@admin/utils';

type TListenerCallback<TEvent extends Event, TThis extends HTMLElement = HTMLElement> = (this: TThis, ev: TEvent) => any;
type TEventListenerMappings = PartialRecord<keyof HTMLElementEventMap, TListenerCallback<DragEvent, HTMLDivElement>>

interface IBaseProps {
    multiple: boolean;
    onFileSelected: (file: File) => void;
    onFileRemoved?: (file: File) => void;
    accept?: string;
    allowRemoval: boolean;
}

type TProps = React.PropsWithChildren<IBaseProps>;

interface IDropZoneStateBasic {
    state: 'empty' | 'dragging' | 'dropped';
}

interface IDropZoneStateError {
    state: 'error';
    message: string;
}

interface IState {
    dropZoneState: IDropZoneStateBasic | IDropZoneStateError;
    files: File[];
}

export default class DragDropFile extends React.Component<TProps, IState> {
    static defaultProps = {
        allowRemoval: true
    };

    private readonly fileInputRef: React.RefObject<HTMLInputElement>;
    private readonly dropRef: React.RefObject<HTMLDivElement>;
    private dragCounter: number = 0;

    constructor(props: Readonly<TProps>) {
        super(props);

        this.state = {
            dropZoneState: { state: 'empty' },
            files: []
        };

        this.fileInputRef = React.createRef();
        this.dropRef = React.createRef();

        this.reset = this.reset.bind(this);
        this.handleDrag = this.handleDrag.bind(this);
        this.handleDragIn = this.handleDragIn.bind(this);
        this.handleDragOut = this.handleDragOut.bind(this);
        this.handleDrop = this.handleDrop.bind(this);
        this.handleFileInputChange = this.handleFileInputChange.bind(this);
        this.browseFile = this.browseFile.bind(this);
        this.handleFileUpload = this.handleFileUpload.bind(this);
    }

    public componentDidMount() {
        if (this.dropRef.current !== null) {
            for (const [type, listener] of Object.entries(this.eventListenerMappings)) {
                this.dropRef.current.addEventListener(type, listener as TListenerCallback<Event>);
            }
        }
    }

    public componentWillUnmount() {
        if (this.dropRef.current !== null) {
            for (const [type, listener] of Object.entries(this.eventListenerMappings)) {
                this.dropRef.current.removeEventListener(type, listener as TListenerCallback<Event>);
            }
        }
    }

    /**
     * Resets the drop zone.
     *
     * @memberof DragDropFile
     */
    public reset() {
        this.dragCounter = 0;

        this.setState({
            dropZoneState: { state: 'empty' },
            files: []
        });
    }

    /**
     * Gets the event listener mappings.
     *
     * @readonly
     * @private
     * @type {TEventListenerMappings}
     * @memberof DragDropFile
     */
    private get eventListenerMappings(): TEventListenerMappings {
        return {
            'dragenter': this.handleDragIn,
            'dragleave': this.handleDragOut,
            'dragover': this.handleDrag,
            'drop': this.handleDrop
        };
    }

    /**
     * Handles a drag
     *
     * @private
     * @param {DragEvent} e
     * @memberof DragDropFile
     */
    private handleDrag(e: DragEvent) {
        e.preventDefault();
        e.stopPropagation();
    }

    /**
     * Handles a file being dragged in
     *
     * @private
     * @param {DragEvent} e
     * @memberof DragDropFile
     */
    private handleDragIn(e: DragEvent) {
        e.preventDefault();
        e.stopPropagation();

        // Only file drags are allowed and items must be present.
        // TODO: Make sure dropped file is acceptable type.
        if (e.dataTransfer?.dropEffect !== 'copy' || e.dataTransfer?.items === undefined || e.dataTransfer.items.length === 0) {
            this.setState({
                dropZoneState: {
                    state: 'error',
                    message: 'Only files can be dropped.'
                }
            });
        } else {
            this.dragCounter++;

            this.setState({ dropZoneState: { state: 'dragging' } })
        }
    }

    /**
     * Handles a file being dragged out.
     *
     * @private
     * @param {DragEvent} e
     * @memberof DragDropFile
     */
    private handleDragOut(e: DragEvent) {
        e.preventDefault();
        e.stopPropagation();

        this.dragCounter--;

        if (this.dragCounter === 0) {
            this.setState({ dropZoneState: { state: 'empty' } });
        }
    }

    /**
     * Handles file(s) being dropped.
     *
     * @private
     * @param {DragEvent} e
     * @memberof DragDropFile
     */
    private handleDrop(e: DragEvent) {
        e.preventDefault();
        e.stopPropagation();

        if (e.dataTransfer?.dropEffect === 'copy' && e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
            this.handleFileUpload(e.dataTransfer.files);

            this.dragCounter = 0;

            e.dataTransfer.clearData();
        }
    }

    /**
     * Handles file(s) being selected by file input.
     *
     * @private
     * @param {React.FormEvent<HTMLInputElement>} e
     * @memberof DragDropFile
     */
    private handleFileInputChange(e: React.FormEvent<HTMLInputElement>) {
        const { files } = e.currentTarget;

        if (files) {
            this.handleFileUpload(files);
        }
    }

    /**
     * Handles file upload.
     * This called after file is dropped or selected from file browser.
     *
     * @private
     * @param {FileList} fileList
     * @memberof DragDropFile
     */
    private handleFileUpload(fileList: FileList) {
        const { multiple, onFileSelected } = this.props;

        // Transform FileList into File array
        const files: File[] = [];

        for (let i = 0; i < fileList.length; i++) {
            // Don't add more than 1 if multiple is false.
            if (i > 0 && !multiple)
                break;

            const file = fileList[i];

            onFileSelected(file);
            files.push(file);
        }

        this.setState({ dropZoneState: { state: 'dropped' }, files })
    }

    /**
     * Displays browse file window to user.
     *
     * @private
     * @param {React.MouseEvent<HTMLAnchorElement>} e
     * @memberof DragDropFile
     */
    private browseFile(e: React.MouseEvent<HTMLAnchorElement>) {
        e.preventDefault();

        this.fileInputRef.current?.click();
    }

    /**
     * Removes file at index from files.
     * This is called when the user clicks the X beside a file.
     * @param toRemove Index to remove from
     * @returns void
     */
    private removeFile(toRemove: number) {
        const { allowRemoval, onFileRemoved } = this.props;
        const { files } = this.state;

        if (!allowRemoval)
            return;

        if (onFileRemoved)
            onFileRemoved(files[toRemove]);

        if (files.length === 1) {
            this.reset();
        } else {
            this.setState({ files: files.filter((file, i) => i !== toRemove) });
        }
    }

    /**
     * Gets the image/icon to use in drop zone
     *
     * @readonly
     * @private
     * @memberof DragDropFile
     */
    private get image() {
        const { children } = this.props;

        return children ?? <FaUpload size='150px' />;
    }

    /**
     * Gets the contents fo the drop zone
     *
     * @readonly
     * @private
     * @memberof DragDropFile
     */
    private get dropZoneContents() {
        const { accept, multiple, allowRemoval } = this.props;
        const { dropZoneState, files } = this.state;

        switch (dropZoneState.state) {
            case 'dropped': {
                return (
                    <>
                        <div>{this.image}</div>
                        <ul className='mt-3 list-unstyled'>
                            {files.map((file, i) => (
                                <li key={i}>
                                    {file.name} ({humanReadableFileSize(file.size)})
                                    {allowRemoval && <Button close className='float-end' onClick={() => this.removeFile(i)} />}
                                </li>
                            ))}
                        </ul>
                    </>
                );
            }

            case 'error': {
                return (
                    <p>{dropZoneState.message} <a href='#' onClick={this.reset}>Click here</a> to try again.</p>
                );
            }

            default: {
                return (
                    <>
                        <input className='d-none' ref={this.fileInputRef} type='file' accept={accept} multiple={multiple} onChange={this.handleFileInputChange} />
                        <div>{this.image}</div>

                        <a href='#' onClick={this.browseFile}>Browse</a> or drop {multiple === true ? 'files' : 'file'} here.
                    </>
                );
            }
        }
    }

    /**
     * Gets the styles for the drop zone.
     *
     * @readonly
     * @private
     * @memberof DragDropFile
     */
    private get dropZoneStyles() {
        const { dropZoneState: { state } } = this.state;

        const styles: React.CSSProperties = { border: '2px dashed #aaa' };

        switch (state) {
            case 'dragging':
                styles.background = 'rgb(232, 248, 255)';
                break;

            case 'error':
                styles.background = 'rgb(248, 215, 218)';
                break;

            default:
                break;
        }

        return styles;
    }

    public render() {
        return (
            <>
                <div
                    ref={this.dropRef}
                    className="upload-drop-zone d-flex align-items-center justify-content-center"
                    style={this.dropZoneStyles}
                >
                    <div className='py-4 text-center'>
                        {this.dropZoneContents}
                    </div>
                </div>
            </>
        );
    }
}
