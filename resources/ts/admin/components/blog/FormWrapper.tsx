import React from 'react';
import { Form, Formik, FormikProps } from 'formik';

import * as Yup from 'yup';

type TFormikProps = React.ComponentProps<typeof Formik<IFormikValues>>;

interface IFormikValues {
    title: string;
    content: string;
    summary: string;
    summary_auto_generate: boolean;
    slug: string;
    slug_auto_generate: boolean;
}

export interface IArticleFormValues extends IFormikValues {

}

interface IProps extends TFormikProps {

}

const FormWrapper = React.forwardRef<FormikProps<IFormikValues>, IProps>(({ children, ...props }, ref) => {
    const formikRef = React.useRef<FormikProps<IFormikValues> | null>();

    const schema =
        React.useMemo(
            () => Yup.object().shape({
                title: Yup.string().required('Title is required').max(255),
                content: Yup.string().required('Content is required'),
                summary: Yup.string().when('summary_auto_generate', {
                    is: false,
                    then: (schema) => schema.required('Summary is required.'),
                    otherwise: (schema) => schema.optional()
                }),
                summary_auto_generate: Yup.boolean(),
                slug: Yup.string().required('Slug is required').matches(/^[a-z][a-z\d]*(-[a-z\d]+)*$/i),
                slug_auto_generate: Yup.boolean()
            }),
            []
        );

    return (
        <>
            <Formik<IFormikValues>
                innerRef={(instance) => formikRef.current = React.assignRef(ref, instance)}
                validationSchema={schema}
                {...props}
            >
                {(formikProps) => (
                    <Form>
                        {typeof children === 'function' ? children(formikProps) : children}
                    </Form>
                )}

            </Formik>
        </>
    );
});

FormWrapper.displayName = 'FormWrapper';

export default FormWrapper;
